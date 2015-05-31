<?php
class Article extends BaseModel
{
	/**
	 * Retrieve every available articles or articles by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $articles Collection of articles as array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'article';

		$fields = [
			'`idArticle`', 
			'`type`', 
			'`title`', 
			'`user_name`', 
			'DATE_FORMAT(`date`, "%Y-%m-%dT%H:%i:%s") as `date`', 
			'`console_names`', 
			'`game_idGame`'
		];
		
		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		$articles = $this->select($fields, $where, [], $join);

		return $articles;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'type'          => 'string',
			'title'         => 'string',
			'user_name'     => 'string',
			'date'          => 'date',
			'console_names' => 'string',
			'game_idGame'   => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new article in database.
	 *
	 * @param array $datas Article's datas
	 * @return int|bool $insertedArticle Article's ID or false if an error has occurred
	 */
	public function insertArticle($datas)
	{
		try {
			$insertedArticle = $this->directInsert($datas);
			return $insertedArticle;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new article in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Article's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedArticle Inserted article's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('INSERT INTO `article` (`type`, `title`, `user_name`, `date`, `console_names`, `game_idGame`) 
							   VALUES (:type, :title, :user_name, :date, :console_names, :game_idGame);');
		$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
		$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
		$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
		$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
		$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
		$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
		$stmt->execute();

		$insertedArticle = $pdo->lastInsertId();
		return $insertedArticle;
	}

	/**
	 * Update article
	 *
	 * @param int $idArticle Article's ID
	 * @param array $datas Article's datas
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function updateArticle($idArticle, $datas)
	{
		try {
			return $this->directUpdate($idArticle, $datas);
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update an article without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idArticle Article's ID
	 * @param array $datas Article's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idArticle, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `article` 
							   SET `type`          = :type,
								   `title`         = :title,
								   `user_name`     = :user_name,
								   `date`          = :date,
								   `console_names` = :console_names,
								   `game_idGame`   = :game_idGame
							   WHERE `idArticle` =  :idArticle;');
		$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
		$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
		$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
		$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
		$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
		$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
		$stmt->bindParam(':idArticle', $idArticle, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete an article by it's ID
	 *
	 * @param $id int Article's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteArticle($idArticle)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `article` 
								   WHERE `idArticle` =  :idArticle;');
			$stmt->bindParam(':idArticle', $idArticle, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing article.
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