<?php
class Editor extends BaseModel
{
	/**
	 * Retrieve every available editors or editors by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $editors Collection of Editors
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'editor e';

		$fields = ['`idEditor`', '`editor`'];
		
		$where = [];
		$join  = [];

		if ($notPaginated) {
			$limit = '';
		} else {
			$entriesPerPage = $this->getLimit();
			$firstEntry     = ($page - 1) * $entriesPerPage;
			$limit          = $firstEntry . ', ' . $entriesPerPage;
		}

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
		
		$editors = $this->select($fields, $where, [], $join, [], $limit);

		return $editors;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'editor' => 'string',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new editor in database.
	 * If the editor already exists, return the existing editor's ID.
	 *
	 * @param array $datas Editor's datas
	 * @return int|bool $insertedEditor Editor's ID or false if an error has occurred
	 */
	public function insertEditor($datas)
	{
		/*
		 * Check that the editor doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingEditor = $this->findBy('editor', $datas['editor'])) {
			$insertedEditor = $existingEditor[0]['idEditor'];
			return $insertedEditor;
		} else {
			try {
				$insertedEditor = $this->directInsert($datas);
				return $insertedEditor;
			} catch (PDOException $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}
	}

	/**
	 * Insert a new editor in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Editor's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedEditor Inserted editor's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		/*
		 * Check that the editor doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingEditor = $this->findBy('editor', $datas['editor'])) {
			$insertedEditor = $existingEditor[0]['idEditor'];
			return $insertedEditor;
		} else {
			if (!$pdo) {
				$pdo  = $this->db;
			}
			$stmt = $pdo->prepare('INSERT INTO `editor` (`editor`) 
								   VALUES (:editor);');
			$stmt->bindParam(':editor', $datas['editor'], PDO::PARAM_STR);
			$stmt->execute();

			$insertedEditor = $pdo->lastInsertId();
			return $insertedEditor;
		}
	}

	/**
	 * Update editor
	 *
	 * @param int $idEditor Editor's ID
	 * @param array $datas Editor's datas
	 * @return bool true or false if an error has occurred
	 */
	public function updateEditor($idEditor, $datas)
	{
		try {
			return $this->directUpdate($idEditor, $datas);
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update an editor without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idEditor Editor's ID
	 * @param array $datas Editor's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idEditor, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `editor` 
							   SET `editor` = :editor 
							   WHERE `idEditor` =  :idEditor;');
		$stmt->bindParam(':editor', $datas['editor'], PDO::PARAM_STR);
		$stmt->bindParam(':idEditor', $idEditor, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}
}