<?php
class EditionController extends CRUD
{
	/**
	 * Show the full editions list or a specific edition by it's ID
	 *
	 * @param $id int Edition's ID
	 */
	public function show()
	{
		$editionModel = new Edition();
		$page         = $this->getPage() ? $this->getPage() : 1;

		// Show the full edition list or a specific edition by it's ID
		$datas = $editionModel->findBy('idEdition', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'editions.xsd')) {
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
		$this->checkRequiredFields(Edition::getRequiredFields(), $_POST);

		// Check every required fields for shops if neeeded
		if (isset($_POST['shops'])) {
			foreach ($_POST['shops'] as $shop) {
				$this->checkRequiredFields(Shop::getRequiredFields(), $shop);
			}
		}

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
			$this->exitError(400, "The 'id' must be specified.");
		}

		// Check every required field
		$this->checkRequiredFields(Edition::getRequiredFields(), $_PUT);

		// Check every required fields for shops if neeeded
		if (isset($_PUT['shops'])) {
			foreach ($_PUT['shops'] as $shop) {
				$this->checkRequiredFields(Shop::getRequiredFields(), $shop);
			}
		}

		$editionModel   = new Edition();
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
			$this->exitError(400, "The 'id' must be specified.");
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

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Edition');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}