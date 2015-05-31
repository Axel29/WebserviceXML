<?php
class GameController extends BaseController
{
	/**
	 * @var $id int Game's ID
	 */
	private $id = null;

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /game/index/id/{id}
	 *
	 * @param $id int ID of the game. Used for POST, PUT and DELETE methods
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
	 * Show the full games list or a specific game by it's ID
	 *
	 * @param $id int Game's ID
	 */
	public function show()
	{
		$gameModel = new Game();

		// Show the full console list or a specific console by it's ID
		$datas = $gameModel->findBy('idGame', $this->getId());

		if ($datas) {
			$this->xml = $this->generateXml($datas)->asXML();

			if ($errors = $this->validateXML($this->xml)) {
				$this->exitError(400, $errors);
			} else {
				$this->loadLayout('xml');
				echo $this->xml;
			}
		} else {
			$this->exitError(400, "This game doesn't exist.");
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

	    // Test datas
	    $_POST = [];

	    $_POST['genders'] = [
	    	[
	    		'gender' => 'Genre n°1',
	    	],
	    	[
	    		'gender' => 'Genre n°2',
	    	],
	    ];

	    $_POST['title'] = 'Titre jeu n°1';
	    
	    $_POST['editors'] = [
	    	[
	    		'editor' => 'Editeur n°1',
	    	],
	    	[
	    		'editor' => 'Editeur n°2',
	    	],
	    ];

	    $_POST['themes'] = [
	    	[
	    		'theme' => 'Thème n°1',
	    	],
	    	[
	    		'theme' => 'Thème n°2',
	    	],
	    ];

	    $_POST['site'] = 'http://www.jeu-1.com/';

	    $_POST['consoles'] = [
	    	[
				'business_model'  => 'Business Model n°1',
				'pegi'            => 'Pegi n°1',
				'modes'           => [
			    	[
			    		'mode' => 'Mode n°1',
			    	],
			    	[
			    		'mode' => 'Mode n°2',
			    	],
			    ],
				'cover_front' => 'http://www.cover-front.com/',
				'cover_back'  => 'http://www.cover-back.com/',
				'supports'    => [
			    	[
			    		'support' => 'Support n°1',
			    	],
			    	[
			    		'support' => 'Support n°2',
			    	],
			    ],
			    'release' => '2015-01-25',
			    'editions' => [
			    	[
						'name'              => 'Nom édition n°1',
						'content'           => 'Contenu édition n°1',
						'shops' => [
							[
								'url'               => 'http://www.shop-n1.com/',
								'name'              => 'Nom magasin n°1',
								'price'             => '1.10',
								'devise'            => '€',
							]
						],
			    	],
			    	[
						'name'              => 'Nom édition n°2',
						'content'           => 'Contenu édition n°2',
						'console_idConsole' => '1',
						'shops' => [
							[
								'url'               => 'http://www.shop-n2.com/',
								'name'              => 'Nom magasin n°2',
								'price'             => '2.20',
								'devise'            => '$',
							]
						],
			    	],
			    ],
			    'name' => 'Nom console n°1',
			    'description' => 'Description console n°1',
			    'dlcs' => [
			    	[
			    		'title'             => 'Titre DLC n°1',
						'description'       => 'Description DLC n°1',
						'price'             => '1.10',
						'devise'            => '€',
			    	],
			    	[
			    		'title'             => 'Titre DLC n°2',
						'description'       => 'Description DLC n°2',
						'price'             => '2.20',
						'devise'            => '$',
			    	],
			    ],
			    'configs' => [
			    	[
			    		'config'            => 'Config n°1',
						'type'              => 'Type config n°1',
			    	],
			    	[
			    		'config'            => 'Config n°2',
						'type'              => 'Type config n°2',
			    	],
			    ],
			    'tests' => [
			    	[
			    		'report'            => 'Report test n°1',
						'date'              => '2015-01-22 11:33:33',
						'user_name'         => 'User name test n°1',
						'note'              => '1',
						'comments'          => [
							[
								'date'        => '2015-01-22 11:33:33',
								'user_name'   => 'User name commentaire n°1',
								'note'        => '1',
								'like'        => '1',
								'dislike'     => '1',
								'text'        => 'Text commentaire n°1',
							],
							[
								'date'        => '2015-02-20 12:44:44',
								'user_name'   => 'User name commentaire n°2',
								'note'        => '2',
								'like'        => '2',
								'dislike'     => '2',
								'text'        => 'Text commentaire n°2',
							],
						],
						'analyses' => [
							[
								'analyse'     => 'Analyse n°1',
								'type'        => 'Type analyse n°1',
							],
							[
								'analyse'     => 'Analyse n°2',
								'type'        => 'Type analyse n°2',
							],
						]
			    	],
			    ],
			],
	    ];

	    $_POST['languages'] = [
	    	[
				'language' => 'Langage n°1',
	    	],
	    	[
				'language' => 'Langage n°2',
	    	],
	    ];

	    $_POST['articles'] = [
	    	[
				'type'          => 'Type article n°1',
				'title'         => 'Titre article n°1',
				'user_name'     => 'User name article n°1',
				'date'          => '2015-02-05 17:54:43',
				'console_names' => 'Console names article n°1',
	    	],
	    	[
				'type'          => 'Type article n°2',
				'title'         => 'Titre article n°2',
				'user_name'     => 'User name article n°2',
				'date'          => '2015-02-05 17:54:43',
				'console_names' => 'Console names article n°2',
	    	],
	    ];

	    $_POST['medias'] = [
	    	[
				'type'          => 'Type media n°1',
				'url'           => 'URL media n°1',
				'unit'          => 'Unité media n°1',
				'width'         => '100',
				'height'        => '100',
				'console_names' => 'Console names media n°1',
	    	],
	    	[
				'type'          => 'Type media n°2',
				'url'           => 'URL media n°2',
				'unit'          => 'Unité media n°2',
				'width'         => '200',
				'height'        => '200',
				'console_names' => 'Console names media n°2',
	    	],
	    ];

	    $_POST['tips'] = [
	    	[
				'content'       => 'Contenu astuce n°1',
				'console_names' => 'Console names astuce n°1',
	    	],
	    	[
				'content'       => 'Contenu astuce n°2',
				'console_names' => 'Console names astuce n°2',
	    	],
	    ];

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
		
		if (!$this->getId()) {
			$this->exitError(400, "'id' must be specified.");
		}

		$post = [
			/**
			 * @todo Ajouter tous les paramètres.
			 */
		];

		$gameModel = new Game();
		$gameModel->updateGame($this->getId(), $post);

		$this->sendStatus(204);
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
		$list = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><list/>');
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

		if (!$domDocument->schemaValidate(SCHEMAS_PATH . 'schemavideogame.xsd')) {
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