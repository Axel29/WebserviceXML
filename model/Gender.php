<?php
class Gender extends BaseModel
{
	/**
	 * Retrieve every available genders or genders by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $genders array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'gender ge';

		$fields = ['`idGender`', '`gender`'];
		
		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			if ($paramName == 'idGame') {
				$where = [
					'ghg.game_idGame' => $paramValue,
				];

				$join = [
					[
						'type'  => 'INNER JOIN',
						'table' => 'game_has_gender ghg',
						'on'    => 'ge.idGender = ghg.gender_idGender',
					],
					[
						'type'  => 'INNER JOIN',
						'table' => 'game ga',
						'on'    => 'ga.idGame = ghg.game_idGame',
					],
				];
			} else {
				$where = [
					'ge.' . $paramName => $paramValue,
				];
			}
		}

		$genders = $this->select($fields, $where, [], $join);

		return $genders;
	}

	/**
	 * Get gender by name
	 *
	 * @param $gender string Gender's name
	 * @return $id int Gender's ID or -1 if doesn't exist
	 */
	public function getGenderByName($gender)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('SELECT `idGender` 
								   FROM `gender` 
								   WHERE `gender` = :gender');
			$stmt->bindParam(':gender', $gender);
			$stmt->execute();

			$result = $stmt->fetch();

			if ($result) {
				return $result['idGender'];
			} else {
				return false;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
			exit();
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
	}

	/**
	 * Insert a new gender in database.
	 * If the gender already exists, return the existing gender's ID.
	 *
	 * @param $datas string Gender's name
	 * @return $id int Gender's ID
	 */
	public function insertGender($datas)
	{
		/*
		 * Check that the gender doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingGender = $this->getGenderByName($datas['gender'])) {
			return $existingGender;
		} else {
			try {
				$pdo  = $this->db;
				$stmt = $pdo->prepare('INSERT INTO `gender` (`gender`) 
									   VALUES (:gender)');
				$stmt->bindParam(':gender', $datas['gender'], PDO::PARAM_STR);
				$stmt->execute();

				return $pdo->lastInsertId();
			} catch (PDOException $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}
	}

	/**
	 * Update gender
	 *
	 * @param $idGender int Gender's ID
	 * @param $gender string Gender's name
	 */
	public function updateGender($idGender, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `gender` 
								   SET `gender` = :gender 
								   WHERE `idGender` =  :idGender');
			$stmt->bindParam(':idGender', $idGender, PDO::PARAM_INT);
			$stmt->bindParam(':gender', $datas['gender'], PDO::PARAM_STR);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing gender.
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