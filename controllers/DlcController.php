<?php
class DlcController extends CRUD
{
	/**
	 * Show the full dlcs list or a specific dlc by it's ID
	 *
	 * @param $id int Dlc's ID
	 */
	public function show()
	{
		$dlcModel = new Dlc();
		$page     = $this->getPage() ? $this->getPage() : 1;

		// Show the full dlc list or a specific dlc by it's ID
		$datas = $dlcModel->findBy('idDlc', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'dlcs.xsd')) {
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
			$this->exitError(400, "The 'id' must be specified.");
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
			$this->exitError(400, "The 'id' must be specified.");
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

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Dlc');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}