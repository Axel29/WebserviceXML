<?php
class GameController extends BaseController
{
	/**
	 * Show the full XML
	 */
	public function indexAction()
	{
		$gameModel  = new Game();
		$games      = $gameModel->getGames();

		$this->list = $this->generateXml($games);

		if ($errors = $this->validateXML($this->list->asXML())) {
			$this->loadLayout();
			echo $errors;
		} else {
			$this->loadLayout('xml');
			$this->render('index');
		}
	}

	/**
	 * Show a single game's XML by it's ID.
	 *
	 * @param $id int Game's ID
	 */
	public function showAction($id)
	{
		$this->loadLayout('xml');

		$gameModel  = new Game();
		$game       = $gameModel->getGames($id);

		$this->game = $this->generateXml($game);

		$this->render('show');
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
		// echo '<pre>'; var_dump($games); echo '</pre>'; die;
		foreach ($games as $game) {
			$gameNode = $list->addChild('game');

			$presentation = $gameNode->addChild('presentation');
				$genders = $presentation->addChild('genders');
				foreach ($game['genders'] as $gender) {
					$genders->addChild('gender', $gender['gender']);
				}

				$presentation->addChild('title', $game['title']);

				$editors = $presentation->addChild('editors');
				foreach ($game['editors'] as $editor) {
					$editors->addChild('editor', $editor['editor']);
				}

				$themes = $presentation->addChild('themes');
					foreach ($game['themes'] as $theme) {
						$themes->addChild('theme', $theme['theme']);	
					}

				$presentation->addChild('site', $game['site']);

				$consoles = $presentation->addChild('consoles');
				foreach ($game['consoles'] as $console) {
					$console  = $consoles->addChild('console');
					$console->addChild('businessModel', $console['businessModel']);
					$console->addChild('pegi', $console['pegi']);
				}

				$modes = $console->addChild('modes');
				$modes->addChild('mode', $mode['mode']);

				$cover = $console->addChild('cover');
				$frontCover = $cover->addChild('front');
				$frontCover->addAttribute('url', 'test');
				$backCover = $cover->addChild('back');
				$backCover->addAttribute('url', '/location/to/content');
				

				$supports = $console->addChild('supports');
				$supports->addChild('support');

				$console->addChild('release', '2015-01-25');

				$editions = $console->addChild('editions');
				$edition = $editions->addChild('edition');
				$edition->addChild('name');

				$edition->addChild('content');

				$shops = $edition->addChild('shops');
				$shop = $shops->addChild('shop');
				$shop->addAttribute('url', 'test');
				$shop->addChild('name');
				$shopPrice = $shop->addChild('price');
				$shopPrice->addAttribute('devise', 'euro');

				$console->addChild('name');

				$console->addChild('description');

				$dlcs = $console->addChild('dlcs');
				$dlc = $dlcs->addChild('dlc');
				$dlc->addChild('title');
				$dlc->addChild('description');
				$dlcPrice = $dlc->addChild('price');
				$dlcPrice->addAttribute('devise', 'euro');

				$configs = $console->addChild('configs');
				$config = $configs->addChild('config');
				$config->addAttribute('type', 'minimale');
				$config = $configs->addChild('config');
				$config->addAttribute('type', 'optimale');

				$test = $console->addChild('test');
				$test->addChild('report');
				$test->addChild('date', '2015-01-22T11:33:33');
				$test->addChild('userName');
				$test->addChild('note', '1');

				$comments = $test->addChild('comments');
				$comment = $comments->addChild('comment');
				$comment->addChild('text');
				$comment->addChild('date', '2015-01-22T11:33:33');
				$comment->addChild('userName');
				$comment->addChild('note', '3');
				$comment->addChild('like', '0');
				$comment->addChild('dislike', '0');

				$analyses = $test->addChild('analyses');
				$analyse = $analyses->addChild('analyse');
				$analyse->addAttribute('type', 'positive');
				$analyse = $analyses->addChild('analyse');
				$analyse->addAttribute('type', 'negative');

				$languages = $presentation->addChild('languages');
				$languages->addChild('language');
				}

				$articles = $gameNode->addChild('articles');
				$article = $articles->addChild('article');
				$article->addAttribute('type', 'news');
				$articleConsolesNames = $article->addChild('consolesNames');
				$articleConsolesNames->addChild('consoleName');

				$article->addChild('title');
				$article->addChild('userName');
				$article->addChild('date', '2015-01-22T11:33:33');

				$medias = $gameNode->addChild('medias');
				$media = $medias->addChild('media');
				$media->addAttribute('type', 'video');
				$media->addAttribute('url', 'path/to/media');
				$mediaConsolesNames = $media->addChild('consolesNames');
				$mediaConsolesNames->addChild('consoleName');
				$mediaDimensions = $media->addChild('dimensions');
				$mediaDimensions->addAttribute('unit', 'pt');
				$mediaDimensions->addAttribute('height', '32.4');
				$mediaDimensions->addAttribute('width', '90.5');

				$tips = $gameNode->addChild('tips');
				$tip = $tips->addChild('tip');
				$tipConsoleNames = $tip->addChild('consolesNames');
				$tipConsoleNames->addChild('consoleName');
				$tip->addChild('content');
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
}