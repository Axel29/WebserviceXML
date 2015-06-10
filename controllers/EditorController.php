<?php
class EditorController extends BaseController
{
	/**
	 * @var $id int Editor's ID
	 */
	private $id = null;

	/**
	 * @var $page int Editor's page
	 */
	private $page = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /editor/index/id/{id}
	 *
	 * @param $id int ID of the editor. Used for POST, PUT and DELETE methods
	 */
	public function indexAction($id = null, $page = null)
	{
		if ($id) {
			$this->setId($id);
		}

		if ($page) {
			$this->setPage($page);
		}

		switch ($this->getRequestMethod()) {
			case 'GET':
				$this->show();
				break;
			case 'POST':
				$this->add();
				break;
			case 'PUT':
				$this->update();
				break;
			case 'DELETE':
				$this->delete();
				break;
			
			default:
				$this->show();
				break;
		}
	}

	/**
	 * Show the full editors list or a specific editor by it's ID
	 *
	 * @param $id int Editor's ID
	 */
	public function show()
	{
		$editorModel = new Editor();
		$page = $this->getPage();

		// Show the full editor list or a specific editor by it's ID
		$datas = $editorModel->findBy('idEditor', $this->getId(), $page);

		if ($this->getId() && !$datas) {
			$this->exitError(400, "This editor doesn't exist.");
		}

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
		}
	}

	/**
	 * Add new editor
	 * Route: /editor
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Editor::getRequiredFields(), $_POST);

		$editorModel    = new Editor();
		$insertedEditor = $editorModel->insertEditor($_POST);

		if ($insertedEditor) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update editor
	 * Route: /editor/index/id/{id}
	 */
	public function update()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'PUT') {
			$this->exitError(405, 'Only PUT methods are allowed.');
			return;
		}

		// Get PUT datas
		parse_str(file_get_contents("php://input"), $_PUT);
		if (!$this->getId()) {
			$this->exitError(400, "The 'id' must be specified.");
		}

		// Check every required field
		$this->checkRequiredFields(Editor::getRequiredFields(), $_PUT);

		$editorModel  = new Editor();
		$updateEditor = $editorModel->updateEditor($this->getId(), $_PUT);

		if ($updateEditor) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete editor. Forbiddent action.
	 * Route: /editor/index/id/{id}
	 *
	 * @param $id int Editor's ID to delete
	 */
	public function delete()
	{		
		$this->exitError(405, 'Editors deletion is not allowed.');
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one editor's XML.
	 *
	 * @param array $editor Editor to insert in the XML
	 * @return SimpleXMLElement $editors List of editors or editor
	 */
	public function generateXml($editors = [])
	{
		$routing  = new Routing();
		$route    = $routing->parseUri();
		$page     = isset($route['args']['page']) ? $route['args']['page'] : null;

		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><editors/>');
		foreach ($editors as $editor) {
			$editorNode = $list->addChild('editor', $editor['editor']);
			$editorNode->addAttribute('id', $editor['idEditor']);
		}

		if ($page) {
			$prevPage = $page - 1;
			$nextPage = $page + 1;

			$editorModel = new Editor();
			$prevPageExists = false;
			$nextPageExists = $editorModel->pageExists($nextPage);

			if ($page > 1) {
				$prevPageExists = $editorModel->pageExists($prevPage);
				$urlPrev = BASE_URL . '/' . $_GET['p'];
				$urlPrev = preg_replace('/(page\/\d+)/', 'page/' . $prevPage, $urlPrev);
			} else {
				$urlPrev = '';
			}

			if ($nextPageExists) {
				$urlNext = BASE_URL . '/' . $_GET['p'];
				$urlNext = preg_replace('/(page\/\d+)/', 'page/' . $nextPage, $urlNext);
			} else {
				$urlNext = '';
			}

			$list->addChild('prev', $urlPrev);
			$list->addChild('next', $urlNext);
		}
		$this->loadLayout('xml');
		echo($list->asXML());
		die;
		return $list;
	}

	/**
	 * Validate XML from XSD
	 *
	 * @param $xml SimpleXMLElement XML to validate
	 * @return $result string Errors to display or empty string
	 */
	public function validateXML($xml)
	{
		// Enable user error handling
		libxml_use_internal_errors(true);

		$domDocument = new DOMDocument();
		$domDocument->loadXML($xml);

		$result = '';

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'editors.xsd')) {
			$errors = libxml_get_errors();

			foreach ($errors as $error) {
				$result = "<br>\n";
				switch ($error->level) {
					case LIBXML_ERR_WARNING:
						$result .= "<strong>Warning $error->code</strong>: ";
					break;
					case LIBXML_ERR_ERROR:
						$result .= "<strong>Error $error->code</strong>: ";
					break;
					case LIBXML_ERR_FATAL:
						$result .= "<strong>Fatal Error $error->code</strong>: ";
					break;
				}

				$result .= trim($error->message);

				if ($error->file) {
					$result .= " in <strong>$error->file</strong>";
				}
				$result .= " on line <strong>$error->line</strong>\n";
			}
			libxml_clear_errors();
		}

		return $result;
	}

	/**
	 * Get the ID
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set the ID
	 *
	 * @param $id int
	 */
	public function setId($id)
	{
		if ($this->isInt($id)) {
			$this->id = $id;
		} else {
			$this->exitError(400, sprintf('The ID must be an integer. %s given', gettype($id)));
		}
	}

	/**
	 * Get the current page
	 *
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * Set the current page
	 *
	 * @param int $page Current page
	 */
	public function setPage($page)
	{
		if ($this->isInt($page)) {
			$this->page = $page;
		} else {
			$this->exitError(400, sprintf('The page must be an integer. %s given', gettype($page)));
		}
	}
}