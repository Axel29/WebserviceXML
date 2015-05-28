<?php
class Mode extends BaseModel
{
	/**
	 * Retrieve every available modes or modes by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $modes array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'mode m';
		
		$fields = [
			'`idMode`',
			'`mode`',
		];

		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			if ($paramName == 'idConsole') {
				$join = [
					[
						'type'  => 'INNER JOIN',
						'table' => 'console_has_mode chm',
						'on'    => 'm.idMode = chm.mode_idMode',
					],
					[
						'type'  => 'INNER JOIN',
						'table' => 'console c',
						'on'    => 'c.idConsole = chm.console_idConsole',
					],
				];

				$where = [
					'chm.console_idConsole' => $paramValue,
				];
			} else {
				$where = [
					$paramName => $paramValue,
				];
			}
		}

		$modes = $this->select($fields, $where, [], $join);

		return $modes;
	}

	/**
	 * Insert a new mode in database.
	 *
	 * @param $datas array Mode's name
	 * @return $id int Mode's ID
	 */
	public function insertMode($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `mode` (`mode`) 
								   VALUES (:mode)');
			$stmt->bindParam(':mode', $datas['mode'], PDO::PARAM_STR);
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
	 * Update mode
	 *
	 * @param $idMode int Mode's ID
	 * @param $datas array Datas to update
	 */
	public function updateMode($idMode, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `mode`
								   SET `mode`             = :mode
								   WHERE `idMode` =  :idMode');
			$stmt->bindParam(':mode', $datas['mode'], PDO::PARAM_STR);
			$stmt->bindParam(':idMode', $idMode, PDO::PARAM_INT);
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
	 * Delete a mode by it's ID
	 *
	 * @param $id int Mode's ID
	 */
	public function deleteMode($idMode)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `mode` 
								   WHERE `idMode` =  :idMode');
			$stmt->bindParam(':idMode', $idMode, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->rowCount();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}