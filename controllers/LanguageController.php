<?php
class LanguageController extends CRUD
{
	/**
	 * Show the full languages list or a specific language by it's ID
	 *
	 * @param $id int Language's ID
	 */
	public function show()
	{
		$languageModel = new Language();
		$page          = $this->getPage() ? $this->getPage() : 1;

		// Show the full language list or a specific language by it's ID
		$datas = $languageModel->findBy('idLanguage', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'languages.xsd')) {
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
	 * Add new language
	 * Route: /language
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Language::getRequiredFields(), $_POST);

		$languageModel    = new Language();
		$insertedLanguage = $languageModel->insertLanguage($_POST);

		if ($insertedLanguage) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update language
	 * Route: /language/index/id/{id}
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
			'language' => 'string',
		];

		$this->checkRequiredFields(Language::getRequiredFields(), $_PUT);

		$languageModel  = new Language();
		$updateLanguage = $languageModel->updateLanguage($this->getId(), $_PUT);

		if ($updateLanguage) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete language. Forbiddent action.
	 * Route: /language/index/id/{id}
	 *
	 * @param $id int Language's ID to delete
	 */
	public function delete()
	{		
		$this->exitError(405, 'Languages deletion is not allowed.');
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one language's XML.
	 *
	 * @param array $language Language to insert in the XML
	 * @return SimpleXMLElement $languages List of languages or language
	 */
	public function generateXml($languages = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><languages/>');
		foreach ($languages as $language) {
			$languageNode = $list->addChild('language', $language['language']);
			$languageNode->addAttribute('id', $language['idLanguage']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Language');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}