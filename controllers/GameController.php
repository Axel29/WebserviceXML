<?php
class Game extends BaseController
{
	public function indexAction()
	{
		header("Content-type: text/xml; charset=UTF-8");
		
		$games = [
			'1',
			'2',
		];
		$list = $this->generateXml($games);

		echo $list->asXML();
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
			$game = $list->addChild('game');

			$presentation = $game->addChild('presentation');
			$genders = $presentation->addChild('genders');
			$genders->addChild('gender', 'Action');
			$genders->addChild('gender', 'Mélodramatique');
			$presentation->addChild('title', 'ThisIsAVideoGame');
			$editors = $presentation->addChild('editors');
			$editors->addChild('editor', 'An editor');
			$themes = $presentation->addChild('themes');
			$themes->addChild('theme');
			$presentation->addChild('site');

			$consoles = $presentation->addChild('consoles');
			$console  = $consoles->addChild('console');
			$console->addChild('businessModel');
			$console->addChild('pegi');
			$modes = $console->addChild('modes');
			$modes->addChild('mode');
			$cover = $console->addChild('cover');
			$frontCover = $cover->addChild('front');
			$frontCover->addAttribute('url', 'test');
			$backCover = $cover->addChild('back');
			$backCover->addAttribute('url', '/location/to/content');
			$supports = $console->addChild('supports');
			$supports->addChild('support');
			$console->addChild('release');
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
			$test->addChild('date');
			$test->addChild('userName');
			$test->addChild('note');
			$comments = $test->addChild('comments');
			$comment = $comments->addChild('comment');
			$comment->addChild('text');
			$comment->addChild('date');
			$comment->addChild('userName');
			$comment->addChild('note');
			$comment->addChild('like');
			$comment->addChild('dislike');

			$analyses = $test->addChild('analyses');
			$analyse = $analyses->addChild('analyse');
			$analyse->addAttribute('type', 'positive');
			$analyse = $analyses->addChild('analyse');
			$analyse->addAttribute('type', 'negative');

			$languages = $presentation->addChild('languages');
			$languages->addChild('language');

			$articles = $game->addChild('articles');
			$article = $articles->addChild('article');
			$article->addAttribute('type', 'news');
			$articleConsoleNames = $article->addChild('consoleNames');
			$articleConsoleNames->addChild('consoleName');
			$article->addChild('title');
			$article->addChild('userName');
			$article->addChild('date');

			$medias = $game->addChild('medias');
			$media = $medias->addChild('media');
			$media->addAttribute('type', 'video');
			$media->addAttribute('url', 'path/to/media');
			$mediaConsoleNames = $media->addChild('consoleNames');
			$mediaConsoleNames->addChild('consoleName');
			$mediaDimensions = $media->addChild('dimensions');
			$mediaDimensions->addAttribute('unit', 'pt');
			$mediaDimensions->addChild('height');
			$mediaDimensions->addChild('width');

			$tips = $game->addChild('tips');
			$tip = $tips->addChild('tip');
			$tipConsoleNames = $tip->addChild('consoleNames');
			$tipConsoleNames->addChild('consoleNames');
			$tip->addChild('content');
		}

		return $list;
	}
}