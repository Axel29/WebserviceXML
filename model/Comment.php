<?php
class Comment extends BaseModel
{
	/**
	 * Retrieve every available comments or comments by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $comments array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'comment';
		
		$fields = [
			'idComment',
			'DATE_FORMAT(`date`, "%Y-%m-%dT%H:%i:%s") as `date`',
			'`user_name`',
			'`note`',
			'`like`',
			'`dislike`',
			'`text`',
			'`test_idTest`',
		];

		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		$comments = $this->select($fields, $where);

		return $comments;
	}

	/**
	 * Insert a new comment in database.
	 *
	 * @param $datas array Comment's name
	 * @return $id int Comment's ID
	 */
	public function insertComment($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `comment` (`date`, `user_name`, `note`, `like`, `dislike`, `text`, `test_idTest`) 
								   VALUES (:date, :user_name, :note, :like, :dislike, :text, :test_idTest)');
			$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
			$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
			$stmt->bindParam(':note', $datas['note'], PDO::PARAM_INT);
			$stmt->bindParam(':like', $datas['like'], PDO::PARAM_INT);
			$stmt->bindParam(':dislike', $datas['dislike'], PDO::PARAM_INT);
			$stmt->bindParam(':text', $datas['text'], PDO::PARAM_STR);
			$stmt->bindParam(':test_idTest', $datas['test_idTest'], PDO::PARAM_INT);
			$stmt->execute();

			return $pdo->lastInsertId();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update comment
	 *
	 * @param $idComment int Comment's ID
	 * @param $datas array Datas to update
	 */
	public function updateComment($idComment, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `comment` 
								   SET `date` = :date,
								       `user_name` = :user_name,
								       `note` = :note,
								       `like` = :like,
								       `dislike` = :dislike,
								       `text` = :text,
								       `test_idTest` = :test_idTest
								   WHERE `idComment` =  :idComment');
			$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
			$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
			$stmt->bindParam(':note', $datas['note'], PDO::PARAM_INT);
			$stmt->bindParam(':like', $datas['like'], PDO::PARAM_INT);
			$stmt->bindParam(':dislike', $datas['dislike'], PDO::PARAM_INT);
			$stmt->bindParam(':text', $datas['text'], PDO::PARAM_STR);
			$stmt->bindParam(':test_idTest', $datas['test_idTest'], PDO::PARAM_INT);
			$stmt->bindParam(':idComment', $idComment, PDO::PARAM_INT);
			$stmt->execute();
			/*
			 * Check that the update was performed on an existing comment.
			 * MySQL won't send any error as, regarding to him, the request is correct, so we have to handle it manually.
			 */
			return $stmt->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
			return false;
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	/**
	 * Delete a comment by it's ID
	 *
	 * @param $id int Comment's ID
	 */
	public function deleteComment($idComment)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `comment` 
								   WHERE `idComment` =  :idComment');
			$stmt->bindParam(':idComment', $idComment, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing comment.
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