<?php
class Theme extends BaseModel
{
	/**
	 * Retrieve every available themes or themes by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $themes array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'theme t';

		$fields = ['`idTheme`', '`theme`'];
		
		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			if ($paramName == 'idGame') {
				$join = [
					[
						'type'  => 'INNER JOIN',
						'table' => 'game_has_theme ght',
						'on'    => 't.idTheme = ght.theme_idTheme',
					],
					[
						'type'  => 'INNER JOIN',
						'table' => 'game g',
						'on'    => 'g.idGame = ght.game_idGame',
					],
				];

				$where = [
					'ght.game_idGame' => $paramValue,
				];
			} else {
				$where = [
					$paramName => $paramValue,
				];
			}
		}

		$themes = $this->select($fields, $where, [], $join);

		return $themes;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'theme' => 'string',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new theme in database.
	 * If the theme already exists, return the existing theme's ID.
	 *
	 * @param $datas string Theme's name
	 * @return $id int Theme's ID
	 */
	public function insertTheme($datas)
	{
		/*
		 * Check that the theme doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingTheme = $this->findBy('theme', $datas['theme'])) {
			return $existingTheme[0]['idTheme'];
		} else {
			try {
				$pdo  = $this->db;
				$stmt = $pdo->prepare('INSERT INTO `theme` (`theme`) 
									   VALUES (:theme)');
				$stmt->bindParam(':theme', $datas['theme'], PDO::PARAM_STR);
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
	 * Update theme
	 *
	 * @param $idTheme int Theme's ID
	 * @param $theme string Theme's name
	 */
	public function updateTheme($idTheme, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `theme` 
								   SET `theme` = :theme 
								   WHERE `idTheme` =  :idTheme');
			$stmt->bindParam(':theme', $datas['theme'], PDO::PARAM_STR);
			$stmt->bindParam(':idTheme', $idTheme, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing theme.
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