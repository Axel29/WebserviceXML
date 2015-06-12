<?php
class ShopController extends CRUD
{
	/**
	 * Show the full shops list or a specific shop by it's ID
	 *
	 * @param $id int Shop's ID
	 */
	public function show()
	{
		$shopModel = new Shop();
		$page      = $this->getPage() ? $this->getPage() : 1;

		// Show the full shop list or a specific shop by it's ID
		$datas = $shopModel->findBy('idShop', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'shops.xsd')) {
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
	 * Add new shop
	 * Route: /shop
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Shop::getRequiredFields(), $_POST);

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
	 * Route: /shop/index/id/{id}
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
		$this->checkRequiredFields(Shop::getRequiredFields(), $_PUT);

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
	 * Route: /shop/index/id/{id}
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
			$this->exitError(400, "The 'id' must be specified.");
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

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Shop');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}