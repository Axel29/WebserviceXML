<?php
/**
 * @todo Vérifier que l'ID du jeu existe avant d'insérer ou de mettre à jour.
 */
class TipController extends CRUD
{
	/**
	 * Show the full tips list or a specific tip by it's ID
	 *
	 * @param $id int Tip's ID
	 */
	public function show()
	{
		$tipModel = new Tip();
		$page     = $this->getPage() ? $this->getPage() : 1;

		// Show the full tip list or a specific tip by it's ID
		$datas = $tipModel->findBy('idTip', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'tips.xsd')) {
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
	 * Add new tip
	 * Route: /tip
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Tip::getRequiredFields(), $_POST);

		$tipModel    = new Tip();
		$insertedTip = $tipModel->insertTip($_POST);

		if ($insertedTip) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update tip
	 * Route: /tip/index/id/{id}
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
		$this->checkRequiredFields(Tip::getRequiredFields(), $_PUT);

		$tipModel  = new Tip();
		$updateTip = $tipModel->updateTip($this->getId(), $_PUT);

		if ($updateTip) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete tip.
	 * Route: /tip/index/id/{id}
	 *
	 * @param $id int Tip's ID to delete
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

		$tipModel   = new Tip();
		$deletedTip = $tipModel->deleteTip($this->getId());

		if ($deletedTip) {
			$this->sendStatus(204);
		} else {
			$this->exitError(400, 'An error has occured. Please try again.');
		}
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one tip's XML.
	 *
	 * @param array $tip Tip to insert in the XML
	 * @return SimpleXMLElement $tips List of tips or tip
	 */
	public function generateXml($tips = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tips/>');
		foreach ($tips as $tip) {
			$tipNode = $list->addChild('tip');
			$tipNode->addAttribute('id', $tip['idTip']);

			$consolesNamesNode = $tipNode->addChild('consolesNames');
			foreach (explode(',', $tip['console_names']) as $consoleName) {
				$consolesNamesNode->addChild('consoleName', $consoleName);
			}
			
			$tipNode->addChild('content', $tip['content']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Tip');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}