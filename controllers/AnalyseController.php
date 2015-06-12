<?php
class AnalyseController extends CRUD
{
	/**
	 * Show the full analyses list or a specific analyse by it's ID
	 *
	 * @param $id int Analyse's ID
	 */
	public function show()
	{
		$analyseModel = new Analyse();
		$page         = $this->getPage() ? $this->getPage() : 1;

		// Show the full analyse list or a specific analyse by it's ID
		$datas = $analyseModel->findBy('idAnalyse', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'analyses.xsd')) {
				$this->exitError(400, $errors);
			} else {
				$this->loadLayout('xml');
				echo $this->xml;
			}
		} else {
			if ($this->getId()) {
				$this->exitError(404, "The ID you specified can't be found.");
			} else {
				$this->exitError(404, "The page you specified doesn't exist.");
			}
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
		$this->checkRequiredFields(Analyse::getRequiredFields(), $_POST);

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
			$this->exitError(400, "The 'id' must be specified.");
		}

		// Check every required field
		$this->checkRequiredFields(Analyse::getRequiredFields(), $_PUT);

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
			$this->exitError(400, "The 'id' must be specified.");
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

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Analyse');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}