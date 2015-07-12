<?php
class Analyse extends BaseModel
{
	/**
	 * Retrieve every available analyses or analyses by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $analyses Collection of analyses as array
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'analyse';
		
		if ($notPaginated) {
			$limit = '';
		} else {
			$entriesPerPage = $this->getLimit();
			$firstEntry     = ($page - 1) * $entriesPerPage;
			$limit          = $firstEntry . ', ' . $entriesPerPage;
		}

		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		$analyses = $this->select(['*'], $where, [], [], [], $limit);

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
	 * @return int $insertedAnalse Inserted analyse's ID
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

		$stmt = $pdo->prepare('INSERT INTO `analyse` (`analyse`, `type`, `test_idTest`) 
							   VALUES (:analyse, :type, :test_idTest);');
		$stmt->bindParam(':analyse', $datas['analyse'], PDO::PARAM_STR);
		$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
		$stmt->bindParam(':test_idTest', $datas['test_idTest'], PDO::PARAM_INT);
		$stmt->execute();

		$insertedAnalyse = $pdo->lastInsertId();
		return $insertedAnalyse;
	}

	/**
	 * Update analyse
	 *
	 * @param int $idAnalyse Analyse's ID
	 * @param array $datas Datas to update
	 * @return bool true or false if an error has occurred
	 */
	public function updateAnalyse($idAnalyse, $datas)
	{
		try {
			return $this->directUpdate($idAnalyse, $datas);
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
		
		// Check that the analyse's ID exists
		$stmt = $pdo->prepare('SELECT `idAnalyse`
							   FROM `analyse`
							   WHERE `idAnalyse` = :idAnalyse;');
		$stmt->bindParam(':idAnalyse', $idAnalyse, PDO::PARAM_INT);
		$stmt->execute();

		$analyse = $stmt->fetch();
		if (!count($analyse) || !isset($analyse['idAnalyse'])) {
			return false;
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