<?php
class Media extends BaseModel
{
	/**
	 * Retrieve every available medias or medias by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $medias array
	 */
	public function findBy($paramName = null, $paramValue = null)
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

		$medias = $this->select($fields, $where, [], $join);

		return $medias;
	}

	/**
	 * Insert a new media in database.
	 * If the media already exists, return the existing media's ID.
	 *
	 * @param $datas string Media's name
	 * @return $id int Media's ID
	 */
	public function insertMedia($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `media` (`type`, `url`, `unit`, `width`, `height`, `console_names`, `game_idGame`) 
								   VALUES (:type, :url, :unit, :width, :height, :console_names, :game_idGame)');
			$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
			$stmt->bindParam(':url', $datas['url'], PDO::PARAM_STR);
			$stmt->bindParam(':unit', $datas['unit'], PDO::PARAM_STR);
			$stmt->bindParam(':width', $datas['width'], PDO::PARAM_STR);
			$stmt->bindParam(':height', $datas['height'], PDO::PARAM_STR);
			$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
			$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
			$stmt->execute();

			return $pdo->lastInsertId();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update media
	 *
	 * @param $idMedia int Media's ID
	 * @param $media string Media's name
	 */
	public function updateMedia($idMedia, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `media` 
								   SET `type`      = :type,
									   `url`           = :url,
									   `unit`          = :unit,
									   `width`         = :width,
									   `height`        = :height,
									   `console_names` = :console_names,
									   `game_idGame`   = :game_idGame
								   WHERE `idMedia` =  :idMedia');
			$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
			$stmt->bindParam(':url', $datas['url'], PDO::PARAM_STR);
			$stmt->bindParam(':unit', $datas['unit'], PDO::PARAM_STR);
			$stmt->bindParam(':width', $datas['width'], PDO::PARAM_STR);
			$stmt->bindParam(':height', $datas['height'], PDO::PARAM_STR);
			$stmt->bindParam(':console_names', $datas['console_names'], PDO::PARAM_STR);
			$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
			$stmt->bindParam(':idMedia', $idMedia, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing media.
			 * MySQL won't send any error as, regarding to him, the request is correct, so we have to handle it manually.
			 */
			return $stmt->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage(); die;
			return false;
		} catch (Exception $e) {
			echo $e->getMessage(); die;
			return false;
		}
	}

	/**
	 * Delete an media by it's ID
	 *
	 * @param $id int Media's ID
	 */
	public function deleteMedia($idMedia)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `media` 
								   WHERE `idMedia` =  :idMedia');
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