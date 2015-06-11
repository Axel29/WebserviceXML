<?php
class Theme extends BaseModel
{
	/**
	 * Retrieve every available themes or themes by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $themes Collection of Themes
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
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

		if ($notPaginated) {
			$limit = '';
		} else {
			$limit = $page - 1 . ', ' . $this->getLimit();
		}

		$themes = $this->select($fields, $where, [], $join, [], $limit);

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
	 * @param array $datas Theme's datas
	 * @return int|bool $insertedTheme Theme's ID or false if an error has occurred
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
				$insertedTheme = $this->directInsert($datas);
				return $insertedTheme;
			} catch (PDOException $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}
	}

	/**
	 * Insert a new theme in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Theme's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedTheme Inserted theme's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		/*
		 * Check that the theme doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingTheme = $this->findBy('theme', $datas['theme'])) {
			$insertedTheme = $existingTheme[0]['idTheme'];
			return $insertedTheme;
		} else {
			if (!$pdo) {
				$pdo  = $this->db;
			}
			$stmt = $pdo->prepare('INSERT INTO `theme` (`theme`) 
								   VALUES (:theme)');
			$stmt->bindParam(':theme', $datas['theme'], PDO::PARAM_STR);
			$stmt->execute();

			$insertedTheme = $pdo->lastInsertId();
			return $insertedTheme;
		}
	}

	/**
	 * Update theme
	 *
	 * @param int $idTheme Theme's ID
	 * @param array $theme Theme's datas
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function updateTheme($idTheme, $datas)
	{
		try {
			return $this->directUpdate($idTheme, $datas);
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update an theme without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idTheme Theme's ID
	 * @param array $datas Theme's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idTheme, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `theme` 
							   SET `theme` = :theme 
							   WHERE `idTheme` =  :idTheme');
		$stmt->bindParam(':theme', $datas['theme'], PDO::PARAM_STR);
		$stmt->bindParam(':idTheme', $idTheme, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}
}