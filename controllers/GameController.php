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

		$this->xml = $this->generateXml($datas)->asXML();

		if ($errors = $this->validateXML($this->xml)) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			echo $this->xml;
		}
	}

	/**
	 * Add new game
	 * Route: /game
	 */
	public function add()
	{
		// Security check for the request method
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only POST methods are allowed.');
			return;
		}
		// if (isset($_POST['analyses']['analyse']) && 
		// 	isset($_POST['analyses']['type']) &&
		// 	isset($_POST['articles']['type']) &&
		// 	isset($_POST['articles']['title']) &&
		// 	isset($_POST['articles']['user_name']) &&
		// 	isset($_POST['articles']['date']) &&
		// 	isset($_POST['articles']['console_names']) &&
		// 	isset($_POST['comments']['date']) &&
		// 	isset($_POST['comments']['user_name']) &&
		// 	isset($_POST['comments']['note']) &&
		// 	isset($_POST['comments']['like']) &&
		// 	isset($_POST['comments']['dislike']) &&
		// 	isset($_POST['comments']['text']) &&
		// 	isset($_POST['configs']['config']) &&
		// 	isset($_POST['configs']['type']) &&
		// 	isset($_POST['consoles']['business_model']) &&
		// 	isset($_POST['consoles']['pegi']) &&
		// 	isset($_POST['consoles']['release']) &&
		// 	isset($_POST['consoles']['name']) &&
		// 	isset($_POST['consoles']['description']) &&
		// 	isset($_POST['consoles']['cover_front']) &&
		// 	isset($_POST['consoles']['cover_back']) &&
		// 	isset($_POST['dlcs']['title']) &&
		// 	isset($_POST['dlcs']['description']) &&
		// 	isset($_POST['dlcs']['price']) &&
		// 	isset($_POST['dlcs']['devise']) &&
		// 	isset($_POST['editions']['name']) &&
		// 	isset($_POST['editions']['content']) &&
		// 	isset($_POST['editors']['editor']) &&
		// 	isset($_POST['game']['title']) &&
		// 	isset($_POST['game']['site']) &&
		// 	isset($_POST['genders']['gender']) &&
		// 	isset($_POST['languages']['language']) &&
		// 	isset($_POST['medias']['type']) &&
		// 	isset($_POST['medias']['url']) &&
		// 	isset($_POST['medias']['unit']) &&
		// 	isset($_POST['medias']['width']) &&
		// 	isset($_POST['medias']['height']) &&
		// 	isset($_POST['medias']['console_names']) &&
		// 	isset($_POST['modes']['name']) &&
		// 	isset($_POST['shops']['url']) && 
		// 	isset($_POST['shops']['name']) && 
		// 	isset($_POST['shops']['price']) &&
		// 	isset($_POST['shops']['device']) && 
		// 	isset($_POST['supports']['support']) &&
		// 	isset($_POST['tests']['report']) &&
		// 	isset($_POST['tests']['date']) &&
		// 	isset($_POST['tests']['user_name']) &&
		// 	isset($_POST['tests']['note']) &&
		// 	isset($_POST['themes']['theme']) &&
		// 	isset($_POST['tips']['content']) &&
		// 	isset($_POST['tips']['console_names'])
		// 	) {

			$gameModel = new Game();
			$gameModel->addGame($_POST);

			$this->sendStatus(204);

		// } else {
		// 	$this->exitError(400);
		// }
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

	public function testAction()
	{
		$dateTime = date("Y-m-d H:i:s");
		$datas    = [
			'analyses'       => [
				'analyse'        => 'Test analyse -> analyse INSERT',
				'type'           => 'Test analyse -> type INSERT',
			],
			'articles'       => [
				'type'           => 'Test article -> type INSERT',
				'title'          => 'Test article -> title INSERT',
				'user_name'      => 'Test article -> user_name INSERT',
				'date'           => $dateTime,
				'console_names' => 'Test article -> console_names INSERT',
			],
			'comments'       => [
				'date'           => $dateTime,
				'user_name'      => 'Test comment -> user_name INSERT',
				'note'           => 'Test comment -> note INSERT',
				'like'           => 'Test comment -> like INSERT',
				'dislike'        => 'Test comment -> dislike INSERT',
				'text'           => 'Test comment -> text INSERT',
			],
			'configs'        => [
				'config'         => 'Test config -> config INSERT',
				'type'           => 'Test config -> type INSERT',
			],
			'consoles'       => [
				'business_model' => 'Test console -> business_model INSERT',
				'pegi'           => 'Test console -> pegi INSERT',
				'release'        => 'Test console -> release INSERT',
				'name'           => 'Test console -> name INSERT',
				'description'    => 'Test console -> description INSERT',
				'cover_front'    => 'Test console -> cover_front INSERT',
				'cover_back'     => 'Test console -> cover_back INSERT',
			],
			'dlcs'           => [
				'title'          => 'Test dlc -> title INSERT',
				'description'    => 'Test dlc -> description INSERT',
				'price'          => 'Test dlc -> price INSERT',
				'devise'         => 'Test dlc -> devise INSERT',
			],
			'editions'       => [
				'name'           => 'Test edition -> name INSERT',
				'content'        => 'Test edition -> content INSERT',
			],
			'editors'        => [
				'editor'         => 'Test editor -> editor INSERT',
			],
			'game'           => [
				'title'          => 'Test game -> title INSERT',
				'site'           => 'Test game -> site INSERT',
			],
			'genders'        => [
				'gender'         => 'Test gender -> gender INSERT',
			],
			'languages'      => [
				'language'       => 'Test language -> language INSERT',
			],
			'medias'         => [
				'type'           => 'Test media -> type INSERT',
				'url'            => 'Test media -> url INSERT',
				'unit'           => 'Test media -> unit INSERT',
				'width'          => 'Test media -> width INSERT',
				'height'         => 'Test media -> height INSERT',
				'console_names' => 'Test media -> console_names INSERT',
			],
			'modes'          => [
				'name'           => 'Test mode -> name INSERT',
			],
			'shops'          => [
				'url'            => 'Test shop -> url INSERT',
				'name'           => 'Test shop -> name INSERT',
				'price'          => 'Test shop -> price INSERT',
				'device'         => 'Test shop -> device INSERT',
			],
			'supports'       => [
				'support'        => 'Test support -> support INSERT',
			],
			'tests'          => [
				'report'         => 'Test test -> report INSERT',
				'date'           => date('Y-m-d'),
				'user_name'      => 'Test test -> user_name INSERT',
				'note'           => 'Test test -> note INSERT',
			],
			'themes'         => [
				'theme'          => 'Test theme -> theme INSERT',
			],
			'tips'           => [
				'content'        => 'Test tip -> content INSERT',
				'console_names' => 'Test tip -> console_names INSERT',
			],
		];
		$data     = http_build_query ($datas);

		// echo '<pre>'; var_dump($datas); echo '</pre>'; die;
		 
		$context = stream_context_create ([
			'http' => [
				'method'  => 'POST',
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n" . 
							 "Content-Length: " . strlen($data) . "\r\n",
				'content' => $data,
			]
		]);

		$result = file_get_contents('http://ws-xml.localhost.com/game', null, $context);
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