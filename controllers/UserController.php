<?php
class UserController extends BaseController
{
	/**
	 * @var $id int User's ID
	 */
	private $id = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /user/index/id/{id}
	 *
	 * @param $id int ID of the user. Used for POST, PUT and DELETE methods
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
	 * Show the full users list or a specific user by it's ID
	 *
	 * @param $id int User's ID
	 */
	public function show()
	{
		$userModel = new User();

		// Show the full user list or a specific user by it's ID
		$datas = $userModel->findBy('idUser', $this->getId());

		if ($this->getId() && !$datas) {
			$this->exitError(400, "This user doesn't exist.");
		}

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
		}
	}

	/**
	 * Add new user
	 * Route: /user
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(User::getRequiredFields(), $_POST);

		$userModel    = new User();
		$insertedUser = $userModel->insertUser($_POST);

		if ($insertedUser) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update user
	 * Route: /user/index/id/{id}
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
		$requiredFields = User::getRequiredFields();
		if (isset($requiredFields['role'])) unset($requiredFields['role']);
		$this->checkRequiredFields($requiredFields, $_PUT);

		$userModel  = new User();
		$updateUser = $userModel->updateUser($this->getId(), $_PUT);

		if ($updateUser) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete user.
	 * Route: /user/index/id/{id}
	 *
	 * @param $id int User's ID to delete
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

		$userModel   = new User();
		$deletedUser = $userModel->deleteUser($this->getId());

        if ($deletedUser) {
            $this->sendStatus(204);
        } else {
            $this->exitError(400, 'An error has occured. Please try again.');
        }
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one user's XML.
	 *
	 * @param array $user User to insert in the XML
	 * @return SimpleXMLElement $users List of users or user
	 */
	public function generateXml($users = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><users/>');
		foreach ($users as $user) {
			$userNode = $list->addChild('user');
			$userNode->addAttribute('id', $user['idUser']);
			$userNode->addChild('username', $user['username']);
			$userNode->addChild('password', $user['password']);
			$userNode->addChild('apiKey', $user['apiKey']);
			$userNode->addChild('apiSecret', $user['apiSecret']);
			$userNode->addChild('role', $user['role']);
		}
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'users.xsd')) {
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