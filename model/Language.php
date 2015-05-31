<?php
class Language extends BaseModel
{
	/**
	 * Retrieve every available languages or languages by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $languages Collection of Languages
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'language l';

		$fields = ['`idLanguage`', '`language`'];
		
		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			if ($paramName == 'idGame') {
				$join = [
					[
						'type'  => 'INNER JOIN',
						'table' => 'game_has_language ghl',
						'on'    => 'l.idLanguage = ghl.language_idLanguage',
					],
					[
						'type'  => 'INNER JOIN',
						'table' => 'game g',
						'on'    => 'g.idGame = ghl.game_idGame',
					],
				];

				$where = [
					'ghl.game_idGame' => $paramValue,
				];
			} else {
				$where = [
					$paramName => $paramValue,
				];
			}
		}

		$languages = $this->select($fields, $where, [], $join);

		return $languages;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'language' => 'string',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new language in database.
	 * If the language already exists, return the existing language's ID.
	 *
	 * @param array $datas Language's datas
	 * @return int|bool $insertedLanguage Language's ID or false if an error has occurred
	 */
	public function insertLanguage($datas)
	{
		/*
		 * Check that the language doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingLanguage = $this->findBy('language', $datas['language'])) {
			return $existingLanguage[0]['idLanguage'];
		} else {
			try {
				$insertedLanguage = $this->directInsert($datas);
				return $insertedLanguage;
			} catch (PDOException $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}
	}

	/**
	 * Insert a new language in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Language's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedLanguage Inserted language's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		/*
		 * Check that the language doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingLanguage = $this->findBy('language', $datas['language'])) {
			$insertedLanguage = $existingLanguage[0]['idLanguage'];
			return $insertedLanguage;
		} else {
			if (!$pdo) {
				$pdo  = $this->db;
			}
			$stmt = $pdo->prepare('INSERT INTO `language` (`language`) 
								   VALUES (:language);');
			$stmt->bindParam(':language', $datas['language'], PDO::PARAM_STR);
			$stmt->execute();

			$insertedLanguage = $pdo->lastInsertId();
			return $insertedLanguage;
		}
	}

	/**
	 * Update language
	 *
	 * @param int $idLanguage int Language's ID
	 * @param array $datas Language's datas
	 * @return bool true or false if an error has occurrred
	 */
	public function updateLanguage($idLanguage, $datas)
	{
		try {
			return $this->directUpdate($idLanguage, $datas);
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update an language without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idLanguage Language's ID
	 * @param array $datas Language's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idLanguage, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `language` 
							   SET `language` = :language 
							   WHERE `idLanguage` =  :idLanguage;');
		$stmt->bindParam(':language', $datas['language'], PDO::PARAM_STR);
		$stmt->bindParam(':idLanguage', $idLanguage, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}
}