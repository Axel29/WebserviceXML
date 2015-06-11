<?php
class CommentController extends CRUD
{
	/**
	 * Show the full comments list or a specific comment by it's ID
	 *
	 * @param $id int Comment's ID
	 */
	public function show()
	{
		$commentModel = new Comment();
		$page         = $this->getPage() ? $this->getPage() : 1;

		// Show the full comment list or a specific comment by it's ID
		$datas = $commentModel->findBy('idComment', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'comments.xsd')) {
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
	 * Add new comment
	 * Route: /comment
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Comment::getRequiredFields(), $_POST, 'Y-m-d');

		$commentModel    = new Comment();
		$insertedComment = $commentModel->insertComment($_POST);

		if ($insertedComment) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update comment
	 * Route: /comment/index/id/{id}
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
		$this->checkRequiredFields(Comment::getRequiredFields(), $_PUT, 'Y-m-d');

		$commentModel  = new Comment();
		$updatedComment = $commentModel->updateComment($this->getId(), $_PUT);

		if ($updatedComment) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete comment
	 * Route: /comment/index/id/{id}
	 *
	 * @param $id int Comment's ID to delete
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

		$commentModel   = new Comment();
		$deletedComment = $commentModel->deleteComment($this->getId());

		if ($deletedComment) {
			$this->sendStatus(204);
		} else {
			$this->exitError(400, 'An error has occured. Please try again.');
		}
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one comment's XML.
	 *
	 * @param array $comment Comment to insert in the XML
	 * @return SimpleXMLElement $comments List of comments or comment
	 */
	public function generateXml($comments = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><comments/>');
		foreach ($comments as $comment) {
			$commentNode = $list->addChild('comment');
			$commentNode->addAttribute('id', $comment['idComment']);
			$commentNode->addChild('text', $comment['text']);
			$commentNode->addChild('date', $comment['date']);
			$commentNode->addChild('userName', $comment['user_name']);
			$commentNode->addChild('note', $comment['note']);
			$commentNode->addChild('like', $comment['like']);
			$commentNode->addChild('dislike', $comment['dislike']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Comment');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}