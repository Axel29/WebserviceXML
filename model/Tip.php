<?php
class Tip extends BaseModel
{
	/**
	 * Retrieve every available tips or tips by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $tips array
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
	 * Insert a new tip in database.
	 *
	 * @param $datas string Tip's name
	 * @return $id int Tip's ID
	 */
	public function insertTip($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `tip` (`content`, `console_names`, `game_idGame`) 
								   VALUES (:content, :console_names, :game_idGame)');
			$stmt->bindParam(':content', $datas['content'], PDO::PARAM_STR);
			$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
			$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
			$stmt->execute();

			return $pdo->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage(); die;
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update tip
	 *
	 * @param $idTip int Tip's ID
	 * @param $tip string Tip's name
	 */
	public function updateTip($idTip, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `tip` 
								   SET `content`      = :content,
									   `console_names` = :console_names,
									   `game_idGame`   = :game_idGame
								   WHERE `idTip` =  :idTip');
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
			echo $e->getMessage(); die;
			return false;
		} catch (Exception $e) {
			echo $e->getMessage(); die;
			return false;
		}
	}

	/**
	 * Delete an tip by it's ID
	 *
	 * @param $id int Tip's ID
	 */
	public function deleteTip($idTip)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `tip` 
								   WHERE `idTip` =  :idTip');
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