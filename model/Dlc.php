<?php
class Dlc extends BaseModel
{
	/**
	 * Retrieve every available dlcs or dlcs by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $dlcs array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'dlc';
		
		$fields = [
			'`idDlc`',
			'`title`',
			'`description`',
			'`price`',
			'`devise`',
			'`console_idConsole`',
		];

		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		$dlcs = $this->select($fields, $where);

		return $dlcs;
	}

	/**
	 * Insert a new dlc in database.
	 *
	 * @param $datas array Dlc's name
	 * @return $id int Dlc's ID
	 */
	public function insertDlc($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `dlc` (`title`, `description`, `price`, `devise`, `console_idConsole`) 
								   VALUES (:title, :description, :price, :devise, :console_idConsole)');
			$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
			$stmt->bindParam(':description', $datas['description'], PDO::PARAM_STR);
			$stmt->bindParam(':price', $datas['price'], PDO::PARAM_STR);
			$stmt->bindParam(':devise', $datas['devise'], PDO::PARAM_STR);
			$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
			$stmt->execute();

			return $pdo->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update dlc
	 *
	 * @param $idDlc int Dlc's ID
	 * @param $datas array Datas to update
	 */
	public function updateDlc($idDlc, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `dlc`
									SET `title`             = :title,
										`description`       = :description,
										`price`             = :price,
										`devise`            = :devise,
										`console_idConsole` = :console_idConsole
								   WHERE `idDlc` =  :idDlc');
			$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
			$stmt->bindParam(':description', $datas['description'], PDO::PARAM_STR);
			$stmt->bindParam(':price', $datas['price'], PDO::PARAM_STR);
			$stmt->bindParam(':devise', $datas['devise'], PDO::PARAM_STR);
			$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
			$stmt->bindParam(':idDlc', $idDlc, PDO::PARAM_INT);
			$stmt->execute();

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
	 * Delete a dlc by it's ID
	 *
	 * @param $id int Dlc's ID
	 */
	public function deleteDlc($idDlc)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `dlc` 
								   WHERE `idDlc` =  :idDlc');
			$stmt->bindParam(':idDlc', $idDlc, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->rowCount();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}