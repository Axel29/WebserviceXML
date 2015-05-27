<?php
class GenderController extends BaseController
{
	/**
	 * @var $id int Gender's ID
	 */
	private $id = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /gender/index/id/{id}
	 *
	 * @param $id int ID of the gender. Used for POST, PUT and DELETE methods
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
	 * Show the full genders list or a specific gender by it's ID
	 *
	 * @param $id int Gender's ID
	 */
	public function show()
	{
		$genderModel = new Gender();

		// Show the full gender list or a specific gender by it's ID
		$datas = $genderModel->findBy('idGender', $this->getId());

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
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
		$requiredFields = [
			'gender' => 'string',
		];

		$this->checkRequiredFields($requiredFields, $_POST);

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
			$this->exitError(400, "'id' must be specified.");
		}

		// Check every required field
		$requiredFields = [
			'analyse'     => 'string',
			'type'        => 'string',
			'test_idTest' => 'int',
		];

		$this->checkRequiredFields($requiredFields, $_PUT);

		if (!isset($_PUT['gender'])) {
			$this->exitError(400, "'gender' must be specified.");
		}
		if (!is_string($_PUT['gender'])) {
			$this->exitError(400, "'gender' must be a valid string.");
		}

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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'genders.xsd')) {
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