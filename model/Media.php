<?php
class Media extends BaseModel
{
	/**
	 * Retrieve every available medias or medias by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $medias Collection of medias
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'media';

		$fields = [
			'`idMedia`', 
			'`type`', 
			'`url`', 
			'`unit`', 
			'`width`', 
			'`height`', 
			'`console_names`', 
			'`game_idGame`'
		];
		
		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		if ($notPaginated) {
			$limit = '';
		} else {
			$limit = $page - 1 . ', ' . $this->getLimit();
		}

		$medias = $this->select($fields, $where, [], $join, [], $limit);

		return $medias;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'type'          => 'string',
			'url'           => 'string',
			'unit'          => 'string',
			'width'         => 'float',
			'height'        => 'float',
			'console_names' => 'string',
			'game_idGame'   => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new media in database.
	 *
	 * @param array $datas Media's datas
	 * @return int|bool $insertedMedia Media's ID or false if an error has occurred
	 */
	public function insertMedia($datas)
	{
		try {
			$insertedMedia = $this->directInsert($datas);
			return $insertedMedia;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new media in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Media's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedMedia Inserted media's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo = $this->db;
		}

		// Check that the game's ID exists
		$stmt = $pdo->prepare('SELECT `idGame`
							   FROM `game`
							   WHERE `idGame` = :idGame;');
		$stmt->bindParam(':idGame', $datas['game_idGame'], PDO::PARAM_INT);
		$stmt->execute();

		$game = $stmt->fetch();
		if (!count($game) || !isset($game['idGame'])) {
			return false;
		}

		$stmt = $pdo->prepare('INSERT INTO `media` (`type`, `url`, `unit`, `width`, `height`, `console_names`, `game_idGame`) 
							   VALUES (:type, :url, :unit, :width, :height, :console_names, :game_idGame);');
		$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
		$stmt->bindParam(':url', $datas['url'], PDO::PARAM_STR);
		$stmt->bindParam(':unit', $datas['unit'], PDO::PARAM_STR);
		$stmt->bindParam(':width', $datas['width'], PDO::PARAM_STR);
		$stmt->bindParam(':height', $datas['height'], PDO::PARAM_STR);
		$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
		$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
		$stmt->execute();

		$insertedMedia = $pdo->lastInsertId();
		return $insertedMedia;
	}

	/**
	 * Update media
	 *
	 * @param int $idMedia Media's ID
	 * @param array $datas Media's datas
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function updateMedia($idMedia, $datas)
	{
		try {
			return $this->directInsert($idMedia, $datas);
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update an media without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idMedia Media's ID
	 * @param array $datas Media's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idMedia, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}

		// Check that the media's ID exists
		$stmt = $pdo->prepare('SELECT `idMedia`
							   FROM `media`
							   WHERE `idMedia` = :idMedia;');
		$stmt->bindParam(':idMedia', $idMedia, PDO::PARAM_INT);
		$stmt->execute();

		$media = $stmt->fetch();
		if (!count($media) || !isset($media['idMedia'])) {
			return false;
		}

		$stmt = $pdo->prepare('UPDATE `media` 
							   SET `type`      = :type,
								   `url`           = :url,
								   `unit`          = :unit,
								   `width`         = :width,
								   `height`        = :height,
								   `console_names` = :console_names,
								   `game_idGame`   = :game_idGame
							   WHERE `idMedia` =  :idMedia;');
		$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
		$stmt->bindParam(':url', $datas['url'], PDO::PARAM_STR);
		$stmt->bindParam(':unit', $datas['unit'], PDO::PARAM_STR);
		$stmt->bindParam(':width', $datas['width'], PDO::PARAM_STR);
		$stmt->bindParam(':height', $datas['height'], PDO::PARAM_STR);
		$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
		$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
		$stmt->bindParam(':idMedia', $idMedia, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete an media by it's ID
	 *
	 * @param int $idMedia Media's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteMedia($idMedia)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `media` 
								   WHERE `idMedia` =  :idMedia;');
			$stmt->bindParam(':idMedia', $idMedia, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing media.
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