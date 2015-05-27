<?php
/**
 * @todo Vérifier que l'ID du test existe avant d'insérer ou de mettre à jour.
 */
class AnalyseController extends BaseController
{
	/**
	 * @var $id int Analyse's ID
	 */
	private $id = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /analyse/index/id/{id}
	 *
	 * @param $id int ID of the analyse. Used for POST, PUT and DELETE methods
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
	 * Show the full analyses list or a specific analyse by it's ID
	 *
	 * @param $id int Analyse's ID
	 */
	public function show()
	{
		$analyseModel = new Analyse();

		// Show the full analyse list or a specific analyse by it's ID
		$datas = $analyseModel->findBy('idAnalyse', $this->getId());

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
		}
	}

	/**
	 * Add new analyse
	 * Route: /analyse
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
			'analyse'     => 'string',
			'type'        => 'string',
			'test_idTest' => 'int',
		];

		$this->checkRequiredFields($requiredFields, $_POST);

		$analyseModel    = new Analyse();
		$insertedAnalyse = $analyseModel->insertAnalyse($_POST);

		if ($insertedAnalyse) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update analyse
	 * Route: /analyse/index/id/{id}
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
			'analyse'     => 'string',
			'type'        => 'string',
			'test_idTest' => 'int',
		];

		$this->checkRequiredFields($requiredFields, $_PUT);

		$analyseModel  = new Analyse();
		$updatedAnalyse = $analyseModel->updateAnalyse($this->getId(), $_PUT);

		if ($updatedAnalyse) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete analyse
	 * Route: /analyse/index/id/{id}
	 *
	 * @param $id int Analyse's ID to delete
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

		$analyseModel   = new Analyse();
		$deletedAnalyse = $analyseModel->deleteAnalyse($this->getId());

		if ($deletedAnalyse) {
			$this->sendStatus(204);
		} else {
			$this->exitError(400, 'An error has occured. Please try again.');
		}
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one analyse's XML.
	 *
	 * @param array $analyse Analyse to insert in the XML
	 * @return SimpleXMLElement $analyses List of analyses or analyse
	 */
	public function generateXml($analyses = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><analyses/>');
		foreach ($analyses as $analyse) {
			$analyseNode = $list->addChild('analyse', $analyse['analyse']);
			$analyseNode->addAttribute('id', $analyse['idAnalyse']);
			$analyseNode->addAttribute('type', $analyse['type']);
		}
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'analyses.xsd')) {
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
			$this->exitError(400, sprintf("'id' must be an integer, %s given", gettype($id)));
		}
	}
}