<?php
class Game extends BaseModel
{
	/**
	 * Retrieve every available games or games by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $games Collection of games
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'game';
		
		$fields = [
			'`idGame`',
			'`title`',
			'`site`',
		];

		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		if ($notPaginated) {
			$limit = '';
		} else {
			$entriesPerPage = $this->getLimit();
			$firstEntry     = ($page - 1) * $entriesPerPage;
			$limit          = $firstEntry . ', ' . $entriesPerPage;
		}

		$consoles = $this->select($fields, $where, [], [], [], $limit);

		return $consoles;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'title' => 'string',
			'site'  => 'string',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new game in database.
	 *
	 * @param array $datas Game's datas
	 * @return int|bool $gameId Game's ID or false if an error has occurred
	 */
	public function insertGame($datas)
	{
		$pdo  = $this->db;
		try {
		    // Begin transaction to avoid inserting wrong or partial datas
		    $pdo->beginTransaction();

		    // Insert datas into 'game' table
			$stmt = $pdo->prepare('INSERT INTO `game` (`title`, `site`) 
								   VALUES (:title, :site);');
			$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
			$stmt->bindParam(':site', $datas['site'], PDO::PARAM_STR);
			$stmt->execute();

			$gameId = $pdo->lastInsertId();

			// Insert datas into 'gender' table
			$genderModel = new Gender();
			foreach ($datas['genders'] as $gender) {
				$insertedGender = $genderModel->directInsert($gender, $pdo);

				// Adding relation links
				$stmt = $pdo->prepare('INSERT INTO `game_has_gender` (`game_idGame`, `gender_idGender`) 
									   VALUES (:game_idGame, :gender_idGender);');
				$stmt->bindParam(':game_idGame', $gameId, PDO::PARAM_INT);
				$stmt->bindParam(':gender_idGender', $insertedGender, PDO::PARAM_INT);
				$stmt->execute();
			}

			// Insert datas into 'editor' table
			$editorModel = new Editor();
			foreach ($datas['editors'] as $editor) {
				$insertedEditor = $editorModel->directInsert($editor, $pdo);

				// Adding relation links
				$stmt = $pdo->prepare('INSERT INTO `game_has_editor` (`game_idGame`, `editor_idEditor`) 
									   VALUES (:game_idGame, :editor_idEditor);');
				$stmt->bindParam(':game_idGame', $gameId, PDO::PARAM_INT);
				$stmt->bindParam(':editor_idEditor', $insertedEditor, PDO::PARAM_INT);
				$stmt->execute();
			}

			// Insert datas into 'theme' table
			$themeModel = new Theme();
			foreach ($datas['themes'] as $theme) {
				$insertedTheme = $themeModel->directInsert($theme, $pdo);

				// Adding relation links
				$stmt = $pdo->prepare('INSERT INTO `game_has_theme` (`game_idGame`, `theme_idTheme`) 
									   VALUES (:game_idGame, :theme_idTheme);');
				$stmt->bindParam(':game_idGame', $gameId, PDO::PARAM_INT);
				$stmt->bindParam(':theme_idTheme', $insertedTheme, PDO::PARAM_INT);
				$stmt->execute();
			}

			// Insert datas into 'console' table
			$consoleModel = new Console();
			foreach ($datas['consoles'] as $console) {
				$console['game_idGame'] = $gameId;
				$insertedConsole        = $consoleModel->directInsert($console, $pdo);
			}

			// Insert datas into 'language' table
			$languageModel = new Language();
			foreach ($datas['languages'] as $language) {
				$insertedLanguage = $languageModel->directInsert($language, $pdo);

				// Adding relation links
				$stmt = $pdo->prepare('INSERT INTO `game_has_language` (`game_idGame`, `language_idLanguage`) 
									   VALUES (:game_idGame, :language_idLanguage);');
				$stmt->bindParam(':game_idGame', $gameId, PDO::PARAM_INT);
				$stmt->bindParam(':language_idLanguage', $insertedLanguage, PDO::PARAM_INT);
				$stmt->execute();
			}

			// Insert datas into 'article' table
			$articleModel = new Article();
			foreach ($datas['articles'] as $article) {
				$article['game_idGame'] = $gameId;
				$insertedArticle        = $articleModel->directInsert($article, $pdo);
			}

			// Insert datas into 'media' table
			$mediaModel = new Media();
			foreach ($datas['medias'] as $media) {
				$media['game_idGame'] = $gameId;
				$insertedMedia        = $mediaModel->directInsert($media, $pdo);
			}

			// Insert datas into 'tip' table
			$tipModel = new Tip();
			foreach ($datas['tips'] as $tip) {
				$tip['game_idGame'] = $gameId;
				$insertedTip        = $tipModel->directInsert($tip, $pdo);
			}

			// If everything went well, commit the transaction
			$pdo->commit();

			return $gameId;
		} catch (PDOException $e) {
			// Cancel the transaction
		    $pdo->rollback();
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update game
	 *
	 * @param int $idGame Game's ID
	 * @param array $datas Datas to update
	 * @return bool
	 */
	public function updateGame($idGame, $datas)
	{
		$pdo  = $this->db;

		try {
			// Begin transaction to avoid inserting wrong or partial datas
			$pdo->beginTransaction();

			// Check that the game's ID exists
			$stmt = $pdo->prepare('SELECT `idGame`
								   FROM `game`
								   WHERE `idGame` = :idGame;');
			$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
			$stmt->execute();

			$game = $stmt->fetch();
			if (!count($game) || !isset($game['idGame'])) {
				return false;
			}

			$stmt = $pdo->prepare('UPDATE `game` 
								   SET `title` = :title,
								       `site` = :site
								   WHERE `idGame` = :idGame;');
			$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
			$stmt->bindParam(':site', $datas['site'], PDO::PARAM_STR);
			$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Update datas from 'gender' table
			 */
			$genderModel = new Gender();

			// Stock every gender IDs to remove deleted ones from game_has_gender table
			$genderIds = [];
			foreach ($datas['genders'] as $gender) {
				if (isset($gender['idGender'])) {
					$genderIds[] = (int)$gender['idGender'];
				}
			}

			foreach ($datas['genders'] as $gender) {
				if (isset($gender['idGender'])) {
					$idGender = $gender['idGender'];
				} else {
					$idGender = $genderModel->directInsert($gender, $pdo);
					if (!in_array($idGender, $genderIds)) $genderIds[] = $idGender;
				}

				// Check that the relation link exists, otherwise, we add it.
				$stmt = $pdo->prepare('SELECT *
									   FROM `game_has_gender`
									   WHERE `game_idGame` = :idGame
									   AND gender_idGender = :idGender;');
				$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
				$stmt->bindParam(':idGender', $idGender, PDO::PARAM_INT);
				$stmt->execute();

				$gameHasGender = $stmt->fetch();
				if (!$gameHasGender || !isset($gameHasGender['game_idGame'])) {
					// Adding relation links
					$stmt = $pdo->prepare('INSERT INTO `game_has_gender` (`game_idGame`, `gender_idGender`) 
										   VALUES (:game_idGame, :gender_idGender);');
					$stmt->bindParam(':game_idGame', $idGame, PDO::PARAM_INT);
					$stmt->bindParam(':gender_idGender', $idGender, PDO::PARAM_INT);
					$stmt->execute();
				}

				// Remove deleted links
				$implodedGenderIds = implode("','", $genderIds);
				$stmt = $pdo->prepare("DELETE
									   FROM `game_has_gender`
									   WHERE `game_idGame` = :idGame
									   AND `gender_idGender` NOT IN('" . $implodedGenderIds . "');");
				$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
				$stmt->execute();
			}
			/*
			 * End of genders
			 */

			/*
			 * Update datas from 'editor' table
			 */
			$editorModel = new Editor();

			// Stock every mode IDs to remove deleted ones from game_has_gender table
			$editorIds = [];
			foreach ($datas['editors'] as $editor) {
				if (isset($editor['idEditor'])) {
					$editorIds[] = (int)$editor['idEditor'];
				}
			}

			foreach ($datas['editors'] as $editor) {
				if (isset($editor['idEditor'])) {
					$idEditor = $editor['idEditor'];
				} else {
					$idEditor = $editorModel->directInsert($editor, $pdo);
					if (!in_array($idEditor, $editorIds)) $genderIds[] = $idEditor;
				}

				// Check that the relation link exists, otherwise, we add it.
				$stmt = $pdo->prepare('SELECT *
									   FROM `game_has_editor`
									   WHERE `game_idGame` = :idGame
									   AND editor_idEditor = :idEditor;');
				$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
				$stmt->bindParam(':idEditor', $idEditor, PDO::PARAM_INT);
				$stmt->execute();

				$gameHasEditor = $stmt->fetch();
				if (!$gameHasEditor || !isset($gameHasEditor['game_idGame'])) {
					// Adding relation links
					$stmt = $pdo->prepare('INSERT INTO `game_has_editor` (`game_idGame`, `editor_idEditor`) 
										   VALUES (:game_idGame, :editor_idEditor);');
					$stmt->bindParam(':game_idGame', $idGame, PDO::PARAM_INT);
					$stmt->bindParam(':editor_idEditor', $idEditor, PDO::PARAM_INT);
					$stmt->execute();
				}
			}

			// Remove deleted links
			$implodedEditorIds = implode("','", $editorIds);
			$stmt = $pdo->prepare("DELETE
								   FROM `game_has_editor`
								   WHERE `game_idGame` = :idGame
								   AND `editor_idEditor` NOT IN('" . $implodedEditorIds . "');");
			$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
			$stmt->execute();
			/*
			 * End of editors
			 */

			/*
			 * Update datas from 'theme' table
			 */
			$themeModel = new Theme();

			// Stock every mode IDs to remove deleted ones from game_has_gender table
			$themeIds = [];
			foreach ($datas['themes'] as $theme) {
				if (isset($theme['idTheme'])) {
					$themeIds[] = (int)$theme['idTheme'];
				}
			}

			foreach ($datas['themes'] as $theme) {
				if (isset($theme['idTheme'])) {
					$idTheme = $theme['idTheme'];
				} else {
					$idTheme = $themeModel->directInsert($theme, $pdo);
					if (!in_array($idTheme, $themeIds)) $themeIds[] = $idTheme;
				}

				// Check that the relation link exists, otherwise, we add it.
				$stmt = $pdo->prepare('SELECT *
									   FROM `game_has_theme`
									   WHERE `game_idGame` = :idGame
									   AND theme_idTheme = :idTheme;');
				$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
				$stmt->bindParam(':idTheme', $idTheme, PDO::PARAM_INT);
				$stmt->execute();

				$gameHasTheme = $stmt->fetch();
				if (!$gameHasTheme || !isset($gameHasTheme['game_idGame'])) {
					// Adding relation links
					$stmt = $pdo->prepare('INSERT INTO `game_has_theme` (`game_idGame`, `theme_idTheme`) 
										   VALUES (:game_idGame, :theme_idTheme);');
					$stmt->bindParam(':game_idGame', $idGame, PDO::PARAM_INT);
					$stmt->bindParam(':theme_idTheme', $idTheme, PDO::PARAM_INT);
					$stmt->execute();
				}
			}

			// Remove deleted links
			$implodedThemeIds = implode("','", $themeIds);
			$stmt = $pdo->prepare("DELETE
								   FROM `game_has_theme`
								   WHERE `game_idGame` = :idGame
								   AND `theme_idTheme` NOT IN('" . $implodedThemeIds . "');");
			$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
			$stmt->execute();
			/*
			 * End of themes
			 */

			// Update datas from 'console' table and it's sub-tables
			$consoleModel = new Console();
			foreach ($datas['consoles'] as $console) {
				$console['game_idGame'] = $idGame;
				$updatedConsole         = $consoleModel->directUpdate($console['idConsole'], $console, $pdo);
			}

			/*
			 * Update datas from 'language' table
			 */
			$languageModel = new Language();

			// Stock every mode IDs to remove deleted ones from game_has_gender table
			$languageIds = [];
			foreach ($datas['languages'] as $language) {
				if (isset($language['idLanguage'])) {
					$languageIds[] = (int)$language['idLanguage'];
				}
			}

			foreach ($datas['languages'] as $language) {
				if (isset($language['idLanguage'])) {
					$idLanguage = $language['idLanguage'];
				} else {
					$idLanguage = $languageModel->directInsert($language, $pdo);
					if (!in_array($idLanguage, $languageIds)) $languageIds[] = $idLanguage;
				}

				// Check that the relation link exists, otherwise, we add it.
				$stmt = $pdo->prepare('SELECT *
									   FROM `game_has_language`
									   WHERE `game_idGame` = :idGame
									   AND language_idLanguage = :idLanguage;');
				$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
				$stmt->bindParam(':idLanguage', $idLanguage, PDO::PARAM_INT);
				$stmt->execute();

				$gameHasLanguage = $stmt->fetch();
				if (!$gameHasLanguage || !isset($gameHasLanguage['game_idGame'])) {
					// Adding relation links
					$stmt = $pdo->prepare('INSERT INTO `game_has_language` (`game_idGame`, `language_idLanguage`) 
										   VALUES (:game_idGame, :language_idLanguage);');
					$stmt->bindParam(':game_idGame', $idGame, PDO::PARAM_INT);
					$stmt->bindParam(':language_idLanguage', $idLanguage, PDO::PARAM_INT);
					$stmt->execute();
				}
			}

			// Remove deleted links
			$implodedLanguageIds = implode("','", $languageIds);
			$stmt = $pdo->prepare("DELETE
								   FROM `game_has_language`
								   WHERE `game_idGame` = :idGame
								   AND `language_idLanguage` NOT IN('" . $implodedLanguageIds . "');");
			$stmt->bindParam(':idGame', $idGame, PDO::PARAM_INT);
			$stmt->execute();
			/*
			 * End of languages
			 */

			// Update datas from 'article' table
			$articleModel = new Article();
			foreach ($datas['articles'] as $article) {
				$idArticle              = $article['idArticle'];
				$article['game_idGame'] = $idGame;
				$updatedArticle         = $articleModel->directUpdate($idArticle, $article, $pdo);
			}

			// Update datas from 'media' table
			$mediaModel = new Media();
			foreach ($datas['medias'] as $media) {
				$idMedia                = $media['idMedia'];
				$media['game_idGame']   = $idGame;
				$updatedMedia           = $mediaModel->directUpdate($idMedia, $media, $pdo);
			}

			// Update datas from 'tip' table
			$tipModel = new Tip();
			foreach ($datas['tips'] as $tip) {
				$idTip              = $tip['idTip'];
				$tip['game_idGame'] = $idGame;
				$updatedTip         = $tipModel->directUpdate($idTip, $tip, $pdo);
			}

			// If everything went well, commit the transaction
			$pdo->commit();

			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
			// Cancel the transaction
		    $pdo->rollback();
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Delete a game by it's ID
	 *
	 * @param int $idGame Game's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteGame($idGame)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `game` 
								   WHERE `idGame` = :idGame;');
			$stmt->bindParam(':idGame', $idConsole, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing game.
			 * MySQL won't send any error as, regarding to him, the request is correct, so we have to handle it manually.
			 */
			return $stmt->rowCount();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}