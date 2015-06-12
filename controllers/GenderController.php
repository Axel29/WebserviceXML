<?php
/**
 * @todo Vérifier que l'ID du jeu existe avant d'insérer ou de mettre à jour.
 */
class GenderController extends CRUD
{
	/**
	 * Show the full genders list or a specific gender by it's ID
	 *
	 * @param $id int Gender's ID
	 */
	public function show()
	{
		$genderModel = new Gender();
		$page        = $this->getPage() ? $this->getPage() : 1;

		// Show the full gender list or a specific gender by it's ID
		$datas = $genderModel->findBy('idGender', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'genders.xsd')) {
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
	 * Add new gender
	 * Route: /gender
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Gender::getRequiredFields(), $_POST);

		$genderModel    = new Gender();
		$insertedGender = $genderModel->insertGender($_POST);

		if ($insertedGender) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update gender
	 * Route: /gender/index/id/{id}
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
		$requiredFields = [
			'gender' => 'string',
		];

		$this->checkRequiredFields(Gender::getRequiredFields(), $_PUT);

		$genderModel  = new Gender();
		$updateGender = $genderModel->updateGender($this->getId(), $_PUT);

		if ($updateGender) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete gender. Forbiddent action.
	 * Route: /gender/index/id/{id}
	 *
	 * @param $id int Gender's ID to delete
	 */
	public function delete()
	{		
		$this->exitError(405, 'Genders deletion is not allowed.');
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one gender's XML.
	 *
	 * @param array $gender Gender to insert in the XML
	 * @return SimpleXMLElement $genders List of genders or gender
	 */
	public function generateXml($genders = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><genders/>');
		foreach ($genders as $gender) {
			$genderNode = $list->addChild('gender', $gender['gender']);
			$genderNode->addAttribute('id', $gender['idGender']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Config');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}