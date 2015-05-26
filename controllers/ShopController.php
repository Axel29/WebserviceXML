<?php
/**
 * @todo Vérifier que l'ID de l'édition existe avant d'insérer ou de mettre à jour.
 */
class ShopController extends BaseController
{
	/**
	 * @var $id int Shop's ID
	 */
	private $id = null;

	/**
	 * @var $requiredFields array Required fields and their types for insert / update
	 */
	private $requiredFields = [
		'url'               => 'string',
		'name'              => 'string',
		'price'             => 'float',
		'devise'            => 'string',
		'edition_idEdition' => 'int',
	];

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /shop/id/{id}
	 *
	 * @param $id int ID of the shop. Used for POST, PUT and DELETE methods
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
	 * Show the full shops list or a specific shop by it's ID
	 *
	 * @param $id int Shop's ID
	 */
	public function show()
	{
		$shopModel = new Shop();

		// Show the full shop list or a specific shop by it's ID
		$datas = $shopModel->findBy('idShop', $this->getId());

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
		}
	}

	/**
	 * Add new shop
	 * Route: /shop/add
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

		$shopModel    = new Shop();
		$insertedShop = $shopModel->insertShop($_POST);

		if ($insertedShop) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update shop
	 * Route: /shop/id/{id}
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

		$shopModel  = new Shop();
		$updatedShop = $shopModel->updateShop($this->getId(), $_PUT);

		if ($updatedShop) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete shop
	 * Route: /shop/delete/id/{id}
	 *
	 * @param $id int Shop's ID to delete
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

		$shopModel   = new Shop();		
		$deletedShop = $shopModel->deleteShop($this->getId());

        if ($deletedShop) {
            $this->sendStatus(204);
        } else {
            $this->exitError(400, 'An error has occured. Please try again.');
        }
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one shop's XML.
	 *
	 * @param array $shop Shop to insert in the XML
	 * @return SimpleXMLElement $shops List of shops or shop
	 */
	public function generateXml($shops = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><shops/>');
		foreach ($shops as $shop) {
			$shopNode = $list->addChild('shop');
			$shopNode->addAttribute('id', $shop['idShop']);
			$shopNode->addAttribute('url', $shop['url']);

			$shopNode->addChild('name', $shop['name']);

			$priceNode = $shopNode->addChild('price', $shop['price']);
			$priceNode->addAttribute('devise', $shop['devise']);
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'shops.xsd')) {
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