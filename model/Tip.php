<?php
class Tip extends BaseModel
{
	/**
	 * Retrieve every available tips or tips by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $tips Collection of tips
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'tip';

		$fields = [
			'`idTip`', 
			'`content`', 
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

		if ($notPaginated) {
			$limit = '';
		} else {
			$limit = $page - 1 . ', ' . $this->getLimit();
		}

		$tips = $this->select($fields, $where, [], $join, [], $limit);

		return $tips;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'content'       => 'string',
			'console_names' => 'string',
			'game_idGame'   => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new tip in database.
	 *
	 * @param array $datas Tip's datas
	 * @return int $insertedTip Tip's ID
	 */
	public function insertTip($datas)
	{
		try {
			$insertedTip = $this->directInsert($datas);
			return $insertedTip;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new language in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Language's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedLanguage Inserted language's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('INSERT INTO `tip` (`content`, `console_names`, `game_idGame`) 
							   VALUES (:content, :console_names, :game_idGame);');
		$stmt->bindParam(':content', $datas['content'], PDO::PARAM_STR);
		$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
		$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
		$stmt->execute();

		$insertedTip = $pdo->lastInsertId();
		return $insertedTip;
	}

	/**
	 * Update tip
	 *
	 * @param int $idTip Tip's ID
	 * @param array $datas Tip's datas
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function updateTip($idTip, $datas)
	{
		try {
			return $this->directUpdate($idTip, $datas);
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update an language without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idLanguage Language's ID
	 * @param array $datas Language's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idLanguage, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `tip` 
							   SET `content`      = :content,
								   `console_names` = :console_names,
								   `game_idGame`   = :game_idGame
							   WHERE `idTip` =  :idTip;');
		$stmt->bindParam(':content', $datas['content'], PDO::PARAM_STR);
		$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
		$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
		$stmt->bindParam(':idTip', $idTip, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete an tip by it's ID
	 *
	 * @param int $idTip Tip's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteTip($idTip)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `tip` 
								   WHERE `idTip` =  :idTip;');
			$stmt->bindParam(':idTip', $idTip, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing tip.
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