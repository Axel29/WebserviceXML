<?php
class EditorController extends CRUD
{
	/**
	 * Show the full editors list or a specific editor by it's ID
	 *
	 * @param $id int Editor's ID
	 */
	public function show()
	{
		$editorModel = new Editor();
		$page = $this->getPage() ? $this->getPage() : 1;

		// Show the full editor list or a specific editor by it's ID
		$datas = $editorModel->findBy('idEditor', $this->getId(), false, $page);

		if ($this->getId() && !$datas) {
			$this->exitError(400, "This editor doesn't exist.");
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
	 * Add new editor
	 * Route: /editor
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Editor::getRequiredFields(), $_POST);

		$editorModel    = new Editor();
		$insertedEditor = $editorModel->insertEditor($_POST);

		if ($insertedEditor) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update editor
	 * Route: /editor/index/id/{id}
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
		$this->checkRequiredFields(Editor::getRequiredFields(), $_PUT);

		$editorModel  = new Editor();
		$updateEditor = $editorModel->updateEditor($this->getId(), $_PUT);

		if ($updateEditor) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete editor. Forbiddent action.
	 * Route: /editor/index/id/{id}
	 *
	 * @param $id int Editor's ID to delete
	 */
	public function delete()
	{		
		$this->exitError(405, 'Editors deletion is not allowed.');
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one editor's XML.
	 *
	 * @param array $node Editors datas
	 * @return SimpleXMLElement $list List of editors or editor
	 */
	public function generateXml($node = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><editors/>');
		foreach ($node as $editor) {
			$editorNode = $list->addChild('editor', $editor['editor']);
			$editorNode->addAttribute('id', $editor['idEditor']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Editor');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}