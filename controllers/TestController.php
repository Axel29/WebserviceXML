<?php
/**
 * @todo Vérifier que l'ID de la console existe avant d'insérer ou de mettre à jour.
 */
class TestController extends BaseController
{
	/**
	 * @var $id int Test's ID
	 */
	private $id = null;

	/**
	 * @var $requiredFields array Required fields and their types for insert / update
	 */
	private $requiredFields = [
		'report'            => 'string',
		'date'              => 'date',
		'user_name'         => 'string',
		'note'              => 'int',
		'console_idConsole' => 'int',
	];

	/**
	 * @var $shopsRequiredFields array Required fields for shops and their types for insert / update
	 */
	private $commentsRequiredFields = [
		'date'        => 'date',
		'user_name'   => 'string',
		'note'        => 'int',
		'like'        => 'int',
		'dislike'     => 'int',
		'text'        => 'string',
		'test_idTest' => 'int',
	];

	/**
	 * @var $analysesRequiredFields array Required fields for analyses and their types for insert / update
	 */
	private $analysesRequiredFields = [
		'analyse'     => 'string',
		'type'        => 'string',
		'test_idTest' => 'int',
	];

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /test/index/id/{id}
	 *
	 * @param $id int ID of the test. Used for POST, PUT and DELETE methods
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
	 * Show the full tests list or a specific test by it's ID
	 *
	 * @param $id int Test's ID
	 */
	public function show()
	{
		$testModel = new Test();

		// Show the full test list or a specific test by it's ID
		$datas = $testModel->findBy('idTest', $this->getId());

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
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
		$this->checkRequiredFields($this->requiredFields, $_POST);

		// Check every required fields for comments if neeeded
		if (isset($_POST['comments'])) {
			foreach ($_POST['comments'] as $comment) {
				$this->checkRequiredFields($this->commentsRequiredFields, $comment);
			}
		}

		// Check every required fields for analyses if neeeded
		if (isset($_POST['analyses'])) {
			foreach ($_POST['analyses'] as $analyse) {
				$this->checkRequiredFields($this->analysesRequiredFields, $analyse);
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
			$this->exitError(400, "'id' must be specified.");
		}

		// Check every required field
		$this->checkRequiredFields($this->requiredFields, $_PUT);

		// Check every required fields for comments if neeeded
		$this->commentsRequiredFields['idComment'] = 'int';
		if (isset($_PUT['comments'])) {
			foreach ($_PUT['comments'] as $comment) {
				$this->checkRequiredFields($this->commentsRequiredFields, $comment);
			}
		}

		// Check every required fields for analyses if neeeded
		$this->analysesRequiredFields['idAnalyse'] = 'int';
		if (isset($_PUT['analyses'])) {
			foreach ($_PUT['analyses'] as $analyse) {
				$this->checkRequiredFields($this->analysesRequiredFields, $analyse);
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
			$this->exitError(400, "'id' must be specified.");
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'tests.xsd')) {
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
			$this->exitError(400, sprintf("'id' must be an integer, %s given", gettype($id)));
		}
	}
}