<?php
class TestController extends CRUD
{
	/**
	 * Show the full tests list or a specific test by it's ID
	 *
	 * @param $id int Test's ID
	 */
	public function show()
	{
		$testModel = new Test();
		$page      = $this->getPage() ? $this->getPage() : 1;

		// Show the full test list or a specific test by it's ID
		$datas = $testModel->findBy('idTest', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'tests.xsd')) {
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
	 * Add new test
	 * Route: /test
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Test::getRequiredFields(), $_POST);

		// Check every required fields for comments if neeeded
		if (isset($_POST['comments'])) {
			foreach ($_POST['comments'] as $comment) {
				$this->checkRequiredFields(Comment::getRequiredFields(), $comment);
			}
		}

		// Check every required fields for analyses if neeeded
		if (isset($_POST['analyses'])) {
			foreach ($_POST['analyses'] as $analyse) {
				$this->checkRequiredFields(Analyse::getRequiredFields(), $analyse);
			}
		}

		$testModel    = new Test();
		$insertedTest = $testModel->insertTest($_POST);

		if ($insertedTest) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update test
	 * Route: /test/index/id/{id}
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
		$this->checkRequiredFields(Test::getRequiredFields(), $_PUT);

		// Check every required fields for comments if neeeded
		if (isset($_PUT['comments'])) {
			foreach ($_PUT['comments'] as $comment) {
				$this->checkRequiredFields(Comment::getRequiredFields(), $comment);
			}
		}

		// Check every required fields for analyses if neeeded
		if (isset($_PUT['analyses'])) {
			foreach ($_PUT['analyses'] as $analyse) {
				$this->checkRequiredFields(Analyse::getRequiredFields(), $analyse);
			}
		}

		$testModel  = new Test();
		$updatedTest = $testModel->updateTest($this->getId(), $_PUT);

		if ($updatedTest) {
			$this->sendStatus(204);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete test
	 * Route: /test/index/id/{id}
	 *
	 * @param $id int Test's ID to delete
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

		$testModel   = new Test();

        // Check that there is at least one test left before deleting, otherwise, the XML would be broken.
        if ($testModel->getNumberOfTestsLeft($this->getId()) > 1) {
            $deletedTest = $testModel->deleteTest($this->getId());

            if ($deletedTest) {
                $this->sendStatus(204);
            } else {
                $this->exitError(400, 'An error has occured. Please try again.');
            }
        } else {
            $this->exitError(400, 'There must be at least one test per console.');
        }
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one test's XML.
	 *
	 * @param array $test Test to insert in the XML
	 * @return SimpleXMLElement $tests List of tests or test
	 */
	public function generateXml($tests = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tests/>');
		foreach ($tests as $test) {
			$testNode = $list->addChild('test');
			$testNode->addAttribute('id', $test['idTest']);
			$testNode->addChild('report', $test['report']);
			$testNode->addChild('date', $test['date']);
			$testNode->addChild('userName', $test['user_name']);
			$testNode->addChild('note', $test['note']);

			$comments     = new Comment();
			$commentsNode = $testNode->addChild('comments');
			if ($comments = $comments->findBy('test_idTest', $test['idTest'])) {
				foreach ($comments as $comment) {
					$commentNode = $commentsNode->addChild('comment');
					$commentNode->addAttribute('id', $comment['idComment']);
					$commentNode->addChild('text', $comment['text']);
					$commentNode->addChild('date', $comment['date']);
					$commentNode->addChild('userName', $comment['user_name']);
					$commentNode->addChild('note', $comment['note']);
					$commentNode->addChild('like', $comment['like']);
					$commentNode->addChild('dislike', $comment['dislike']);
				}
			}

			$analyses     = new Analyse();
			$analysesNode = $testNode->addChild('analyses');
			if ($analyses = $analyses->findBy('test_idTest', $test['idTest'])) {
				foreach ($analyses as $analyse) {
					$analyseNode = $analysesNode->addChild('analyse');
					$analyseNode->addAttribute('id', $analyse['idAnalyse']);
					$analyseNode->addAttribute('type', $analyse['type']);
				}
			}
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Test');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}