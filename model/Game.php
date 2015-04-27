<?php
class Game extends BaseModel
{
	/**
	 * Retrieve every game or a specific game with every information filled.
	 *
	 * @param $gameId int Game ID to retrieve
	 * @return $games array
	 */
	public function getGames($gameId = null)
	{
		$games = [];

		foreach ($this->getGameInfos($gameId) as $game) {
			$gameId = $game['idGame'];

			$games[$gameId]['title']     = $game['title'];
			$games[$gameId]['site']      = $game['site'];
			$games[$gameId]['analyses']  = $this->getGameAnalyses($gameId);
			$games[$gameId]['articles']  = $this->getGameArticles($gameId);
			$games[$gameId]['comments']  = $this->getGameComments($gameId);
			$games[$gameId]['config']    = $this->getGameConfigs($gameId);
			$games[$gameId]['consoles']  = $this->getGameConsoles($gameId);
			$games[$gameId]['dlcs']      = $this->getGameDlcs($gameId);
			$games[$gameId]['editions']  = $this->getGameEditions($gameId);
			$games[$gameId]['editors']   = $this->getGameEditors($gameId);
			$games[$gameId]['genders']   = $this->getGameGenders($gameId);
			$games[$gameId]['languages'] = $this->getGameLanguages($gameId);
			$games[$gameId]['medias']    = $this->getGameMedias($gameId);
			$games[$gameId]['modes']     = $this->getGameModes($gameId);
			$games[$gameId]['shops']     = $this->getGameShops($gameId);
			$games[$gameId]['supports']  = $this->getGameSupports($gameId);
			$games[$gameId]['themes']    = $this->getGameThemes($gameId);
			$games[$gameId]['tips']      = $this->getGameTips($gameId);
		}

		return $games;
	}

	/**
	 * Retrieve main informations for every game or a specific game.
	 *
	 * @param $gameId int Game's ID to retrieve if not list mode.
	 * @return $game array Game object
	 */
	public function getGameInfos($gameId = null)
	{
		$this->table = 'Game g';

		$fields = ['`idGame`', '`title`', '`site`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				'g.idGame' => $gameId,
			];
		}

		$analyses = $this->select($fields, $where);

		return $analyses;
	}

	/**
	 * Retrieve every available analyses or analyses by game ID
	 *
	 * @param $gameId in Game's ID
	 * @return $analyses array
	 */
	public function getGameAnalyses($gameId = null)
	{
		$this->table = 'Analyses a';

		$fields = ['`analyse`', '`type`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				'a.Game_idGame' => $gameId,
			];
		}

		$analyses = $this->select($fields, $where);

		return $analyses;
	}

	/**
	 * Retrieve every available articles or articles by game ID
	 *
	 * @param $gameId in Game's ID
	 * @return $articles array
	 */
	public function getGameArticles($gameId = null)
	{
		$this->table = 'Articles a';

		$fields = ['`title`', '`userName`', '`date`', '`consoleNames`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				'a.Game_idGame' => $gameId,
			];
		}

		$articles = $this->select($fields, $where);

		return $articles;
	}

	/**
	 * Retrieve every available comments or comments by game ID
	 *
	 * @param $gameId in Game's ID
	 * @return $comments array
	 */
	public function getGameComments($gameId = null)
	{
		$this->table = 'Comments c';

		$fields = ['`date`', '`userName`', '`note`', '`like`', '`dislike`', '`texte`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				'c.Game_idGame' => $gameId,
			];
		}

		$comments = $this->select($fields, $where);

		return $comments;
	}

	/**
	 * Retrieve every available configs or configs by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $configs array
	 */
	public function getGameConfigs($gameId = null)
	{
		$this->table = 'Configs c';

		$fields = ['config', 'type'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghc.Game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game_has_Configs ghc',
					'on'    => 'c.idConfigs = ghc.Configs_idConfigs',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game g',
					'on'    => 'g.idGame = ghc.Game_idGame',
				],
			];
		}

		$configs = $this->select($fields, $where, [], $join);

		return $configs;
	}

	/**
	 * Retrieve every available consoles or consoles by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $consoles array
	 */
	public function getGameConsoles($gameId = null)
	{
		$this->table = 'Consoles c';

		$fields = ['`businessModel`', '`pegi`', '`release`', '`name`', '`description`', '`testReport`', '`testDate`', '`testUserName`', '`testNote`', '`coverFront`', '`coverBack`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghc.Game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game_has_Consoles ghc',
					'on'    => 'c.idConsoles = ghc.Consoles_idConsoles',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game g',
					'on'    => 'g.idGame = ghc.Game_idGame',
				],
			];
		}

		$consoles = $this->select($fields, $where, [], $join);

		return $consoles;
	}

	/**
	 * Retrieve every available dlcs or dlcs by game ID
	 *
	 * @param $gameId in Game's ID
	 * @return $dlcs array
	 */
	public function getGameDlcs($gameId = null)
	{
		$this->table = 'dlcs d';

		$fields = ['`title`', '`description`', '`price`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				'd.Game_idGame' => $gameId,
			];
		}

		$dlcs = $this->select($fields, $where);

		return $dlcs;
	}

	/**
	 * Retrieve every available editions or editions by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $editions array
	 */
	public function getGameEditions($gameId = null)
	{
		$this->table = 'Editions e';

		$fields = ['`name`', '`content`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghe.Game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game_has_Editions ghe',
					'on'    => 'e.idEditions = ghe.Editions_idEditions',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game g',
					'on'    => 'g.idGame = ghe.Game_idGame',
				],
			];
		}

		$editions = $this->select($fields, $where, [], $join);

		return $editions;
	}

	/**
	 * Retrieve every available edtiors or edtiors by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $edtiors array
	 */
	public function getGameEditors($gameId = null)
	{
		$this->table = 'Editors e';

		$fields = ['`editor`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghe.Game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game_has_Editors ghe',
					'on'    => 'e.idEditors = ghe.Editors_idEditors',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game g',
					'on'    => 'g.idGame = ghe.Game_idGame',
				],
			];
		}

		$edtiors = $this->select($fields, $where, [], $join);

		return $edtiors;
	}

	/**
	 * Retrieve every available genders or genders by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $genders array
	 */
	public function getGameGenders($gameId = null)
	{
		$this->table = 'Genders gender';

		$fields = ['`gender`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghg.Game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game_has_Genders ghg',
					'on'    => 'gender.idGenders = ghg.Genders_idGenders',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game g',
					'on'    => 'g.idGame = ghg.Game_idGame',
				],
			];
		}

		$genders = $this->select($fields, $where, [], $join);

		return $genders;
	}

	/**
	 * Retrieve every available languages or languages by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $languages array
	 */
	public function getGameLanguages($gameId = null)
	{
		$this->table = 'Languages l';

		$fields = ['`language`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghl.Game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game_has_Languages ghl',
					'on'    => 'l.idLanguages = ghl.Languages_idLanguages',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game g',
					'on'    => 'g.idGame = ghl.Game_idGame',
				],
			];
		}

		$languages = $this->select($fields, $where, [], $join);

		return $languages;
	}

	/**
	 * Retrieve every available medias or medias by game ID
	 *
	 * @param $gameId in Game's ID
	 * @return $medias array
	 */
	public function getGameMedias($gameId = null)
	{
		$this->table = 'Medias m';

		$fields = ['`type`', '`url`', '`unit`', '`width`', '`height`', '`consoleNames`', '`Game_idGame`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				'm.Game_idGame' => $gameId,
			];
		}

		$medias = $this->select($fields, $where);

		if (!count($medias)) {
			return false;
		} else {
			return $medias;
		}
	}

	/**
	 * Retrieve every available modes or modes by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $modes array
	 */
	public function getGameModes($gameId = null)
	{
		$this->table = 'Modes m';

		$fields = ['`mode`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghm.Game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game_has_Modes ghm',
					'on'    => 'm.idModes = ghm.Modes_idModes',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game g',
					'on'    => 'g.idGame = ghm.Game_idGame',
				],
			];
		}

		$modes = $this->select($fields, $where, [], $join);

		return $modes;
	}

	/**
	 * Retrieve every available shops or shops by game ID
	 *
	 * @param $gameId in Game's ID
	 * @return $shops array
	 */
	public function getGameShops($gameId = null)
	{
		$this->table = 'Shops s';

		$fields = ['`shop`', '`price`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				's.Game_idGame' => $gameId,
			];
		}

		$shops = $this->select($fields, $where);

		return $shops;
	}

	/**
	 * Retrieve every available supports or supports by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $supports array
	 */
	public function getGameSupports($gameId = null)
	{
		$this->table = 'Supports s';

		$fields = ['`support`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghs.Game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game_has_Supports ghs',
					'on'    => 's.idSupports = ghs.Supports_idSupports',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game g',
					'on'    => 'g.idGame = ghs.Game_idGame',
				],
			];
		}

		$supports = $this->select($fields, $where, [], $join);

		return $supports;
	}

	/**
	 * Retrieve every available themes or themes by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $themes array
	 */
	public function getGameThemes($gameId = null)
	{
		$this->table = 'Themes t';

		$fields = ['`theme`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ght.Game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game_has_Themes ght',
					'on'    => 't.idThemes = ght.Themes_idThemes',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'Game g',
					'on'    => 'g.idGame = ght.Game_idGame',
				],
			];
		}

		$themes = $this->select($fields, $where, [], $join);

		return $themes;
	}

	/**
	 * Retrieve every available tips or tips by game ID
	 *
	 * @param $gameId in Game's ID
	 * @return $tips array
	 */
	public function getGameTips($gameId = null)
	{
		$this->table = 'Tips t';

		$fields = ['`content`', '`consoleNames`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				't.Game_idGame' => $gameId,
			];
		}

		$tips = $this->select($fields, $where);

		return $tips;
	}
}