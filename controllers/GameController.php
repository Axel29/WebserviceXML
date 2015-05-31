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

		// Show the full games list or a specific game by it's ID
		$datas = $gameModel->getGames($this->getId());

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
		foreach ($games as $idGame => $game) {
			$gameNode = $list->addChild('game');
			$gameNode->addAttribute('id', $idGame);

			$presentationNode = $gameNode->addChild('presentation');
			// Game's genders
			$gendersNode = $presentationNode->addChild('genders');
			foreach ($game['presentation']['genders'] as $gender) {
				$genderNode = $gendersNode->addChild('gender', $gender['gender']);
				$genderNode->addAttribute('id', $gender['idGender']);
			}

			// Game's title
			$presentationNode->addChild('title', $game['presentation']['title']);

			// Game's editors
			$editorsNode = $presentationNode->addChild('editors');
			foreach ($game['presentation']['editors'] as $editor) {
				$editorNode = $editorsNode->addChild('editor', $editor['editor']);
				$editorNode->addAttribute('id', $editor['idEditor']);
			}

			// Game's themes
			$themesNode = $presentationNode->addChild('themes');
			if (isset($game['presentation']['themes'])) {
				foreach ($game['presentation']['themes'] as $theme) {
					$themeNode = $themesNode->addChild('theme', $theme['theme']);
					$themeNode->addAttribute('id', $theme['idTheme']);
				}
			}

			// Game's website
			$presentationNode->addChild('site', $game['presentation']['site']);

			$consolesNode = $presentationNode->addChild('consoles');
			foreach ($game['presentation']['consoles'] as $console) {
				$consoleNode  = $consolesNode->addChild('console');
				$consoleNode->addAttribute('id', $console['idConsole']);
				// Console's business model
				$consoleNode->addChild('businessModel', $console['business_model']);
				// Console's pegi
				$consoleNode->addChild('pegi', $console['pegi']);

				// Console's modes
				$modesNode = $consoleNode->addChild('modes');
				foreach ($console['modes'] as $mode) {
					$modeNode = $modesNode->addChild('mode', $mode['mode']);
					$modeNode->addAttribute('id', $mode['idMode']);
				}

				// Console's covers
				$coverNode  = $consoleNode->addChild('cover');
				$frontCover = $coverNode->addChild('front');
				$frontCover->addAttribute('url', $console['cover_front']);

				$backCover  = $coverNode->addChild('back');
				$backCover->addAttribute('url', $console['cover_back']);
			
				// Console's supports
				$supportsNode = $consoleNode->addChild('supports');
				foreach ($console['supports'] as $support) {
					$supportNode = $supportsNode->addChild('support', $support['support']);
					$supportNode->addAttribute('id', $support['idSupport']);
				}

				// Console's release date
				$consoleNode->addChild('release', $console['release']);

				// Editions
				$editionsNode = $consoleNode->addChild('editions');
				foreach ($console['editions'] as $edition) {
					$editionNode = $editionsNode->addChild('edition');
					$editionNode->addAttribute('id', $edition['idEdition']);
					$editionNode->addChild('name', $edition['name']);
					$editionNode->addChild('content', $edition['content']);

					// Edition's shops
					$shopsNode = $editionNode->addChild('shops');
					if (isset($edition['shops'])) {
						foreach ($edition['shops'] as $shop) {
							$shopNode = $shopsNode->addChild('shop');
							$shopNode->addAttribute('id', $shop['idShop']);
							$shopNode->addAttribute('url', $shop['url']);
							$shopNode->addChild('name', $shop['name']);
							$shopPrice = $shopNode->addChild('price', $shop['price']);
							$shopPrice->addAttribute('devise', $shop['devise']);
						}
					}
				}

				// Console's name
				$consoleNode->addChild('name', $console['name']);

				// Console's description
				$consoleNode->addChild('description', $console['description']);

				// DLCs
				$dlcsNode = $consoleNode->addChild('dlcs');
				if (isset($console['dlcs'])) {
					foreach ($console['dlcs'] as $dlc) {
						$dlcNode = $dlcsNode->addChild('dlc');
						$dlcNode->addAttribute('id', $dlc['idDlc']);
						$dlcNode->addChild('title', $dlc['title']);
						$dlcNode->addChild('description', $dlc['description']);
						$dlcPrice = $dlcNode->addChild('price', $dlc['price']);
						$dlcPrice->addAttribute('devise', $dlc['devise']);						
					}
				}

				// Configs
				$configsNode = $consoleNode->addChild('configs');
				if (isset($console['configs'])) {
					foreach ($console['configs'] as $config) {
						$configNode = $configsNode->addChild('config', $config['config']);
						$configNode->addAttribute('id', $config['idConfig']);
						$configNode->addAttribute('type', $config['type']);
					}
				}

				// Tests
				if (isset($console['tests'])) {
					foreach ($console['tests'] as $test) {
						$testNode = $consoleNode->addChild('test');
						$testNode->addAttribute('id', $test['idTest']);
						$testNode->addChild('report', $test['report']);
						$testNode->addChild('date', $test['date']);
						$testNode->addChild('userName', $test['user_name']);
						$testNode->addChild('note', $test['note']);

						// Test's comments
						$commentsNode = $testNode->addChild('comments');
						if (isset($test['comments'])) {
							foreach ($test['comments'] as $comment) {
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

						// Test's analyses
						$analysesNode = $testNode->addChild('analyses');
						if (isset($test['analyses'])) {
							foreach ($test['analyses'] as $analyse) {
								$analyseNode = $analysesNode->addChild('analyse', $analyse['analyse']);
								$analyseNode->addAttribute('id', $analyse['idAnalyse']);
								$analyseNode->addAttribute('type', $analyse['type']);
							}
						}
					}
				}
			}

			// Game's languages
			$languagesNode = $presentationNode->addChild('languages');
			foreach ($game['presentation']['languages'] as $language) {
				$languageNode = $languagesNode->addChild('language', $language['language']);
				$languageNode->addAttribute('id', $language['idLanguage']);
			}

			// Game's articles
			$articlesNode = $gameNode->addChild('articles');
			if (isset($game['articles'])) {
				foreach ($game['articles'] as $article) {
					$articleNode = $articlesNode->addChild('article');
					$articleNode->addAttribute('id', $article['idArticle']);
					$articleNode->addAttribute('type', $article['type']);
					$articleConsolesNames = $articleNode->addChild('consolesNames');
					foreach (explode(',', $article['console_names']) as $consoleName) {
						$articleConsolesNames->addChild('consoleName', $consoleName);
					}
					$articleNode->addChild('title', $article['title']);
					$articleNode->addChild('userName', $article['user_name']);
					$articleNode->addChild('date', $article['date']);
				}
			}

			// Game's medias
			$mediasNode = $gameNode->addChild('medias');
			if (isset($game['medias'])) {
				foreach ($game['medias'] as $media) {
					$mediaNode = $mediasNode->addChild('media');
					$mediaNode->addAttribute('id', $media['idMedia']);
					$mediaNode->addAttribute('type', $media['type']);
					$mediaNode->addAttribute('url', $media['url']);
					$mediaConsolesNames = $mediaNode->addChild('consolesNames');
					foreach (explode(',', $media['console_names']) as $consoleName) {
						$mediaConsolesNames->addChild('consoleName', $consoleName);
					}
					$mediaDimensions = $mediaNode->addChild('dimensions');
					$mediaDimensions->addAttribute('unit', $media['unit']);
					$mediaDimensions->addAttribute('height', $media['height']);
					$mediaDimensions->addAttribute('width', $media['width']);
				}
			}

			// Game's tips
			$tipsNode = $gameNode->addChild('tips');
			if (isset($game['tips'])) {
				foreach ($game['tips'] as $tip) {
					$tipNode = $tipsNode->addChild('tip');
					$tipNode->addAttribute('id', $tip['idTip']);
					$tipConsoleNames = $tipNode->addChild('consolesNames');
					foreach (explode(',', $tip['console_names']) as $consoleName) {
						$tipConsoleNames->addChild('consoleName', $consoleName);
					}
					$tipNode->addChild('content', $tip['content']);
				}
			}
		}
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