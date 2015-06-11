<?php
class ConfigController extends CRUD
{
	/**
	 * Show the full configs list or a specific config by it's ID
	 *
	 * @param $id int Config's ID
	 */
	public function show()
	{
		$configModel = new Config();
		$page        = $this->getPage() ? $this->getPage() : 1;

		// Show the full config list or a specific config by it's ID
		$datas = $configModel->findBy('idConfig', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'configs.xsd')) {
				$this->exitError(400, $errors);
			} else {
				$this->loadLayout('xml');
				echo $this->xml;
			}
		} else {
			if ($this->getId()) {
				$this->exitError(400, "The ID you specified can't be found.");
			} else {
				$this->exitError(400, "The page you specified doesn't exist.");
			}
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
			$this->exitError(400, "The 'id' must be specified.");
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
			$this->exitError(400, "The 'id' must be specified.");
		}

		$configModel   = new Config();

		// Check that there is at least one test left before deleting, otherwise, the XML would be broken.
		if ($configModel->getNumberOfConfigsLeft($this->getId()) > 1) {
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

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Config');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}