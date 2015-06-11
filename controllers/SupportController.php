<?php
class SupportController extends CRUD
{
	/**
	 * Show the full supports list or a specific support by it's ID
	 *
	 * @param $id int Support's ID
	 */
	public function show()
	{
		$supportModel = new Support();
		$page         = $this->getPage() ? $this->getPage() : 1;

		// Show the full support list or a specific support by it's ID
		$datas = $supportModel->findBy('idSupport', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'supports.xsd')) {
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
	 * Add new support
	 * Route: /support
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Support::getRequiredFields(), $_POST);

		$supportModel    = new Support();
		$insertedSupport = $supportModel->insertSupport($_POST);

		if ($insertedSupport) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update support
	 * Route: /support/index/id/{id}
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
		$this->checkRequiredFields(Support::getRequiredFields(), $_PUT);

		$supportModel   = new Support();
		$updatedSupport = $supportModel->updateSupport($this->getId(), $_PUT);

		if ($updatedSupport) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete support
	 * Route: /support/index/id/{id}
	 *
	 * @param $id int Support's ID to delete
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

		$supportModel   = new Support();		
		$deletedSupport = $supportModel->deleteSupport($this->getId());

        if ($deletedSupport) {
            $this->sendStatus(204);
        } else {
            $this->exitError(400, 'An error has occured. Please try again.');
        }
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one support's XML.
	 *
	 * @param array $support Support to insert in the XML
	 * @return SimpleXMLElement $supports List of supports or support
	 */
	public function generateXml($supports = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><supports/>');
		foreach ($supports as $support) {
			$supportNode = $list->addChild('support', $support['support']);
			$supportNode->addAttribute('id', $support['idSupport']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Support');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}