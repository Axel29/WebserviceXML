<?php
/**
 * @todo Vérifier que l'ID du jeu existe avant d'insérer ou de mettre à jour.
 */
class MediaController extends CRUD
{
	/**
	 * Show the full medias list or a specific media by it's ID
	 *
	 * @param $id int Media's ID
	 */
	public function show()
	{
		$mediaModel = new Media();
		$page       = $this->getPage() ? $this->getPage() : 1;

		// Show the full media list or a specific media by it's ID
		$datas = $mediaModel->findBy('idMedia', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'medias.xsd')) {
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
	 * Add new media
	 * Route: /media
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Media::getRequiredFields(), $_POST);

		$mediaModel    = new Media();
		$insertedMedia = $mediaModel->insertMedia($_POST);

		if ($insertedMedia) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update media
	 * Route: /media/index/id/{id}
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
		$this->checkRequiredFields(Media::getRequiredFields(), $_PUT);

		$mediaModel  = new Media();
		$updateMedia = $mediaModel->updateMedia($this->getId(), $_PUT);

		if ($updateMedia) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete media.
	 * Route: /media/index/id/{id}
	 *
	 * @param $id int Media's ID to delete
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

		$mediaModel   = new Media();
		$deletedMedia = $mediaModel->deleteMedia($this->getId());

		if ($deletedMedia) {
			$this->sendStatus(204);
		} else {
			$this->exitError(400, 'An error has occured. Please try again.');
		}
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one media's XML.
	 *
	 * @param array $media Media to insert in the XML
	 * @return SimpleXMLElement $medias List of medias or media
	 */
	public function generateXml($medias = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><medias/>');
		foreach ($medias as $media) {
			$mediaNode = $list->addChild('media');
			$mediaNode->addAttribute('id', $media['idMedia']);
			$mediaNode->addAttribute('type', $media['type']);
			$mediaNode->addAttribute('url', $media['url']);

			$consolesNamesNode = $mediaNode->addChild('consolesNames');
			foreach (explode(',', $media['console_names']) as $consoleName) {
				$consolesNamesNode->addChild('consoleName', $consoleName);
			}

			$dimensionsNode = $mediaNode->addChild('dimensions');
			$dimensionsNode->addAttribute('unit', $media['unit']);
			$dimensionsNode->addAttribute('width', $media['width']);
			$dimensionsNode->addAttribute('height', $media['height']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Media');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}