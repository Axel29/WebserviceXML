<?php
class Tip extends BaseModel
{
	/**
	 * Retrieve every available tips or tips by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $tips Collection of tips
	 */
	public function findBy($paramName = null, $paramValue = null)
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

		$tips = $this->select($fields, $where, [], $join);

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
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `tip` (`content`, `console_names`, `game_idGame`) 
								   VALUES (:content, :console_names, :game_idGame);');
			$stmt->bindParam(':content', $datas['content'], PDO::PARAM_STR);
			$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
			$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
			$stmt->execute();

			$insertedTip = $pdo->lastInsertId();
			return $insertedTip;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
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
			$pdo  = $this->db;
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