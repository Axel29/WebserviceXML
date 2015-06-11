<?php
/**
 * @todo Vérifier que l'ID du jeu existe avant d'insérer ou de mettre à jour.
 */
class ThemeController extends CRUD
{
	/**
	 * Show the full themes list or a specific theme by it's ID
	 *
	 * @param $id int Theme's ID
	 */
	public function show()
	{
		$themeModel = new Theme();
		$page       = $this->getPage() ? $this->getPage() : 1;

		// Show the full theme list or a specific theme by it's ID
		$datas = $themeModel->findBy('idTheme', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'themes.xsd')) {
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
	 * Add new theme
	 * Route: /theme
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Theme::getRequiredFields(), $_POST);

		$themeModel    = new Theme();
		$insertedTheme = $themeModel->insertTheme($_POST);

		if ($insertedTheme) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update theme
	 * Route: /theme/index/id/{id}
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
			'theme' => 'string',
		];

		$this->checkRequiredFields(Theme::getRequiredFields(), $_PUT);

		$themeModel  = new Theme();
		$updateTheme = $themeModel->updateTheme($this->getId(), $_PUT);

		if ($updateTheme) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete theme. Forbiddent action.
	 * Route: /theme/index/id/{id}
	 *
	 * @param $id int Theme's ID to delete
	 */
	public function delete()
	{		
		$this->exitError(405, 'Themes deletion is not allowed.');
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one theme's XML.
	 *
	 * @param array $theme Theme to insert in the XML
	 * @return SimpleXMLElement $themes List of themes or theme
	 */
	public function generateXml($themes = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><themes/>');
		foreach ($themes as $theme) {
			$themeNode = $list->addChild('theme', $theme['theme']);
			$themeNode->addAttribute('id', $theme['idTheme']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Theme');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}