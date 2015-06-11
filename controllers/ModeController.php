<?php
/**
 * @todo Vérifier que l'ID de la console existe avant d'insérer ou de mettre à jour.
 */
class ModeController extends CRUD
{
	/**
	 * Show the full modes list or a specific mode by it's ID
	 *
	 * @param $id int Mode's ID
	 */
	public function show()
	{
		$modeModel = new Mode();
		$page      = $this->getPage() ? $this->getPage() : 1;

		// Show the full mode list or a specific mode by it's ID
		$datas = $modeModel->findBy('idMode', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'modes.xsd')) {
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
	 * Add new mode
	 * Route: /mode
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Mode::getRequiredFields(), $_POST);

		$modeModel    = new Mode();
		$insertedMode = $modeModel->insertMode($_POST);

		if ($insertedMode) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update mode
	 * Route: /mode/index/id/{id}
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
		$this->checkRequiredFields(Mode::getRequiredFields(), $_PUT);

		$modeModel  = new Mode();
		$updatedMode = $modeModel->updateMode($this->getId(), $_PUT);

		if ($updatedMode) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete mode
	 * Route: /mode/index/id/{id}
	 *
	 * @param $id int Mode's ID to delete
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

		$modeModel   = new Mode();		
		$deletedMode = $modeModel->deleteMode($this->getId());

        if ($deletedMode) {
            $this->sendStatus(204);
        } else {
            $this->exitError(400, 'An error has occured. Please try again.');
        }
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one mode's XML.
	 *
	 * @param array $mode Mode to insert in the XML
	 * @return SimpleXMLElement $modes List of modes or mode
	 */
	public function generateXml($modes = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><modes/>');
		foreach ($modes as $mode) {
			$modeNode = $list->addChild('mode', $mode['mode']);
			$modeNode->addAttribute('id', $mode['idMode']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Mode');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}