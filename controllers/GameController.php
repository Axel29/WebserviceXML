<?php
class GameController extends CRUD
{
	/**
	 * Show the full games list or a specific game by it's ID
	 *
	 * @param $id int Game's ID
	 */
	public function show()
	{
		$gameModel = new Game();
		$page      = $this->getPage() ? $this->getPage() : 1;

		// Show the full console list or a specific console by it's ID
		$datas = $gameModel->findBy('idGame', $this->getId(), false, $page);

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml, SCHEMAS_PATH . 'schemavideogame.xsd')) {
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
		$this->checkRequiredFields(Game::getRequiredFields(), $_POST);

		// Check every required fields for genders
		foreach ($_POST['genders'] as $gender) {
			$this->checkRequiredFields(Gender::getRequiredFields(), $gender);
		}

		// Check every required fields for editors
		foreach ($_POST['editors'] as $editor) {
			$this->checkRequiredFields(Editor::getRequiredFields(), $editor);
		}

		// Check every required fields for consoles
		foreach ($_POST['consoles'] as $console) {
			$requiredFields = Console::getRequiredFields();
			if (isset($requiredFields['game_idGame'])) unset($requiredFields['game_idGame']);
			$this->checkRequiredFields($requiredFields, $console, 'Y-m-d');
		}

		// Check every required fields for themes
		if (isset($_POST['themes'])) {
			foreach ($_POST['themes'] as $theme) {
				$this->checkRequiredFields(Theme::getRequiredFields(), $theme);
			}
		}

		// Check every required fields for languages
		foreach ($_POST['languages'] as $language) {
			$this->checkRequiredFields(Language::getRequiredFields(), $language);
		}

		// Check every required fields for articles
		if (isset($_POST['articles'])) {
			$requiredFields = Article::getRequiredFields();
			if (isset($requiredFields['game_idGame'])) unset($requiredFields['game_idGame']);
			foreach ($_POST['articles'] as $article) {
				$this->checkRequiredFields($requiredFields, $article);
			}
		}

		// Check every required fields for medias
		if (isset($_POST['medias'])) {
			$requiredFields = Media::getRequiredFields();
			if (isset($requiredFields['game_idGame'])) unset($requiredFields['game_idGame']);
			foreach ($_POST['medias'] as $media) {
				$this->checkRequiredFields($requiredFields, $media);
			}
		}

		// Check every required fields for tips
		if (isset($_POST['tips'])) {
			$requiredFields = Tip::getRequiredFields();
			if (isset($requiredFields['game_idGame'])) unset($requiredFields['game_idGame']);
			foreach ($_POST['tips'] as $tip) {
				$this->checkRequiredFields($requiredFields, $tip);
			}
		}

		$gameModel    = new Game();
		$insertedGame = $gameModel->insertGame($_POST);

		if ($insertedGame) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Update game
	 * Route: /game/index/id/{id}
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
		$this->checkRequiredFields(Game::getRequiredFields(), $_PUT, 'Y-m-d');

		// Check every required fields for genders
		foreach ($_PUT['genders'] as $gender) {
			$requiredFields = Gender::getRequiredFields();
			if (isset($gender['idGender'])) unset($requiredFields['gender']);
			$this->checkRequiredFields($requiredFields, $gender);
		}
		
		// Check every required fields for editors
		foreach ($_PUT['editors'] as $editor) {
			$requiredFields = Editor::getRequiredFields();
			if (isset($editor['idEditor'])) unset($requiredFields['editor']);
			$this->checkRequiredFields($requiredFields, $editor);
		}
		
		// Check every required fields for themes
		foreach ($_PUT['themes'] as $theme) {
			$requiredFields = Theme::getRequiredFields();
			if (isset($theme['idTheme'])) unset($requiredFields['theme']);
			$this->checkRequiredFields($requiredFields, $theme);
		}

		// Check every required fields for console
		foreach ($_PUT['consoles'] as $console) {
			$requiredFields = Console::getRequiredFields();
			if (isset($requiredFields['game_idGame'])) unset($requiredFields['game_idGame']);
			$this->checkRequiredFields($requiredFields, $console, 'Y-m-d');

			// Check every required fields for modes
			foreach ($console['modes'] as $mode) {
				$this->checkRequiredFields(Mode::getRequiredFields(), $mode);
			}

			// Check every required fields for supports
			foreach ($console['supports'] as $support) {
				$this->checkRequiredFields(Support::getRequiredFields(), $support);
			}

			// Check every required fields for editions
			foreach ($console['editions'] as $edition) {
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
			if (isset($console['dlcs'])) 
				$requiredFields = Dlc::getRequiredFields();
				if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);{
				foreach ($console['dlcs'] as $dlc) {
					$this->checkRequiredFields($requiredFields, $dlc);
				}
			}

			// Check every required fields for configs
			if (isset($console['configs'])) {
				$requiredFields = Config::getRequiredFields();
				if (isset($requiredFields['console_idConsole'])) unset($requiredFields['console_idConsole']);
				foreach ($console['configs'] as $config) {
					$this->checkRequiredFields($requiredFields, $config);
				}
			}

			// Check every required fields for tests
			if (isset($console['tests'])) {
				foreach ($console['tests'] as $test) {
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
		}

		// Check every required fields for languages
		foreach ($_PUT['languages'] as $language) {
			$requiredFields = Language::getRequiredFields();
			if (isset($language['idTheme'])) unset($requiredFields['language']);
			$this->checkRequiredFields($requiredFields, $language);
		}

		// Check every required fields for articles
		foreach ($_PUT['articles'] as $article) {
			$requiredFields = Article::getRequiredFields();
			if (isset($requiredFields['game_idGame'])) unset($requiredFields['game_idGame']);
			$this->checkRequiredFields($requiredFields, $article);
		}

		// Check every required fields for medias
		foreach ($_PUT['medias'] as $media) {
			$requiredFields = Media::getRequiredFields();
			if (isset($requiredFields['game_idGame'])) unset($requiredFields['game_idGame']);
			$this->checkRequiredFields($requiredFields, $media);
		}

		// Check every required fields for tips
		foreach ($_PUT['tips'] as $tip) {
			$requiredFields = Tip::getRequiredFields();
			if (isset($requiredFields['game_idGame'])) unset($requiredFields['game_idGame']);
			$this->checkRequiredFields($requiredFields, $tip);
		}

		$gameModel   = new Game();
		$updatedGame = $gameModel->updateGame($this->getId(), $_PUT);

		if ($updatedGame) {
			$this->sendStatus(201);
			return;
		} else {
			$this->exitError(400, 'An error has occurred. Please try again.');
		}
	}

	/**
	 * Delete game
	 * Route: /game/index/id/{id}
	 *
	 * @param $id int Game's ID to delete
	 */
	public function delete()
	{		
		// Security check for the request method
		if (!$this->getRequestMethod() == 'DELETE') {
			$this->exitError(405, 'Only DELETE methods are allowed.');
			return;
		}

		if (!$this->getId()) {
			$this->exitError(400, 'ID must be specified.');
		}

		$gameModel = new Game();
		$gameModel->deleteGame($this->getId());

		$this->sendStatus(204);
	}

	/**
	 * Generate XML from database.
	 * Can generate either the entire database's XML, either only one game's XML.
	 *
	 * @param array $games Games to insert in the XML
	 * @return SimpleXMLElement $list List of games or game
	 */
	public function generateXml($games = [])
	{
		$list           = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><list/>');
		$hasCorrectGame = false;
		foreach ($games as $game) {
			// Check that every datas exist, otherwise we skip this console.
			$idGame = $game['idGame'];

			$genderModel = new Gender();
			$genders = $genderModel->findBy('idGame', $idGame);

			$editorModel = new Editor();
			$editors = $editorModel->findBy('idGame', $idGame);

			$themeModel = new Theme();
			$themes = $themeModel->findBy('idGame', $idGame);

			$consoleModel = new Console();
			$consoles = $consoleModel->findBy('game_idGame', $idGame);

			$languageModel = new Language();
			$languages = $languageModel->findBy('idGame', $idGame);

			if (!$genders || !$editors || !$themes || !$consoles || !$languages) {
				continue;
			} else {
				$hasCorrectGame = true;
			}

			// If everything is okay, we can process
			$gameNode         = $list->addChild('game');
			$gameNode->addAttribute('id', $idGame);
			$presentationNode = $gameNode->addChild('presentation');

			// Genders: REQUIRED
			$gendersNode = $presentationNode->addChild('genders');
			foreach ($genders as $gender) {
				$genderNode = $gendersNode->addChild('gender', $gender['gender']);
				$genderNode->addAttribute('id', $gender['idGender']);
			}

			$presentationNode->addChild('title', $game['title']);

			// Editors: REQUIRED
			$editorsNode = $presentationNode->addChild('editors');
			foreach ($editors as $editor) {
				$editorNode = $editorsNode->addChild('editor', $editor['editor']);
				$editorNode->addAttribute('id', $editor['idEditor']);
			}

			// Themes: REQUIRED
			$themesNode = $presentationNode->addChild('themes');
			foreach ($themes as $theme) {
				$themeNode = $themesNode->addChild('theme', $theme['theme']);
				$themeNode->addAttribute('id', $theme['idTheme']);
			}

			$presentationNode->addChild('site', $game['site']);

			// Consoles: REQUIRED
			$consolesNode = $presentationNode->addChild('consoles');
			foreach ($consoles as $console) {
				$idConsole = $console['idConsole'];
				// Check that every datas exist for the console too, otherwise we skip this console.
				$modes = new Mode();
				$modes = $modes->findBy('idConsole', $idConsole);

				$supports = new Support();
				$supports = $supports->findBy('idConsole', $idConsole);

				$editions = new Edition();
				$editions = $editions->findBy('console_idConsole', $idConsole);

				if (!$modes || !$supports || !$editions) {
					continue;
				}

				$consoleNode = $consolesNode->addChild('console');
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

			// Languages: REQUIRED
			$languagesNode = $presentationNode->addChild('languages');
			foreach ($languages as $language) {
				$languageNode = $languagesNode->addChild('language', $language['language']);
				$languageNode->addAttribute('id', $language['idLanguage']);
			}

			// Articles: OPTIONAL
			$articlesModel = new Article();
			if ($articles = $articlesModel->findBy('game_idGame', $idGame)) {
				$articlesNode = $gameNode->addChild('articles');
				foreach ($articles as $article) {
					$articleNode = $articlesNode->addChild('article');
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
			}

			// Media: OPTIONAL
			$mediasModel = new Media();
			if ($medias = $mediasModel->findBy('game_idGame', $idGame)) {
				$mediasNode = $gameNode->addChild('medias');
				foreach ($medias as $media) {
					$mediaNode = $mediasNode->addChild('media');
					$mediaNode->addAttribute('id', $media['idMedia']);
					$mediaNode->addAttribute('type', $media['type']);
					$mediaNode->addAttribute('url', $media['url']);

					$consolesNamesNode = $mediaNode->addChild('consolesNames');
					foreach (explode(',', $media['console_names']) as $consoleName) {
						$consolesNamesNode->addChild('consoleName', $consoleName);
					}

					$dimensionsNode = $mediaNode->addChild('dimensions');
					$dimensionsNode->addAttribute('unit', $media['unit']);
					$dimensionsNode->addAttribute('width', $media['width']);
					$dimensionsNode->addAttribute('height', $media['height']);
				}
			}

			// Tips: OPTIONAL
			$tipsModel = new Tip();
			if ($tips = $tipsModel->findBy('game_idGame', $idGame)) {
				$tipsNode = $gameNode->addChild('tips');
				foreach ($tips as $tip) {
					$tipNode = $tipsNode->addChild('tip');
					$tipNode->addAttribute('id', $tip['idTip']);

					$consolesNamesNode = $tipNode->addChild('consolesNames');
					foreach (explode(',', $tip['console_names']) as $consoleName) {
						$consolesNamesNode->addChild('consoleName', $consoleName);
					}
					
					$tipNode->addChild('content', $tip['content']);
				}
			}
		}

		if (!$this->getId() && $hasCorrectGame) {
			$nextPrevPagesUrls = $this->getNextPrevPages('Game');
			$list->addChild('prev', $nextPrevPagesUrls['prev']);
			$list->addChild('next', $nextPrevPagesUrls['next']);
		}
		return $list;
	}
}