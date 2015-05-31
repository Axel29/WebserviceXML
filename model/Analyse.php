<?php
class Analyse extends BaseModel
{
	/**
	 * Retrieve every available analyses or analyses by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $analyses Collection of analyses as array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'analyse';
		
		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		$analyses = $this->select(['*'], $where);

		return $analyses;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'analyse'     => 'string',
			'type'        => 'string',
			'test_idTest' => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new analyse in database.
	 *
	 * @param array $datas Analyse's datas
	 * @return int|bool $insertedAnalyse Analyse's ID or false if an error has occurred
	 */
	public function insertAnalyse($datas)
	{
		try {
			$insertedAnalyse = $this->directInsert($datas);
			return $insertedAnalyse;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new analyse in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Analyse's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $id Inserted analyse's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('INSERT INTO `analyse` (`analyse`, `type`, `test_idTest`) 
							   VALUES (:analyse, :type, :test_idTest);');
		$stmt->bindParam(':analyse', $datas['analyse'], PDO::PARAM_STR);
		$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
		$stmt->bindParam(':test_idTest', $datas['test_idTest'], PDO::PARAM_INT);
		$stmt->execute();

		return $pdo->lastInsertId();
	}

	/**
	 * Update analyse
	 *
	 * @param int $idAnalyse Analyse's ID
	 * @param array $datas Datas to update
	 * @return int|bool $updatedAnalyse Updated analyse's ID or false if an error has occurred
	 */
	public function updateAnalyse($idAnalyse, $datas)
	{
		try {
			$updatedAnalyse = $this->directUpdate($idAnalyse, $datas);
			return $updatedAnalyse;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update a analyse without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idAnalyse Analyse's ID
	 * @param array $datas Analyse's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idAnalyse, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `analyse` 
							   SET `analyse` = :analyse, `type` = :type, `test_idTest` = :test_idTest 
							   WHERE `idAnalyse` =  :idAnalyse;');
		$stmt->bindParam(':analyse', $datas['analyse'], PDO::PARAM_STR);
		$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
		$stmt->bindParam(':test_idTest', $datas['test_idTest'], PDO::PARAM_INT);
		$stmt->bindParam(':idAnalyse', $idAnalyse, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete a analyse by it's ID
	 *
	 * @param int|bool $idAnalyse Analyse's ID or false if an error has occurred
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteAnalyse($idAnalyse)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `analyse` 
								   WHERE `idAnalyse` =  :idAnalyse;');
			$stmt->bindParam(':idAnalyse', $idAnalyse, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing analyse.
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