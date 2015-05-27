<?php
/**
 * @todo Vérifier que l'ID de la console existe avant d'insérer ou de mettre à jour.
 */
class EditionController extends BaseController
{
	/**
	 * @var $id int Edition's ID
	 */
	private $id = null;

	/**
	 * @var $requiredFields array Required fields and their types for insert / update
	 */
	private $requiredFields = [
		'name'              => 'string',
		'content'           => 'string',
		'console_idConsole' => 'int',
	];

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /edition/index/id/{id}
	 *
	 * @param $id int ID of the edition. Used for POST, PUT and DELETE methods
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
	 * Show the full editions list or a specific edition by it's ID
	 *
	 * @param $id int Edition's ID
	 */
	public function show()
	{
		$editionModel = new Edition();

		// Show the full edition list or a specific edition by it's ID
		$datas = $editionModel->findBy('idEdition', $this->getId());

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
		}
	}

	/**
	 * Add new edition
	 * Route: /edition
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields($this->requiredFields, $_POST);

		$editionModel    = new Edition();
		$insertedEdition = $editionModel->insertEdition($_POST);

		if ($insertedEdition) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update edition
	 * Route: /edition/index/id/{id}
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
		$this->checkRequiredFields($this->requiredFields, $_PUT);

		$editionModel  = new Edition();
		$updatedEdition = $editionModel->updateEdition($this->getId(), $_PUT);

		if ($updatedEdition) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete edition
	 * Route: /edition/index/id/{id}
	 *
	 * @param $id int Edition's ID to delete
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

		$editionModel = new Edition();

        // Check that there is at least one edition left before deleting, otherwise, the XML would be broken.
        if ($editionModel->getNumberOfEditionsLeft($this->getId()) > 1) {
            $deletedEdition = $editionModel->deleteEdition($this->getId());

            if ($deletedEdition) {
                $this->sendStatus(204);
            } else {
                $this->exitError(400, 'An error has occured. Please try again.');
            }
        } else {
            $this->exitError(400, 'There must be at least one edition per console.');
        }
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one edition's XML.
	 *
	 * @param array $edition Edition to insert in the XML
	 * @return SimpleXMLElement $editions List of editions or edition
	 */
	public function generateXml($editions = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><editions/>');
		foreach ($editions as $edition) {
			$editionNode = $list->addChild('edition');
			$editionNode->addAttribute('id', $edition['idEdition']);
			$editionNode->addChild('name', $edition['name']);
			$editionNode->addChild('content', $edition['content']);

			$shops     = new Shop();
			$shopsNode = $editionNode->addChild('shops');
			if ($shops = $shops->findBy('edition_idEdition', $edition['idEdition'])) {
				foreach ($shops as $shop) {
					$shopNode = $shopsNode->addChild('shop');
					$shopNode->addAttribute('id', $shop['idShop']);
					$shopNode->addAttribute('url', $shop['url']);

					$shopNode->addChild('name', $shop['name']);

					$priceNode = $shopNode->addChild('price', $shop['price']);
					$priceNode->addAttribute('devise', $shop['devise']);
				}
			}
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'editions.xsd')) {
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