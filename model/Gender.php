<?php
class Gender extends BaseModel
{
	/**
	 * Retrieve every available genders or genders by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $genders Collection of Genders
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'gender ge';

		$fields = ['`idGender`', '`gender`'];
		
		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			if ($paramName == 'idGame') {
				$join = [
					[
						'type'  => 'INNER JOIN',
						'table' => 'game_has_gender ghg',
						'on'    => 'ge.idGender = ghg.gender_idGender',
					],
					[
						'type'  => 'INNER JOIN',
						'table' => 'game g',
						'on'    => 'g.idGame = ghg.game_idGame',
					],
				];

				$where = [
					'ghg.game_idGame' => $paramValue,
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
			$entriesPerPage = $this->getLimit();
			$firstEntry     = ($page - 1) * $entriesPerPage;
			$limit          = $firstEntry . ', ' . $entriesPerPage;
		}

		$genders = $this->select($fields, $where, [], $join, [], $limit);

		return $genders;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'gender' => 'string',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new gender in database.
	 * If the gender already exists, return the existing gender's ID.
	 *
	 * @param array $datas Gender's datas
	 * @return int|bool $insertedGender Gender's ID or false if an error has occurred
	 */
	public function insertGender($datas)
	{
		/*
		 * Check that the gender doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingGender = $this->findBy('gender', $datas['gender'])) {
			$insertedGender = $existingGender[0]['idGender'];
			return $insertedGender;
		} else {
			try {
				$insertedGender = $this->directInsert($datas);
				return $insertedGender;
			} catch (PDOException $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}
	}

	/**
	 * Insert a new gender in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Gender's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedGender Inserted gender's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		/*
		 * Check that the gender doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingGender = $this->findBy('gender', $datas['gender'])) {
			$insertedGender = $existingGender[0]['idGender'];
			return $insertedGender;
		} else {
			if (!$pdo) {
				$pdo  = $this->db;
			}
			$stmt = $pdo->prepare('INSERT INTO `gender` (`gender`) 
								   VALUES (:gender);');
			$stmt->bindParam(':gender', $datas['gender'], PDO::PARAM_STR);
			$stmt->execute();

			$insertedGender = $pdo->lastInsertId();
			return $insertedGender;
		}
	}

	/**
	 * Update gender
	 *
	 * @param int $idGender Gender's ID
	 * @param array $datas Gender's datas
	 * @return bool true or false if an error has occurred
	 */
	public function updateGender($idGender, $datas)
	{
		try {
			return $this->directUpdate($idGender, $datas);
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update an gender without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idGender Gender's ID
	 * @param array $datas Gender's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idGender, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `gender` 
							   SET `gender` = :gender 
							   WHERE `idGender` =  :idGender;');
		$stmt->bindParam(':idGender', $idGender, PDO::PARAM_INT);
		$stmt->bindParam(':gender', $datas['gender'], PDO::PARAM_STR);
		$stmt->execute();

		return true;
	}
}