<?php
class Support extends BaseModel
{
	/**
	 * Retrieve every available supports or supports by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $supports Collection of supports
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

		$supports = $this->select($fields, $where, [], $join);

		return $supports;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'support' => 'string',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new support in database.
	 * If the support already exists, return the existing support's ID.
	 *
	 * @param array $datas Support's datas
	 * @return int|bool $insertedSupport Inserted support's ID or false if an error has occurred
	 */
	public function insertSupport($datas)
	{
		try {
			$insertedSupport = $this->directInsert($datas);
			return $insertedSupport;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new support in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Support's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedSupport Inserted support's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		/*
		 * Check that the support doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingMode = $this->findBy('support', $datas['support'])) {
			$insertedSupport = $existingMode[0]['idSupport'];
			return $insertedSupport;
		} else {
			if (!$pdo) {
				$pdo  = $this->db;
			}
			$stmt = $pdo->prepare('INSERT INTO `support` (`support`) 
								   VALUES (:support);');
			$stmt->bindParam(':support', $datas['support'], PDO::PARAM_STR);
			$stmt->execute();

			$insertedSupport = $pdo->lastInsertId();
			return $insertedSupport;
		}
	}

	/**
	 * Update support
	 *
	 * @param int $idSupport Support's ID
	 * @param array $datas Datas to update
	 * @return int|bool $updatedSupport Updated support's ID or false if an error has occurred
	 */
	public function updateSupport($idSupport, $datas)
	{
		try {
			$insertedSupport = $this->directUpdate($idSupport, $datas);
			return $insertedSupport;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update a support without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Support's datas
	 * @return int $id Inserted support's ID
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idSupport, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `support`
							   SET `support` = :support
							   WHERE `idSupport` =  :idSupport;');
		$stmt->bindParam(':support', $datas['support'], PDO::PARAM_STR);
		$stmt->bindParam(':idSupport', $idSupport, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete a support by it's ID
	 *
	 * @param int $idSupport Support's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
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