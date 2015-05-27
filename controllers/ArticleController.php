<?php
class ArticleController extends BaseController
{
	/**
	 * @var $id int Article's ID
	 */
	private $id = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /article/index/id/{id}
	 *
	 * @param $id int ID of the article. Used for POST, PUT and DELETE methods
	 */
	public function indexAction($id = null)
	{
		if ($id) {
			$this->setId($id);
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
	 * Show the full articles list or a specific article by it's ID
	 *
	 * @param $id int Article's ID
	 */
	public function show()
	{
		$articleModel = new Article();

		// Show the full article list or a specific article by it's ID
		$datas = $articleModel->findBy('idArticle', $this->getId());

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
		}
	}

	/**
	 * Add new article
	 * Route: /article
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$requiredFields = [
			'type'          => 'string',
			'title'         => 'string',
			'user_name'     => 'string',
			'date'          => 'date',
			'console_names' => 'string',
			'game_idGame'   => 'int',
		];

		$this->checkRequiredFields($requiredFields, $_POST);

		$articleModel    = new Article();
		$insertedArticle = $articleModel->insertArticle($_POST);

		if ($insertedArticle) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update article
	 * Route: /article/index/id/{id}
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
			$this->exitError(400, "'id' must be specified.");
		}

		// Check every required field
		$requiredFields = [
			'type'          => 'string',
			'title'         => 'string',
			'user_name'     => 'string',
			'date'          => 'date',
			'console_names' => 'string',
		];

		$this->checkRequiredFields($requiredFields, $_PUT);

		$articleModel  = new Article();
		$updateArticle = $articleModel->updateArticle($this->getId(), $_PUT);

		if ($updateArticle) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete article.
	 * Route: /article/index/id/{id}
	 *
	 * @param $id int Article's ID to delete
	 */
	public function delete()
	{		
		// Security check for the request method
		if (!$this->getRequestMethod() == 'DELETE') {
			$this->exitError(405, 'Only DELETE methods are allowed.');
			return;
		}

		if (!$this->getId()) {
			$this->exitError(400, "'id' must be specified.");
		}

		$articleModel   = new Article();
		$deletedArticle = $articleModel->deleteArticle($this->getId());

		if ($deletedArticle) {
			$this->sendStatus(204);
		} else {
			$this->exitError(400, 'An error has occured. Please try again.');
		}
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one article's XML.
	 *
	 * @param array $article Article to insert in the XML
	 * @return SimpleXMLElement $articles List of articles or article
	 */
	public function generateXml($articles = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><articles/>');
		foreach ($articles as $article) {
			$articleNode = $list->addChild('article');
			$articleNode->addAttribute('id', $article['idArticle']);
			$articleNode->addAttribute('type', $article['type']);

			$consoleNamesNode = $articleNode->addChild('consolesNames');
			foreach (explode(',', $article['console_names']) as $consoleName) {
				$consoleNamesNode->addChild('consoleName', $consoleName);
			}

			$articleNode->addChild('title', $article['title']);
			$articleNode->addChild('userName', $article['user_name']);
			$articleNode->addChild('date', $article['date']);
		}
		// $this->loadLayout('xml');
		// echo($list->asXML());
		// die;
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'articles.xsd')) {
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
}