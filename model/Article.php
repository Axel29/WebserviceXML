<?php
class Article extends BaseModel
{
	/**
	 * Retrieve every available articles or articles by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $articles array
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
	 * Insert a new article in database.
	 * If the article already exists, return the existing article's ID.
	 *
	 * @param $datas string Article's name
	 * @return $id int Article's ID
	 */
	public function insertArticle($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `article` (`type`, `title`, `user_name`, `date`, `console_names`, `game_idGame`) 
								   VALUES (:type, :title, :user_name, :date, :console_names, :game_idGame)');
			$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
			$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
			$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
			$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
			$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
			$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
			$stmt->execute();

			return $pdo->lastInsertId();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update article
	 *
	 * @param $idArticle int Article's ID
	 * @param $article string Article's name
	 */
	public function updateArticle($idArticle, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `article` 
								   SET `type`          = :type,
									   `title`         = :title,
									   `user_name`     = :user_name,
									   `date`          = :date,
									   `console_names` = :console_names,
									   `game_idGame`   = :game_idGame
								   WHERE `idArticle` =  :idArticle');
			$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
			$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
			$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
			$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
			$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
			$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
			$stmt->bindParam(':idArticle', $idArticle, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing article.
			 * MySQL won't send any error as, regarding to him, the request is correct, so we have to handle it manually.
			 */
			return $stmt->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage(); die;
			return false;
		} catch (Exception $e) {
			echo $e->getMessage(); die;
			return false;
		}
	}

	/**
	 * Delete an article by it's ID
	 *
	 * @param $id int Article's ID
	 */
	public function deleteArticle($idArticle)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `article` 
								   WHERE `idArticle` =  :idArticle');
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