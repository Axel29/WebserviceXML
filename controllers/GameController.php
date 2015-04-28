<?php
class GameController extends BaseController
{
	/**
	 * Show the full XML
	 * Route: /game
	 */
	public function indexAction()
	{
		$gameModel  = new Game();
		$games      = $gameModel->getGames();

		$this->list = $this->generateXml($games);

		if ($errors = $this->validateXML($this->list->asXML())) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			$this->render('index');
		}
	}

	/**
	 * Show a single game's XML by it's ID.
	 * Route: /game/show/id/{id}
	 *
	 * @param $id int Game's ID
	 */
	public function showAction($id)
	{
		$gameModel  = new Game();
		$game       = $gameModel->getGames($id);

		$this->game = $this->generateXml($game);

		if ($errors = $this->validateXML($this->game->asXML())) {
			$this->exitError(400, $errors);
		} else {
			$this->loadLayout('xml');
			$this->render('show');
		}
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
		$list = new SimpleXMLElement('<list></list>');
		foreach ($games as $game) {
			$gameNode = $list->addChild('game');

			$presentationNode = $gameNode->addChild('presentation');
				// Game's genders
				$gendersNode = $presentationNode->addChild('genders');
				foreach ($game['presentation']['genders'] as $gender) {
					$gendersNode->addChild('gender', $gender['gender']);
				}

				// Game's title
				$presentationNode->addChild('title', $game['presentation']['title']);

				// Game's editors
				$editorsNode = $presentationNode->addChild('editors');
				foreach ($game['presentation']['editors'] as $editor) {
					$editorsNode->addChild('editor', $editor['editor']);
				}

				// Game's themes
				$themesNode = $presentationNode->addChild('themes');
				foreach ($game['presentation']['themes'] as $theme) {
					$themesNode->addChild('theme', $theme['theme']);
				}

				// Game's website
				$presentationNode->addChild('site', $game['presentation']['site']);

				$consolesNode = $presentationNode->addChild('consoles');
				foreach ($game['presentation']['consoles'] as $console) {
					$consoleNode  = $consolesNode->addChild('console');
					// Console's business model
					$consoleNode->addChild('businessModel', $console['business_model']);
					// Console's pegi
					$consoleNode->addChild('pegi', $console['pegi']);

					// Console's modes
					$modesNode = $consoleNode->addChild('modes');
					foreach ($console['modes'] as $mode) {
						$modesNode->addChild('mode', $mode['mode']);
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
						$supportsNode->addChild('support', $support['support']);
					}

					// Console's release date
					$consoleNode->addChild('release', $console['release']);

					// Editions
					$editionsNode = $consoleNode->addChild('editions');
					foreach ($console['editions'] as $edition) {
						$editionNode = $editionsNode->addChild('edition');
						$editionNode->addChild('name', $edition['name']);
						$editionNode->addChild('content', $edition['content']);

						// Edition's shops
						$shopsNode = $editionNode->addChild('shops');
						foreach ($edition['shops'] as $shop) {
							$shopNode = $shopsNode->addChild('shop');
							$shopNode->addAttribute('url', $shop['url']);
							$shopNode->addChild('name', $shop['name']);
							$shopPrice = $shopNode->addChild('price', $shop['price']);
							$shopPrice->addAttribute('devise', $shop['devise']);
						}
					}

					// Console's name
					$consoleNode->addChild('name', $console['name']);

					// Console's description
					$consoleNode->addChild('description', $console['description']);

					// DLCs
					$dlcsNode = $consoleNode->addChild('dlcs');
					foreach ($console['dlcs'] as $dlc) {
						$dlcNode = $dlcsNode->addChild('dlc');
						$dlcNode->addChild('title', $dlc['title']);
						$dlcNode->addChild('description', $dlc['description']);
						$dlcPrice = $dlcNode->addChild('price', $dlc['price']);
						$dlcPrice->addAttribute('devise', $dlc['devise']);						
					}

					// Configs
					$configsNode = $consoleNode->addChild('configs');
					foreach ($console['configs'] as $config) {
						$configNode = $configsNode->addChild('config', $config['config']);
						$configNode->addAttribute('type', $config['type']);
					}

					// Tests
					foreach ($console['tests'] as $test) {
						$testNode = $consoleNode->addChild('test');
						$testNode->addChild('report', $test['report']);
						$testNode->addChild('date', $test['date']);
						$testNode->addChild('userName', $test['user_name']);
						$testNode->addChild('note', $test['note']);

						// Test's comments
						$commentsNode = $testNode->addChild('comments');
						foreach ($test['comments'] as $comment) {
							$commentNode = $commentsNode->addChild('comment');
							$commentNode->addChild('text', $comment['text']);
							$commentNode->addChild('date', $comment['date']);
							$commentNode->addChild('userName', $comment['user_name']);
							$commentNode->addChild('note', $comment['note']);
							$commentNode->addChild('like', $comment['like']);
							$commentNode->addChild('dislike', $comment['dislike']);
						}

						// Test's analyses
						$analysesNode = $testNode->addChild('analyses');
						foreach ($test['analyses'] as $analyse) {
							$analyseNode = $analysesNode->addChild('analyse', $analyse['analyse']);
							$analyseNode->addAttribute('type', $analyse['type']);
						}
					}
				}

				// Game's languages
				$languages = $presentationNode->addChild('languages');
				foreach ($game['presentation']['languages'] as $language) {
					$languages->addChild('language', $language['language']);
				}

				// Game's articles
				$articlesNode = $gameNode->addChild('articles');
				foreach ($game['articles'] as $article) {
					$articleNode = $articlesNode->addChild('article');
					$articleNode->addAttribute('type', $article['type']);
					$articleConsolesNames = $articleNode->addChild('consolesNames');
					foreach (explode(',', $article['consoles_names']) as $consoleName) {
						$articleConsolesNames->addChild('consoleName', $consoleName);
					}
					$articleNode->addChild('title', $article['title']);
					$articleNode->addChild('userName', $article['user_name']);
					$articleNode->addChild('date', $article['date']);
				}


				// Game's medias
				$mediasNode = $gameNode->addChild('medias');
				foreach ($game['medias'] as $media) {
					$mediaNode = $mediasNode->addChild('media');
					$mediaNode->addAttribute('type', $media['type']);
					$mediaNode->addAttribute('url', $media['url']);
					$mediaConsolesNames = $mediaNode->addChild('consolesNames');
					foreach (explode(',', $media['consoles_names']) as $consoleName) {
						$mediaConsolesNames->addChild('consoleName', $consoleName);
					}
					$mediaDimensions = $mediaNode->addChild('dimensions');
					$mediaDimensions->addAttribute('unit', $media['unit']);
					$mediaDimensions->addAttribute('height', $media['height']);
					$mediaDimensions->addAttribute('width', $media['width']);
				}

				// Game's tips
				$tipsNode = $gameNode->addChild('tips');
				foreach ($game['tips'] as $tip) {
					$tipNode = $tipsNode->addChild('tip');
					$tipConsoleNames = $tipNode->addChild('consolesNames');
					foreach (explode(',', $tip['consoles_names']) as $consoleName) {
						$tipConsoleNames->addChild('consoleName', $consoleName);
					}
					$tipNode->addChild('content', $tip['content']);
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

		if (!$domDocument->schemaValidate(ROOT . 'core/schemavideogame.xsd')) {
			$errors = libxml_get_errors();

			foreach ($errors as $error) {
				$result = "<br/>\n";
				switch ($error->level) {
					case LIBXML_ERR_WARNING:
						$result .= "<b>Warning $error->code</b>: ";
					break;
					case LIBXML_ERR_ERROR:
						$result .= "<b>Error $error->code</b>: ";
					break;
					case LIBXML_ERR_FATAL:
						$result .= "<b>Fatal Error $error->code</b>: ";
					break;
				}

				$result .= trim($error->message);

				if ($error->file) {
					$result .= " in <b>$error->file</b>";
				}
				$result .= " on line <b>$error->line</b>\n";
			}
			libxml_clear_errors();
		}

		return $result;
	}

	/**
	 * Add new game
	 * Route: /game/add
	 */
	public function addAction()
	{
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only post methods are accepted.');
			return;
		}

		/**
		 * @todo Tester que toutes les valeurs obligatoires sont présentes avec des if(isset($_POST['...']))
		 * Si un champ non présent, retourner un code statut "400" (Bad Request).
		 */

		$gameModel = new Game();
		$gameModel->addGame($_POST);

		$this->sendStatus(204);
	}

	/**
	 * Update game
	 * Route: /game/update
	 */
	public function updateAction()
	{
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only post methods are accepted.');
			return;
		}

		$post = [
			/**
			 * @todo Ajouter tous les paramètres. Prendre exemple sur le controller Analyse.
			 */
		];

		$gameModel = new Game();
		$gameModel->updateGame($post);

		$this->sendStatus(204);
	}

	/**
	 * Delete game
	 * Route: /game/delete/id/{id}
	 *
	 * @param $id int Game's ID to delete
	 */
	public function deleteAction($id)
	{
		if (!$this->getRequestMethod() == 'POST') {
			$this->exitError(405);
		}

		$gameModel = new Game();
		$gameModel->deleteGame((int)$id);

		$this->sendStatus(204);
	}
}