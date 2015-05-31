<?php
class Game extends BaseModel
{
	/**
	 * Retrieve every available games or games by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $games Collection of games
	 */
	public function findBy($paramName = null, $paramValue = null)
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

		$consoles = $this->select($fields, $where);

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