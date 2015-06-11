<?php
class ConsoleController extends CRUD
{
	/**
	 * Show the full consoles list or a specific console by it's ID
	 *
	 * @param $id int Console's ID
	 */
	public function show()
	{
		$consoleModel = new Console();
		$page         = $this->getPage() ? $this->getPage() : 1;

		// Show the full console list or a specific console by it's ID
		$datas = $consoleModel->findBy('idConsole', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'consoles.xsd')) {
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
	 * Add new console
	 * Route: /console
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}

		// Check every required field
		$this->checkRequiredFields(Console::getRequiredFields(), $_POST, 'Y-m-d');

		// Check every required fields for modes
		foreach ($_POST['modes'] as $mode) {
			$this->checkRequiredFields(Mode::getRequiredFields(), $mode);
		}

		// Check every required fields for supports
		foreach ($_POST['supports'] as $support) {
			$this->checkRequiredFields(Support::getRequiredFields(), $support);
		}

		// Check every required fields for editions
		foreach ($_POST['editions'] as $edition) {
			$requiredFields = Edition::getRequiredFields();
			if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);
			$this->checkRequiredFields($requiredFields, $edition);

			// Check every required fields for sub-elements
			if (isset($edition['shops'])) {
				foreach ($edition['shops'] as $shop) {
					$requiredFields = Shop::getRequiredFields();
					if (isset($requiredFields['edition_idEdition'])) unset($requiredFields['edition_idEdition']);
					$this->checkRequiredFields($requiredFields, $shop);
				}
			}
		}

		// Check every required fields for dlcs
		if (isset($_POST['dlcs'])) {
			$requiredFields = Dlc::getRequiredFields();
			if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);
			foreach ($_POST['dlcs'] as $dlc) {
				$this->checkRequiredFields($requiredFields, $dlc);
			}
		}

		// Check every required fields for configs
		if (isset($_POST['configs'])) {
			$requiredFields = Config::getRequiredFields();
			if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);
			foreach ($_POST['configs'] as $config) {
				$this->checkRequiredFields($requiredFields, $config);
			}
		}

		// Check every required fields for tests
		if (isset($_POST['tests'])) {
			foreach ($_POST['tests'] as $test) {
				$requiredFields = Test::getRequiredFields();
				if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);
				$this->checkRequiredFields($requiredFields, $test);

				// Check every required fields for comments
				if (isset($test['comments'])) {
					$requiredFields = Comment::getRequiredFields();
					if (isset($requiredFields['test_idTest'])) unset($requiredFields['test_idTest']);
					foreach ($test['comments'] as $comment) {
						$this->checkRequiredFields($requiredFields, $comment);
					}
				}

				// Check every required fields for analyses
				if (isset($test['analyses'])) {
					$requiredFields = Analyse::getRequiredFields();
					if (isset($requiredFields['test_idTest'])) unset($requiredFields['test_idTest']);
					foreach ($test['analyses'] as $analyse) {
						$this->checkRequiredFields($requiredFields, $analyse);
					}
				}
			}
		}

		$consoleModel    = new Console();
		$insertedConsole = $consoleModel->insertConsole($_POST);

		if ($insertedConsole) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update console
	 * Route: /console/index/id/{id}
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
		$this->checkRequiredFields(Console::getRequiredFields(), $_PUT, 'Y-m-d');

		// Check every required fields for modes
		foreach ($_PUT['modes'] as $mode) {
			$this->checkRequiredFields(Mode::getRequiredFields(), $mode);
		}

		// Check every required fields for supports
		foreach ($_PUT['supports'] as $support) {
			$this->checkRequiredFields(Support::getRequiredFields(), $support);
		}

		// Check every required fields for editions
		foreach ($_PUT['editions'] as $edition) {
			$requiredFields = Edition::getRequiredFields();
			if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);
			$this->checkRequiredFields($requiredFields, $edition);

			// Check every required fields for sub-elements
			if (isset($edition['shops'])) {
				$requiredFields = Shop::getRequiredFields();
				if (isset($requiredFields['edition_idEdition'])) unset($requiredFields['edition_idEdition']);
				foreach ($edition['shops'] as $shop) {
					$this->checkRequiredFields($requiredFields, $shop);
				}
			}
		}

		// Check every required fields for dlcs
		if (isset($_PUT['dlcs'])) 
			$requiredFields = Dlc::getRequiredFields();
			if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);{
			foreach ($_PUT['dlcs'] as $dlc) {
				$this->checkRequiredFields($requiredFields, $dlc);
			}
		}

		// Check every required fields for configs
		if (isset($_PUT['configs'])) {
			$requiredFields = Config::getRequiredFields();
			if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);
			foreach ($_PUT['configs'] as $config) {
				$this->checkRequiredFields($requiredFields, $config);
			}
		}

		// Check every required fields for tests
		if (isset($_PUT['tests'])) {
			foreach ($_PUT['tests'] as $test) {
				$requiredFields = Test::getRequiredFields();
				if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);
				$this->checkRequiredFields($requiredFields, $test);

				// Check every required fields for comments
				if (isset($test['comments'])) {
					$requiredFields = Comment::getRequiredFields();
					if (isset($requiredFields['test_idTest'])) unset($requiredFields['test_idTest']);
					foreach ($test['comments'] as $comment) {
						$this->checkRequiredFields($requiredFields, $comment);
					}
				}

				// Check every required fields for analyses
				if (isset($test['analyses'])) {
					$requiredFields = Analyse::getRequiredFields();
					if (isset($requiredFields['test_idTest'])) unset($requiredFields['test_idTest']);
					foreach ($test['analyses'] as $analyse) {
						$this->checkRequiredFields($requiredFields, $analyse);
					}
				}
			}
		}

		$consoleModel    = new Console();
		$updatedConsole = $consoleModel->updateConsole($this->getId(), $_PUT);

		if ($updatedConsole) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete console
	 * Route: /console/index/id/{id}
	 *
	 * @param $id int Console's ID to delete
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

		$consoleModel   = new Console();

        // Check that there is at least one console left before deleting, otherwise, the XML would be broken.
        if ($consoleModel->getNumberOfConsolesLeft($this->getId()) > 1) {
            $deletedConsole = $consoleModel->deleteConsole($this->getId());

            if ($deletedConsole) {
                $this->sendStatus(204);
            } else {
                $this->exitError(400, 'An error has occured. Please try again.');
            }
        } else {
            $this->exitError(400, 'There must be at least one console per console.');
        }
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one console's XML.
	 *
	 * @param array $console Console to insert in the XML
	 * @return SimpleXMLElement $consoles List of consoles or console
	 */
	public function generateXml($consoles = [])
	{
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><consoles/>');
		foreach ($consoles as $console) {
			// Check that every datas exist, otherwise we skip this console.
			$idConsole = $console['idConsole'];
			
			$modes = new Mode();
			$modes = $modes->findBy('idConsole', $idConsole);

			$supports = new Support();
			$supports = $supports->findBy('idConsole', $idConsole);

			$editions = new Edition();
			$editions = $editions->findBy('console_idConsole', $idConsole);

			if (!$modes || !$supports || !$editions) {
				continue;
			}

			// If everything is okay, we can process
			$consoleNode = $list->addChild('console');
			$consoleNode->addAttribute('id', $console['idConsole']);

			$consoleNode->addChild('businessModel', $console['business_model']);
			$consoleNode->addChild('pegi', $console['pegi']);

			// Modes: REQUIRED
			$modesNode = $consoleNode->addChild('modes');
			foreach ($modes as $mode) {
				$modeNode = $modesNode->addChild('mode', $mode['mode']);
				$modeNode->addAttribute('id', $mode['idMode']);
			}

			// Covers
			$coverNode = $consoleNode->addChild('cover');
			$frontNode = $coverNode->addChild('front');
			$frontNode->addAttribute('url', $console['cover_front']);
			$backNode = $coverNode->addChild('back');
			$backNode->addAttribute('url', $console['cover_back']);

			// Supports: REQUIRED
			$supportsNode = $consoleNode->addChild('supports');
			foreach ($supports as $support) {
				$supportNode = $supportsNode->addChild('support', $support['support']);
				$supportNode->addAttribute('id', $support['idSupport']);
			}

			$consoleNode->addChild('release', $console['release']);

			// Editions: REQUIRED
			$editionsNode = $consoleNode->addChild('editions');
			foreach ($editions as $edition) {
				$editionNode = $editionsNode->addChild('edition');
				$editionNode->addAttribute('id', $edition['idEdition']);
				$editionNode->addChild('name', $edition['name']);
				$editionNode->addChild('content', $edition['content']);

				// Shops: optional
				$shops     = new Shop();
				$shopsNode = $editionNode->addChild('shops');
				if ($shops = $shops->findBy('edition_idEdition', $edition['idEdition'])) {
					foreach ($shops as $shop) {
						$shopNode = $shopsNode->addChild('shop');
						$shopNode->addAttribute('id', $shop['idShop']);
						$shopNode->addAttribute('url', $shop['url']);

						$shopNode->addChild('name', $shop['name']);

						$priceNode = $shopNode->addChild('price', $shop['price']);
						$priceNode->addAttribute('devise', $shop['devise']);
					}
				}
			}

			$consoleNode->addChild('name', $console['name']);
			$consoleNode->addChild('description', $console['description']);

			// Dlcs: optional
			$dlcs = new Dlc();
			$dlcsNode = $consoleNode->addChild('dlcs');
			if ($dlcs = $dlcs->findBy('console_idConsole', $idConsole)) {
				foreach ($dlcs as $dlc) {
					$dlcNode = $dlcsNode->addChild('dlc');
					$dlcNode->addAttribute('id', $dlc['idDlc']);

					$dlcNode->addChild('title', $dlc['title']);
					$dlcNode->addChild('description', $dlc['description']);

					$priceNode = $dlcNode->addChild('price', $dlc['price']);
					$priceNode->addAttribute('devise', $dlc['devise']);
				}
			}

			// Configs: optional
			$configs     = new Config();
			$configsNode = $consoleNode->addChild('configs');
			if ($configs = $configs->findBy('console_idConsole', $idConsole)) {
				foreach ($configs as $config) {
					$configNode = $configsNode->addChild('config', $config['config']);
					$configNode->addAttribute('id', $config['idConfig']);
					$configNode->addAttribute('type', $config['type']);
				}
			}

			// Tests: optional
			$tests = new Test();
			if ($tests = $tests->findBy('console_idConsole', $idConsole)) {
				foreach ($tests as $test) {
					$testNode = $consoleNode->addChild('test');
					$testNode->addAttribute('id', $test['idTest']);
					$testNode->addChild('report', $test['report']);
					$testNode->addChild('date', $test['date']);
					$testNode->addChild('userName', $test['user_name']);
					$testNode->addChild('note', $test['note']);

					// Comments: optional
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

					// Analyses: optional
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
			}
		}

		if (!$this->getId()) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Console');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}