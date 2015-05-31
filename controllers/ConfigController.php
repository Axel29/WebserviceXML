<?php
class ConfigController extends BaseController
{
	/**
	 * @var $id int Config's ID
	 */
	private $id = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /config/index/id/{id}
	 *
	 * @param $id int ID of the config. Used for POST, PUT and DELETE methods
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
	 * Show the full configs list or a specific config by it's ID
	 *
	 * @param $id int Config's ID
	 */
	public function show()
	{
		$configModel = new Config();

		// Show the full config list or a specific config by it's ID
		$datas = $configModel->findBy('idConfig', $this->getId());

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml)) {
				$this->exitError(400, $errors);
			} else {
				$this->loadLayout('xml');
				echo $this->xml;
			}
		} else {
			$this->exitError(400, "This configuration doesn't exist.");
		}
	}

	/**
	 * Add new config
	 * Route: /config
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Config::getRequiredFields(), $_POST);

		$configModel    = new Config();
		$insertedConfig = $configModel->insertConfig($_POST);

		if ($insertedConfig) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update config
	 * Route: /config/index/id/{id}
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
		$this->checkRequiredFields(Config::getRequiredFields(), $_PUT);

		$configModel  = new Config();
		$updatedConfig = $configModel->updateConfig($this->getId(), $_PUT);

		if ($updatedConfig) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete config
	 * Route: /config/index/id/{id}
	 *
	 * @param $id int Config's ID to delete
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

		$configModel   = new Config();

		// Check that there is at least one test left before deleting, otherwise, the XML would be broken.
		if ($testModel->getNumberOfTestsLeft($this->getId()) > 1) {
			$deletedConfig = $configModel->deleteConfig($this->getId());

			if ($deletedConfig) {
				$this->sendStatus(204);
			} else {
				$this->exitError(400, 'An error has occured. Please try again.');
			}
		} else {
			$this->exitError(400, 'There must be at least one config per console.');
		}
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one config's XML.
	 *
	 * @param array $config Config to insert in the XML
	 * @return SimpleXMLElement $configs List of configs or config
	 */
	public function generateXml($configs = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><configs/>');
		foreach ($configs as $config) {
			$configNode = $list->addChild('config', $config['config']);
			$configNode->addAttribute('id', $config['idConfig']);
			$configNode->addAttribute('type', $config['type']);
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

		if (
			($this->getId() && !$domDocument->schemaValidate(SCHEMAS_PATH . 'config.xsd')) || 
			(!$this->getId() && $domDocument->schemaValidate(SCHEMAS_PATH . 'configs.xsd'))
		) {
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