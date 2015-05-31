<?php
class DlcController extends BaseController
{
	/**
	 * @var $id int Dlc's ID
	 */
	private $id = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /dlc/index/id/{id}
	 *
	 * @param $id int ID of the dlc. Used for POST, PUT and DELETE methods
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
	 * Show the full dlcs list or a specific dlc by it's ID
	 *
	 * @param $id int Dlc's ID
	 */
	public function show()
	{
		$dlcModel = new Dlc();

		// Show the full dlc list or a specific dlc by it's ID
		$datas = $dlcModel->findBy('idDlc', $this->getId());

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
		}
	}

	/**
	 * Add new dlc
	 * Route: /dlc
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Dlc::getRequiredFields(), $_POST);

		$dlcModel    = new Dlc();
		$insertedDlc = $dlcModel->insertDlc($_POST);

		if ($insertedDlc) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update dlc
	 * Route: /dlc/index/id/{id}
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
		$this->checkRequiredFields(Dlc::getRequiredFields(), $_PUT);

		$dlcModel  = new Dlc();
		$updatedDlc = $dlcModel->updateDlc($this->getId(), $_PUT);

		if ($updatedDlc) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete dlc
	 * Route: /dlc/index/id/{id}
	 *
	 * @param $id int Dlc's ID to delete
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

		$dlcModel   = new Dlc();		
		$deletedDlc = $dlcModel->deleteDlc($this->getId());

        if ($deletedDlc) {
            $this->sendStatus(204);
        } else {
            $this->exitError(400, 'An error has occured. Please try again.');
        }
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one dlc's XML.
	 *
	 * @param array $dlc Dlc to insert in the XML
	 * @return SimpleXMLElement $dlcs List of dlcs or dlc
	 */
	public function generateXml($dlcs = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><dlcs/>');
		foreach ($dlcs as $dlc) {
			$dlcNode = $list->addChild('dlc');
			$dlcNode->addAttribute('id', $dlc['idDlc']);

			$dlcNode->addChild('title', $dlc['title']);
			$dlcNode->addChild('description', $dlc['description']);

			$priceNode = $dlcNode->addChild('price', $dlc['price']);
			$priceNode->addAttribute('devise', $dlc['devise']);
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'dlcs.xsd')) {
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