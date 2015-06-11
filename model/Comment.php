<?php
class Comment extends BaseModel
{
	/**
	 * Retrieve every available comments or comments by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $comments Collection of comments
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
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

		if ($notPaginated) {
			$limit = '';
		} else {
			$limit = $page - 1 . ', ' . $this->getLimit();
		}

		$comments = $this->select($fields, $where, [], [], [], $limit);

		return $comments;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'date'        => 'date',
			'user_name'   => 'string',
			'note'        => 'int',
			'like'        => 'int',
			'dislike'     => 'int',
			'text'        => 'string',
			'test_idTest' => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new comment in database.
	 *
	 * @param array $datas Comment's datas
	 * @return int|bool $insertComment Comment's ID or false if an error has occurred
	 */
	public function insertComment($datas)
	{
		try {
			$insertedComment = $this->directInsert($datas);
			return $insertedComment;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new comment in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Comment's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedComment Inserted comment's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}

		// Check that the test's ID exists
		$stmt = $pdo->prepare('SELECT `idTest`
							   FROM `test`
							   WHERE `idTest` = :idTest;');
		$stmt->bindParam(':idTest', $datas['test_idTest'], PDO::PARAM_INT);
		$stmt->execute();

		$test = $stmt->fetch();
		if (!count($test) || !isset($test['idTest'])) {
			return false;
		}

		$stmt = $pdo->prepare('INSERT INTO `comment` (`date`, `user_name`, `note`, `like`, `dislike`, `text`, `test_idTest`) 
							   VALUES (:date, :user_name, :note, :like, :dislike, :text, :test_idTest);');
		$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
		$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
		$stmt->bindParam(':note', $datas['note'], PDO::PARAM_INT);
		$stmt->bindParam(':like', $datas['like'], PDO::PARAM_INT);
		$stmt->bindParam(':dislike', $datas['dislike'], PDO::PARAM_INT);
		$stmt->bindParam(':text', $datas['text'], PDO::PARAM_STR);
		$stmt->bindParam(':test_idTest', $datas['test_idTest'], PDO::PARAM_INT);
		$stmt->execute();

		$insertedComment = $pdo->lastInsertId();
		return $insertedComment;
	}

	/**
	 * Update comment
	 *
	 * @param int $idComment Comment's ID
	 * @param array $datas Comment's datas
	 * @return int|bool Updated comment's ID or false if an error has occured
	 */
	public function updateComment($idComment, $datas)
	{
		try {
			$updatedComment = $this->directUpdate($idComment, $datas);
			return $updatedComment;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update a comment without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idComment Comment's ID
	 * @param array $datas Comment's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idComment, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}

		if (isset($datas['test_idTest'])) {
			// Check that the test's ID exists
			$stmt = $pdo->prepare('SELECT `idTest`
								   FROM `test`
								   WHERE `idTest` = :idTest;');
			$stmt->bindParam(':idTest', $datas['test_idTest'], PDO::PARAM_INT);
			$stmt->execute();

			$test = $stmt->fetch();
			if (!count($test) || !isset($test['idTest'])) {
				return false;
			}
		}
		
		// Check that the comment's ID exists
		$stmt = $pdo->prepare('SELECT `idComment`
							   FROM `comment`
							   WHERE `idComment` = :idComment;');
		$stmt->bindParam(':idComment', $idComment, PDO::PARAM_INT);
		$stmt->execute();

		$comment = $stmt->fetch();
		if (!count($comment) || !isset($comment['idComment'])) {
			return false;
		}

		$stmt = $pdo->prepare('UPDATE `comment` 
							   SET `date` = :date,
							       `user_name` = :user_name,
							       `note` = :note,
							       `like` = :like,
							       `dislike` = :dislike,
							       `text` = :text,
							       `test_idTest` = :test_idTest
							   WHERE `idComment` =  :idComment;');
		$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
		$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
		$stmt->bindParam(':note', $datas['note'], PDO::PARAM_INT);
		$stmt->bindParam(':like', $datas['like'], PDO::PARAM_INT);
		$stmt->bindParam(':dislike', $datas['dislike'], PDO::PARAM_INT);
		$stmt->bindParam(':text', $datas['text'], PDO::PARAM_STR);
		$stmt->bindParam(':test_idTest', $datas['test_idTest'], PDO::PARAM_INT);
		$stmt->bindParam(':idComment', $idComment, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete a comment by it's ID
	 *
	 * @param int $idComment Comment's ID
	 * @return int|bool Number of updated rows or false if an error has occurred
	 */
	public function deleteComment($idComment)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `comment` 
								   WHERE `idComment` =  :idComment;');
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