<?php
class Language extends BaseModel
{
	/**
	 * Retrieve every available languages or languages by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $languages array
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
	 * @param $datas string Language's name
	 * @return $id int Language's ID
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
				$pdo  = $this->db;
				$stmt = $pdo->prepare('INSERT INTO `language` (`language`) 
									   VALUES (:language)');
				$stmt->bindParam(':language', $datas['language'], PDO::PARAM_STR);
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
	 * Update language
	 *
	 * @param $idLanguage int Language's ID
	 * @param $language string Language's name
	 */
	public function updateLanguage($idLanguage, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `language` 
								   SET `language` = :language 
								   WHERE `idLanguage` =  :idLanguage');
			$stmt->bindParam(':language', $datas['language'], PDO::PARAM_STR);
			$stmt->bindParam(':idLanguage', $idLanguage, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing language.
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