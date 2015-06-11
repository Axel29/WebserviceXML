<?php
class Mode extends BaseModel
{
	/**
	 * Retrieve every available modes or modes by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $modes Collection of Modes
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
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

		if ($notPaginated) {
			$limit = '';
		} else {
			$limit = $page - 1 . ', ' . $this->getLimit();
		}

		$modes = $this->select($fields, $where, [], $join, [], $limit);

		return $modes;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'mode' => 'string',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new mode in database.
	 * If the mode already exists, return the existing mode's ID.
	 *
	 * @param array $datas Mode's datas
	 * @return int $insertedMode Mode's ID
	 */
	public function insertMode($datas)
	{
		try {
			$insertedMode = $this->directInsert($datas);
			return $insertedMode;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new mode in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Mode's datas
	 * @return int $insertedMode Inserted mode's ID
	 */
	public function directInsert($datas)
	{
		/*
		 * Check that the mode doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingMode = $this->findBy('mode', $datas['mode'])) {
			$insertedMode = $existingMode[0]['idMode'];
			return $insertedMode;
		} else {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `mode` (`mode`) 
								   VALUES (:mode);');
			$stmt->bindParam(':mode', $datas['mode'], PDO::PARAM_STR);
			$stmt->execute();

			$insertedMode = $pdo->lastInsertId();
			return $insertedMode;
		}
	}

	/**
	 * Update mode
	 *
	 * @param int $idMode Mode's ID
	 * @param array $datas Mode's datas
	 * @return int|bool Updated mode's ID or false if an error has occurred
	 */
	public function updateMode($idMode, $datas)
	{
		try {
			return $this->directUpdate($idMode, $datas);
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update a mode without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idMode Mode's ID
	 * @param array $datas Mode's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idMode, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `mode`
							   SET `mode`             = :mode
							   WHERE `idMode` =  :idMode;');
		$stmt->bindParam(':mode', $datas['mode'], PDO::PARAM_STR);
		$stmt->bindParam(':idMode', $idMode, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete a mode by it's ID
	 *
	 * @param $id int Mode's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteMode($idMode)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `mode` 
								   WHERE `idMode` =  :idMode;');
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