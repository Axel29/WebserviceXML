<?php
/**
 * @todo Vérifier que l'ID du jeu existe avant d'insérer ou de mettre à jour.
 */
class ThemeController extends BaseController
{
	/**
	 * @var $id int Theme's ID
	 */
	private $id = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /theme/index/id/{id}
	 *
	 * @param $id int ID of the theme. Used for POST, PUT and DELETE methods
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
	 * Show the full themes list or a specific theme by it's ID
	 *
	 * @param $id int Theme's ID
	 */
	public function show()
	{
		$themeModel = new Theme();

		// Show the full theme list or a specific theme by it's ID
		$datas = $themeModel->findBy('idTheme', $this->getId());

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml)) {
				$this->exitError(400, $errors);
			} else {
				$this->loadLayout('xml');
				echo $this->xml;
			}
		} else {
			$this->exitError(400, "This theme doesn't exist.");
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
			$this->exitError(400, "'id' must be specified.");
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'themes.xsd')) {
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