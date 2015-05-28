<?php
class Game extends BaseModel
{
	/**
	 * @var $gender Gender
	 */
	private $gender;

	/**
	 * @var $analyse Analyse
	 */
	private $analyse;

	/**
	 * Retrieve every game or a specific game with every information filled.
	 *
	 * @param $gameId int Game ID to retrieve
	 * @return $games array
	 */
	public function getGames($gameId = null)
	{
		// $this->debug = true;
		$games = [];

		foreach ($this->getGameInfos($gameId) as $game) {
			$gameId = $game['idGame'];

			// Presentation
			$gender = new Gender();
			$games[$gameId]['presentation']['genders']   = $gender->findBy('idGame', $gameId);
			$games[$gameId]['presentation']['title']     = $game['title'];
			$games[$gameId]['presentation']['editors']   = $this->getGameEditors($gameId);
			$games[$gameId]['presentation']['themes']    = $this->getGameThemes($gameId);
			$games[$gameId]['presentation']['site']      = $game['site'];

			// Consoles
			foreach ($this->getGameConsoles($gameId) as $console) {
				$idConsole = $console['idConsole'];
				$games[$gameId]['presentation']['consoles'][$idConsole]  = $console;

				// Modes
				foreach ($this->getGameModes($idConsole) as $mode) {
					$games[$gameId]['presentation']['consoles'][$idConsole]['modes'][$mode['idMode']] = $mode;
				}

				// Supports
				foreach ($this->getGameSupports($idConsole) as $support) {
					$games[$gameId]['presentation']['consoles'][$idConsole]['supports'][$support['idSupport']] = $support;
				}

				// Editions
				foreach ($this->getGameEditions($idConsole) as $edition) {
					$idEdition = $edition['idEdition'];
					$games[$gameId]['presentation']['consoles'][$idConsole]['editions'][$idEdition] = $edition;

					// Shops
					foreach ($this->getGameShops($idEdition) as $shop) {
						$games[$gameId]['presentation']['consoles'][$idConsole]['editions'][$idEdition]['shops'][$shop['idShop']] = $shop;
					}
				}

				// Dlcs
				foreach ($this->getGameDlcs($idConsole) as $dlc) {
					$games[$gameId]['presentation']['consoles'][$idConsole]['dlcs'][$dlc['idDlc']] = $dlc;
				}

				// Configs
				foreach ($this->getGameConfigs($idConsole) as $config) {
					$games[$gameId]['presentation']['consoles'][$idConsole]['configs'][$config['idConfig']] = $config;
				}

				// Tests
				foreach ($this->getGameTests($idConsole) as $test) {
					$idTest = $test['idTest'];
					$games[$gameId]['presentation']['consoles'][$idConsole]['tests'][$idTest] = $test;

					// Comments
					$comments = new Comment();
					foreach ($comments->findBy('test_idTest', $idTest) as $comment) {
						$games[$gameId]['presentation']['consoles'][$idConsole]['tests'][$idTest]['comments'][$comment['idComment']] = $comment;
					}

					// Analyses
					$analyses = new Analyse();
					foreach ($analyses->findBy('test_idTest', $idTest) as $analyse) {
						$games[$gameId]['presentation']['consoles'][$idConsole]['tests'][$idTest]['analyses'][$analyse['idAnalyse']] = $analyse;
					}
				}
			}
			// End consoles

			// Languages
			foreach ($this->getGameLanguages($gameId) as $language) {
				$games[$gameId]['presentation']['languages'][$language['idLanguage']]  = $language;
			}
			// End prentation

			// Articles
			foreach ($this->getGameArticles($gameId) as $article) {
				$games[$gameId]['articles'][$article['idArticle']]  = $article;
			}
			// End Articles

			// Media
			foreach ($this->getGameMedias($gameId) as $media) {
				$games[$gameId]['medias'][$media['idMedia']]  = $media;
			}
			// End Media

			// Tips
			foreach ($this->getGameTips($gameId) as $tip) {
				$games[$gameId]['tips'][$tip['idTip']]  = $tip;
			}
			// End Tips
			// echo '<pre>'; var_dump($games); echo '</pre>'; die;
		}

		return $games;
	}

	/**
	 * Retrieve main informations for every game or a specific game.
	 *
	 * @param $gameId int Game's ID to retrieve if not list mode.
	 * @return $gameInfos array Game object
	 */
	public function getGameInfos($gameId = null)
	{
		$this->table = 'game g';

		$fields = ['`idGame`', '`title`', '`site`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				'g.idGame' => $gameId,
			];
		}

		$gameInfos = $this->select($fields, $where);

		return $gameInfos;
	}

	/**
	 * Retrieve every available articles or articles by game ID
	 *
	 * @param $gameId int Game's ID
	 * @return $articles array
	 */
	public function getGameArticles($gameId = null)
	{
		$this->table = 'article a';
		
		$fields = ['`idArticle`', '`type`', '`title`', '`user_name`', 'DATE_FORMAT(`date`, "%Y-%m-%dT%H:%i:%s") as `date`', '`console_names`', '`game_idGame`'];
		$where  = [];
		if ($gameId) {
			$where = [
				'a.game_idGame' => $gameId,
			];
		}

		$articles = $this->select($fields, $where);

		return $articles;
	}

	/**
	 * Retrieve every available configs or configs by consoleId ID
	 *
	 * @param $consoleId int Console's ID attached to the config
	 * @return $configs array
	 */
	public function getGameConfigs($consoleId = null)
	{
		$this->table = 'config c';

		$where = [];
		if ($consoleId) {
			$where = [
				'c.console_idConsole' => $consoleId,
			];
		}

		$configs = $this->select(['*'], $where);

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
		$this->table = 'console c';

		$where = [];
		if ($gameId) {
			$where = [
				'c.game_idGame' => $gameId,
			];
		}

		$consoles = $this->select(['*'], $where);

		return $consoles;
	}

	/**
	 * Retrieve every available dlcs or dlcs by console ID
	 *
	 * @param $consoleId int Console's ID attached to the dlc
	 * @return $dlcs array
	 */
	public function getGameDlcs($consoleId = null)
	{
		$this->table = 'dlc d';
		
		$where = [];
		if ($consoleId) {
			$where = [
				'd.console_idConsole' => $consoleId,
			];
		}

		$dlcs = $this->select(['*'], $where);

		return $dlcs;
	}

	/**
	 * Retrieve every available editions or editions by game ID
	 *
	 * @param $consoleId int Console's ID attached to the edition
	 * @return $editions array
	 */
	public function getGameEditions($consoleId = null)
	{
		$this->table = 'edition e';
		
		$where = [];
		if ($consoleId) {
			$where = [
				'e.console_idConsole' => $consoleId,
			];
		}

		$editions = $this->select(['*'], $where);

		return $editions;
	}

	/**
	 * Retrieve every available edtiors or edtiors by game ID
	 *
	 * @param $gameId int Game's ID attached to the editor
	 * @return $edtiors array
	 */
	public function getGameEditors($gameId = null)
	{
		$this->table = 'editor e';

		$fields = ['`idEditor`', '`editor`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghe.game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'game_has_editor ghe',
					'on'    => 'e.idEditor = ghe.game_idGame',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'game g',
					'on'    => 'g.idGame = ghe.game_idGame',
				],
			];
		}

		$genders = $this->select($fields, $where, [], $join);

		return $genders;
	}

	/**
	 * Retrieve every available languages or languages by game ID
	 *
	 * @param $gameId int Game's ID attached to the language
	 * @return $languages array
	 */
	public function getGameLanguages($gameId = null)
	{
		$this->table = 'language l';

		$fields = ['`idLanguage`', '`language`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ghl.game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'game_has_language ghl',
					'on'    => 'l.idLanguage = ghl.language_idLanguage',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'game g',
					'on'    => 'g.idGame = ghl.game_idGame',
				],
			];
		}

		$languages = $this->select($fields, $where, [], $join);

		return $languages;
	}

	/**
	 * Retrieve every available medias or medias by game ID
	 *
	 * @param $gameId in Game's ID attached to the media
	 * @return $medias array
	 */
	public function getGameMedias($gameId = null)
	{
		$this->table = 'media m';
		
		$where = [];
		if ($gameId) {
			$where = [
				'm.game_idGame' => $gameId,
			];
		}

		$medias = $this->select(['*'], $where);

		return $medias;
	}

	/**
	 * Retrieve every available modes or modes by console ID
	 *
	 * @param $consoleId int Console's ID attached to the mode
	 * @return $modes array
	 */
	public function getGameModes($consoleId = null)
	{
		$this->table = 'mode m';

		$fields = ['`idMode`', '`mode`'];
		
		$where = [];
		$join  = [];
		if ($consoleId) {
			$where = [
				'chm.console_idConsole' => $consoleId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'console_has_mode chm',
					'on'    => 'm.idMode = chm.mode_idMode',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'console c',
					'on'    => 'c.idConsole = chm.console_idConsole',
				],
			];
		}

		$modes = $this->select($fields, $where, [], $join);

		return $modes;
	}

	/**
	 * Retrieve every available shops or shops by edition ID
	 *
	 * @param $editionId in Edition's ID attached to the shop
	 * @return $shops array
	 */
	public function getGameShops($editionId = null)
	{
		$this->table = 'shop s';
		
		$where = [];
		if ($editionId) {
			$where = [
				's.edition_idEdition' => $editionId,
			];
		}

		$shops = $this->select(['*'], $where);

		return $shops;
	}

	/**
	 * Retrieve every available supports or supports by console ID
	 *
	 * @param $consoleId int Console's ID attached to the support
	 * @return $supports array
	 */
	public function getGameSupports($consoleId = null)
	{
		$this->table = 'support s';

		$fields = ['`idSupport`', '`support`'];
		
		$where = [];
		$join  = [];
		if ($consoleId) {
			$where = [
				'chs.console_idConsole' => $consoleId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'console_has_support chs',
					'on'    => 's.idSupport = chs.support_idSupport',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'console c',
					'on'    => 'c.idConsole = chs.console_idConsole',
				],
			];
		}

		$supports = $this->select($fields, $where, [], $join);

		return $supports;
	}

	/**
	 * Retrieve every available tests or tests by consle ID
	 *
	 * @param $consoleId int Console's ID attached to the test
	 * @return $tests array
	 */
	public function getGameTests($consoleId = null)
	{
		$this->table = 'test t';
		
		$fields = ['`idTest`', '`report`', 'DATE_FORMAT(`date`, "%Y-%m-%dT%H:%i:%s") as `date`', '`user_name`', '`note`', '`console_idConsole`'];
		$where  = [];
		if ($consoleId) {
			$where = [
				't.console_idConsole' => $consoleId,
			];
		}

		$tests = $this->select($fields, $where);

		return $tests;
	}

	/**
	 * Retrieve every available themes or themes by game ID
	 *
	 * @param $gameId int Game's ID attached to the theme
	 * @return $themes array
	 */
	public function getGameThemes($gameId = null)
	{
		$this->table = 'theme t';

		$fields = ['`idTheme`', '`theme`'];
		
		$where = [];
		$join  = [];
		if ($gameId) {
			$where = [
				'ght.game_idGame' => $gameId,
			];

			$join = [
				[
					'type'  => 'INNER JOIN',
					'table' => 'game_has_theme ght',
					'on'    => 't.idTheme = ght.theme_idTheme',
				],
				[
					'type'  => 'INNER JOIN',
					'table' => 'game g',
					'on'    => 'g.idGame = ght.game_idGame',
				],
			];
		}

		$themes = $this->select($fields, $where, [], $join);

		return $themes;
	}

	/**
	 * Retrieve every available tips or tips by game ID
	 *
	 * @param $gameId int Game's ID attached to the tip
	 * @return $tips array
	 */
	public function getGameTips($gameId = null)
	{
		$this->table = 'tip t';

		$fields = ['`idTip`', '`content`', '`console_names`'];
		
		$where = [];
		if ($gameId) {
			$where = [
				't.game_idGame' => $gameId,
			];
		}

		$tips = $this->select($fields, $where);

		return $tips;
	}

	/**
	 * Add new game
	 *
	 * @param $post array Datas for the new game
	 */
	public function addGame($post)
	{
		try {
			$pdo = $this->db;
			
		    // Begin transaction to avoid inserting wrong partial datas
		    $pdo->beginTransaction();

		    // Insert game
		    $stmt = $pdo->prepare('INSERT INTO game (`title`, `site`) VALUES (:title, :site)');
		    $stmt->bindParam(':title', $post['game']['title']);
			$stmt->bindParam(':site', $post['game']['site']);
			$stmt->execute();

		    // Insert game
		    $stmt = $pdo->prepare('INSERT INTO game (`title`, `site`) VALUES (:title, :site)');
		    $stmt->bindParam(':title', $post['game']['title']);
			$stmt->bindParam(':site', $post['game']['site']);
			$stmt->execute();

		    // Insert game
		    $stmt = $pdo->prepare('INSERT INTO game (`title`, `site`) VALUES (:title, :site)');
		    $stmt->bindParam(':title', $post['game']['title']);
			$stmt->bindParam(':site', $post['game']['site']);
			$stmt->execute();

		    // Insert game
		    $stmt = $pdo->prepare('INSERT INTO game (`title`, `site`) VALUES (:title, :site)');
		    $stmt->bindParam(':title', $post['game']['title']);
			$stmt->bindParam(':site', $post['game']['site']);
			$stmt->execute();

		    // Insert game
		    $stmt = $pdo->prepare('INSERT INTO game (`title`, `site`) VALUES (:title, :site)');
		    $stmt->bindParam(':title', $post['game']['title']);
			$stmt->bindParam(':site', $post['game']['site']);
			$stmt->execute();

		    // If everything went well, commit the transaction
		    $pdo->commit();
		} catch (Exception $e) {
		    // Cancel the transaction
		    $pdo->rollback();

		    $errors = $e->getMessage();
		    return $errors;
		}

		if ($insertedGame) {
			// Insertion of the gender
			$this->debug = true;
			$this->table    = 'gender';
			$insertedGender = $this->insert(['gender' => $post['genders']['gender']]);

			if ($insertedGender) {
				$this->table    = 'game_has_gender';
				$fields = [
					'game_idGame'     => $insertedGame,
					'gender_idGender' => $insertedGender,
				];
				$this->insert($fields);
			} else {
				return false;
			}

			//insertion of the editor
			$this->table    = 'editor';
			$insertedEditor = $this->insert(['editor' => $post['editors']['editor']]);

			if ($insertedEditor) {
				$this->table    = 'game_has_editor';
				$fields = [
					'game_idGame'     => $insertedGame,
					'editor_idEditor' => $insertedEditor,
				];
				$this->insert($fields);
			} else {
				return false;
			}

			//insertion of the theme
			$this->table    = 'theme';
			$insertedTheme = $this->insert(['theme' => $post['themes']['theme']]);

			if ($insertedTheme) {
				$this->table    = 'game_has_theme';
				$fields = [
					'game_idGame'     => $insertedGame,
					'theme_idTheme' => $insertedTheme,
				];
				$this->insert($fields);
			} else {
				return false;
			}


			//insertion of the console
			$this->table    = 'console';
			$fields = [
				'business_model' => $post['consoles']['business_model'],
				'pegi' => $post['consoles']['pegi'],
				'release' => $post['consoles']['release'],
				'name' => $post['consoles']['name'],
				'description' => $post['consoles']['description'],
				'cover_front' => $post['consoles']['cover_front'],
				'cover_back' => $post['consoles']['cover_back'],
				'game_idGame' => $insertedGame,
			];
			$insertedConsole = $this->insert($fields);

			if ($insertedConsole) {

				$this->table    = 'mode';
				$fields = ['mode' => $post['modes']['name']];

				$insertedMode = $this->insert($fields);

				if ($insertedMode) {

					$this->table    = 'support';
					$fields = ['support' => $post['supports']['support']];

					$insertedSupport = $this->insert($fields);

					if ($insertedSupport) {

						$this->table    = 'edition';
						$fields = [
							'name' => $post['editions']['name'],
							'content' => $post['editions']['content'],
							];

						$insertedEdition = $this->insert($fields);

							if ($insertedEdition) {

							if(isset($post['shop'])){
								$this->table    = 'shop';
								$fields = [
									'url' => $post['shops']['url'],
									'name' => $post['shops']['name'],
									'price' => $post['shops']['price'],
									'devise' => $post['shops']['devise'],
									'edition_idEdition' => $insertedEdition,
									];

								$insertedShop = $this->insert($fields);
							}

							if (isset($post['shop']) && $insertedShop || !isset($post['shop'])) {

								$this->table    = 'dlc';
								$fields = [
									'title' => $post['dlcs']['title'],
									'description' => $post['dlcs']['description'],
									'price' => $post['dlcs']['price'],
									'devise' => $post['dlcs']['devise'],
									'console_idConsole' => $insertedConsole,
									];

								$insertedDlc = $this->insert($fields);

								if ($insertedDlc) {

									$this->table    = 'config';
									$fields = [
										'config' => $post['configs']['config'],
										'type' => $post['configs']['type'],
										];

									$insertedConfig = $this->insert($fields);

									if ($insertedConfig) {
										$this->table    = 'console_has_config';
										$fields = [
											'console_idConsole'     => $insertedConsole,
											'config_idConfig' => $insertedConfig,
										];
										$this->insert($fields);


										if ($insertedConfig) {
											$this->table    = 'test';
											$fields = [
												'report'     => $post['tests']['report'],
												'date' => $post['tests']['date'],
												'user_name' => $post['tests']['user_name'],
												'note' => $post['tests']['note'],
												'console_idConsole' => $insertedConsole,
											];

											$insertedTest = $this->insert($fields);

											if ($insertedTest) {
												$this->table    = 'language';
												$fields = [
													'language'     => $post['languages']['language'],
												];

												$insertedLanguage = $this->insert($fields);

												if ($insertedLanguage) {
													$this->table    = 'game_has_language';
													$fields = [
														'game_idGame'     => $insertedGame,
														'language_idLanguage' => $insertedLanguage,
													];
													$this->insert($fields);


													if ($insertedLanguage) {
														$this->table    = 'article';
														$fields = [
															'type'     => $post['articles']['type'],
															'title' => $post['articles']['title'],
															'user_name' => $post['articles']['user_name'],
															'date' => $post['articles']['date'],
															'console_names' => $post['articles']['console_names'],
															'game_idGame' => $insertedGame,
														];

														$insertedArticle = $this->insert($fields);

																	if ($insertedArticle) {
																		$this->table    = 'media';
																		$fields = [
																			'type'     => $post['medias']['type'],
																			'url' => $post['medias']['url'],
																			'unit' => $post['medias']['unit'],
																			'width' => $post['medias']['width'],
																			'height' => $post['medias']['height'],
																			'console_names' => $post['medias']['console_names'],
																			'game_idGame' => $insertedGame,
																		];

																		$insertedMedia = $this->insert($fields);


																			if ($insertedMedia) {
																				$this->table    = 'tip';
																				$fields = [
																					'content'     => $post['tips']['content'],																		
																					'console_names' => $post['tips']['console_names'],
																					'game_idGame' => $insertedGame,
																				];

																				$insertedTip = $this->insert($fields);

																				} else {
																					return false;
																				}

																		} else {
																			return false;
																		}

														} else {
															return false;
														}

												} else {
													return false;
												}

											} else {
												return false;
											}

										} else {
											return false;
										}

									} else {
										return false;
									}

								} else {
									return false;
								}

							} else {
								return false;
							}

						} else {
							return false;
						}

					} else {
						return false;
					}

				} else {
					return false;
				}

				
			} else {
				return false;
			}


		} else {
			return false;
		}

	}

	/**
	 * Update game
	 *
	 * @param $gameId int Game ID to retrieve
	 */
	public function updateGame($post)
	{
		/**
		 * @todo Tester que toutes les valeurs obligatoires sont présentes avec des if(isset($post['...']))
		 */
		if (isset($post['idGame'])) {
			$this->table = 'game';

			$fields = [
				/**
				 * @todo Ajouter tous les champs avec la structure suivante : cle => valeur (ou clé = nom du champ en BDD)
				 */
			];

			$where = ['idGame' => $post['idGame']];

			$this->update($fields, $where);
		} else {
			return false;
		}
	}

	/**
	 * Delete a game by it's ID
	 *
	 * @param $id int Game's ID
	 */
	public function deleteGame($id)
	{
		$this->table = 'game';
		$this->delete(['idGame' => (int)$id]);
	}
}