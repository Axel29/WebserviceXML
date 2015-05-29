<?php
/**
 * @todo Vérifier que l'ID du jeu existe avant d'insérer ou de mettre à jour.
 */
class MediaController extends BaseController
{
	/**
	 * @var $id int Media's ID
	 */
	private $id = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /media/index/id/{id}
	 *
	 * @param $id int ID of the media. Used for POST, PUT and DELETE methods
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
	 * Show the full medias list or a specific media by it's ID
	 *
	 * @param $id int Media's ID
	 */
	public function show()
	{
		$mediaModel = new Media();

		// Show the full media list or a specific media by it's ID
		$datas = $mediaModel->findBy('idMedia', $this->getId());

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
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
			$this->exitError(400, "'id' must be specified.");
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
			$this->exitError(400, "'id' must be specified.");
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

			$consoleNamesNode = $mediaNode->addChild('consolesNames');
			foreach (explode(',', $media['console_names']) as $consoleName) {
				$consoleNamesNode->addChild('consoleName', $consoleName);
			}

			$dimensionsNode = $mediaNode->addChild('dimensions');
			$dimensionsNode->addAttribute('unit', $media['unit']);
			$dimensionsNode->addAttribute('width', $media['width']);
			$dimensionsNode->addAttribute('height', $media['height']);
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'medias.xsd')) {
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
			$this->exitError(400, sprintf('The ID must be an integer. %s given', gettype($id)));
		}
	}
}