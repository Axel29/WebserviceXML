<?php
class UserController extends CRUD
{
	/**
	 * Show the full users list or a specific user by it's ID
	 *
	 * @param $id int User's ID
	 */
	public function show()
	{
		$userModel = new User();
		$page      = $this->getPage() ? $this->getPage() : 1;

		// Show the full user list or a specific user by it's ID
		$datas = $userModel->findBy('idUser', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'users.xsd')) {
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
			$userNode->addChild('email', $user['email']);
			$userNode->addChild('username', $user['username']);
			$userNode->addChild('password', $user['password']);
			$userNode->addChild('apiKey', $user['apiKey']);
			$userNode->addChild('apiSecret', $user['apiSecret']);
			$userNode->addChild('role', $user['role']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('User');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}