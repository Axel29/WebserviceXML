<?php
class Analyse extends BaseModel
{
	/**
	 * Retrieve every available analyses or analyses by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $analyses array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'analyse a';
		
		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				'a.' . $paramName => $paramValue,
			];
		}

		$analyses = $this->select(['*'], $where);

		return $analyses;
	}

	/**
	 * Insert a new analyse in database.
	 *
	 * @param $datas array Analyse's name
	 * @return $id int Analyse's ID
	 */
	public function insertAnalyse($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `analyse` (`analyse`, `type`, `test_idTest`) 
								   VALUES (:analyse, :type, :test_idTest)');
			$stmt->bindParam(':analyse', $datas['analyse'], PDO::PARAM_STR);
			$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
			$stmt->bindParam(':test_idTest', $datas['test_idTest'], PDO::PARAM_INT);
			$stmt->execute();

			return $pdo->lastInsertId();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update analyse
	 *
	 * @param $idAnalyse int Analyse's ID
	 * @param $datas array Datas to update
	 */
	public function updateAnalyse($idAnalyse, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `analyse` 
								   SET `analyse` = :analyse, `type` = :type, `test_idTest` = :test_idTest 
								   WHERE `idAnalyse` =  :idAnalyse');
			$stmt->bindParam(':analyse', $datas['analyse'], PDO::PARAM_STR);
			$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
			$stmt->bindParam(':test_idTest', $datas['test_idTest'], PDO::PARAM_INT);
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

	/**
	 * Delete a analyse by it's ID
	 *
	 * @param $id int Analyse's ID
	 */
	public function deleteAnalyse($idAnalyse)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `analyse` 
								   WHERE `idAnalyse` =  :idAnalyse');
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