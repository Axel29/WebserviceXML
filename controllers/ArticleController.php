<?php
class ArticleController extends CRUD
{
	/**
	 * Show the full articles list or a specific article by it's ID
	 *
	 * @param $id int Article's ID
	 */
	public function show()
	{
		$articleModel = new Article();
		$page         = $this->getPage() ? $this->getPage() : 1;

		// Show the full article list or a specific article by it's ID
		$datas = $articleModel->findBy('idArticle', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'articles.xsd')) {
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
	 * Add new article
	 * Route: /article
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Article::getRequiredFields(), $_POST);

		$articleModel    = new Article();
		$insertedArticle = $articleModel->insertArticle($_POST);

		if ($insertedArticle) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update article
	 * Route: /article/index/id/{id}
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
		$this->checkRequiredFields(Article::getRequiredFields(), $_PUT);

		$articleModel  = new Article();
		$updateArticle = $articleModel->updateArticle($this->getId(), $_PUT);

		if ($updateArticle) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete article.
	 * Route: /article/index/id/{id}
	 *
	 * @param $id int Article's ID to delete
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

		$articleModel   = new Article();
		$deletedArticle = $articleModel->deleteArticle($this->getId());

		if ($deletedArticle) {
			$this->sendStatus(204);
		} else {
			$this->exitError(400, 'An error has occured. Please try again.');
		}
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one article's XML.
	 *
	 * @param array $article Article to insert in the XML
	 * @return SimpleXMLElement $articles List of articles or article
	 */
	public function generateXml($articles = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><articles/>');
		foreach ($articles as $article) {
			$articleNode = $list->addChild('article');
			$articleNode->addAttribute('id', $article['idArticle']);
			$articleNode->addAttribute('type', $article['type']);

			$consolesNamesNode = $articleNode->addChild('consolesNames');
			foreach (explode(',', $article['console_names']) as $consoleName) {
				$consolesNamesNode->addChild('consoleName', $consoleName);
			}

			$articleNode->addChild('title', $article['title']);
			$articleNode->addChild('userName', $article['user_name']);
			$articleNode->addChild('date', $article['date']);
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Article');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}