<?php
class RoleController extends CRUD
{
	/**
	 * Show the full roles list or a specific role by it's ID
	 *
	 * @param $id int Role's ID
	 */
	public function show()
	{
		$roleModel = new Role();
		$page      = $this->getPage() ? $this->getPage() : 1;

		// Show the full role list or a specific role by it's ID
		$datas = $roleModel->findBy('idRole', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'roles.xsd')) {
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
	 * Add new role
	 * Route: /role
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Role::getRequiredFields(), $_POST);

		$roleModel    = new Role();
		$insertedRole = $roleModel->insertRole($_POST);

		if ($insertedRole) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update role
	 * Route: /role/index/id/{id}
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
		$this->checkRequiredFields(Role::getRequiredFields(), $_PUT);

		$roleModel  = new Role();
		$updateRole = $roleModel->updateRole($this->getId(), $_PUT);

		if ($updateRole) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete role.
	 * Route: /role/index/id/{id}
	 *
	 * @param $id int Role's ID to delete
	 */
	public function delete()
	{		
		$this->exitError(405, 'Roles deletion is not allowed.');
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one role's XML.
	 *
	 * @param array $role Role to insert in the XML
	 * @return SimpleXMLElement $roles List of roles or role
	 */
	public function generateXml($roles = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><roles/>');
		foreach ($roles as $role) {
			$roleNode = $list->addChild('role', $role['role']);
			$roleNode->addAttribute('id', $role['idRole']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Role');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}