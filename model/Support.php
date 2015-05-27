<?php
class Support extends BaseModel
{
	/**
	 * Retrieve every available supports or supports by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $supports array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'support s';
		
		$fields = [
			'`idSupport`',
			'`support`',
		];

		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			if ($paramName == 'idConsole') {
				$join = [
					[
						'type'  => 'INNER JOIN',
						'table' => 'console_has_support chs',
						'on'    => 's.idSupport = chs.support_idSupport',
					],
					[
						'type'  => 'INNER JOIN',
						'table' => 'console c',
						'on'    => 'c.idConsole = chs.console_idConsole',
					],
				];

				$where = [
					'chs.console_idConsole' => $paramValue,
				];
			} else {
				$where = [
					$paramName => $paramValue,
				];
			}
		}

		$supports = $this->select($fields, $where);

		return $supports;
	}

	/**
	 * Insert a new support in database.
	 *
	 * @param $datas array Support's name
	 * @return $id int Support's ID
	 */
	public function insertSupport($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `support` (`support`) 
								   VALUES (:support)');
			$stmt->bindParam(':support', $datas['support'], PDO::PARAM_STR);
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
	 * Update support
	 *
	 * @param $idSupport int Support's ID
	 * @param $datas array Datas to update
	 */
	public function updateSupport($idSupport, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `support`
								   SET `support` = :support
								   WHERE `idSupport` =  :idSupport');
			$stmt->bindParam(':support', $datas['support'], PDO::PARAM_STR);
			$stmt->bindParam(':idSupport', $idSupport, PDO::PARAM_INT);
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
	 * Delete a support by it's ID
	 *
	 * @param $id int Support's ID
	 */
	public function deleteSupport($idSupport)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `support` 
								   WHERE `idSupport` =  :idSupport');
			$stmt->bindParam(':idSupport', $idSupport, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->rowCount();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}