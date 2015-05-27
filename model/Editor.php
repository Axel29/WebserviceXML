<?php
class Editor extends BaseModel
{
	/**
	 * Retrieve every available editors or editors by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $editors array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'editor e';

		$fields = ['`idEditor`', '`editor`'];
		
		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			if ($paramName == 'idGame') {
				$join = [
					[
						'type'  => 'INNER JOIN',
						'table' => 'game_has_editor ghe',
						'on'    => 'e.idEditor = ghe.editor_idEditor',
					],
					[
						'type'  => 'INNER JOIN',
						'table' => 'game g',
						'on'    => 'g.idGame = ghe.game_idGame',
					],
				];

				$where = [
					'ghe.game_idGame' => $paramValue,
				];
			} else {
				$where = [
					$paramName => $paramValue,
				];
			}
		}

		$editors = $this->select($fields, $where, [], $join);

		return $editors;
	}

	/**
	 * Insert a new editor in database.
	 * If the editor already exists, return the existing editor's ID.
	 *
	 * @param $datas string Editor's name
	 * @return $id int Editor's ID
	 */
	public function insertEditor($datas)
	{
		/*
		 * Check that the editor doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingEditor = $this->findBy('editor', $datas['editor'])) {
			return $existingEditor;
		} else {
			try {
				$pdo  = $this->db;
				$stmt = $pdo->prepare('INSERT INTO `editor` (`editor`) 
									   VALUES (:editor)');
				$stmt->bindParam(':editor', $datas['editor'], PDO::PARAM_STR);
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
	 * Update editor
	 *
	 * @param $idEditor int Editor's ID
	 * @param $editor string Editor's name
	 */
	public function updateEditor($idEditor, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `editor` 
								   SET `editor` = :editor 
								   WHERE `idEditor` =  :idEditor');
			$stmt->bindParam(':editor', $datas['editor'], PDO::PARAM_STR);
			$stmt->bindParam(':idEditor', $idEditor, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing editor.
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