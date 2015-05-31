<?php
class Dlc extends BaseModel
{
	/**
	 * Retrieve every available dlcs or dlcs by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $dlcs Collection of Dlcs
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'dlc';
		
		$fields = [
			'`idDlc`',
			'`title`',
			'`description`',
			'`price`',
			'`devise`',
			'`console_idConsole`',
		];

		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		$dlcs = $this->select($fields, $where);

		return $dlcs;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'title'             => 'string',
			'description'       => 'string',
			'price'             => 'float',
			'devise'            => 'string',
			'console_idConsole' => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new dlc in database.
	 *
	 * @param array $datas Dlc's datas
	 * @return int|bool $insertedDlc Dlc's ID or false if an error has occurred
	 */
	public function insertDlc($datas)
	{
		try {
			$insertedDlc = $this->directInsert($datas);
			return $insertedDlc;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new dlc in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Dlc's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $id Inserted mode's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}

		// Check that the console's ID exists
		$stmt = $pdo->prepare('SELECT `idConsole`
							   FROM `console`
							   WHERE `idConsole` = :idConsole;');
		$stmt->bindParam(':idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->execute();

		$console = $stmt->fetch();
		if (!count($console) || !isset($console['idConsole'])) {
			return false;
		}

		$stmt = $pdo->prepare('INSERT INTO `dlc` (`title`, `description`, `price`, `devise`, `console_idConsole`) 
							   VALUES (:title, :description, :price, :devise, :console_idConsole);');
		$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $datas['description'], PDO::PARAM_STR);
		$stmt->bindParam(':price', $datas['price'], PDO::PARAM_STR);
		$stmt->bindParam(':devise', $datas['devise'], PDO::PARAM_STR);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->execute();

		return $pdo->lastInsertId();
	}

	/**
	 * Update dlc
	 *
	 * @param int $idDlc Dlc's ID
	 * @param array $datas Datas to update
	 * @return bool true or false if an error has occurred
	 */
	public function updateDlc($idDlc, $datas)
	{
		try {
			return $this->directUpdate($idDlc, $datas);
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update a dlc without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idDlc Dlc's ID
	 * @param array $datas Dlc's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idDlc, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}

		if (isset($datas['console_idConsole'])) {
			// Check that the console's ID exists
			$stmt = $pdo->prepare('SELECT `idConsole`
								   FROM `console`
								   WHERE `idConsole` = :idConsole;');
			$stmt->bindParam(':idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
			$stmt->execute();

			$console = $stmt->fetch();
			if (!count($console) || !isset($console['idConsole'])) {
				return false;
			}
		}
		
		// Check that the dlc's ID exists
		$stmt = $pdo->prepare('SELECT `idDlc`
							   FROM `dlc`
							   WHERE `idDlc` = :idDlc;');
		$stmt->bindParam(':idDlc', $idDlc, PDO::PARAM_INT);
		$stmt->execute();

		$dlc = $stmt->fetch();
		if (!count($dlc) || !isset($dlc['idDlc'])) {
			return false;
		}

		$stmt = $pdo->prepare('UPDATE `dlc`
								SET `title`             = :title,
									`description`       = :description,
									`price`             = :price,
									`devise`            = :devise,
									`console_idConsole` = :console_idConsole
							   WHERE `idDlc` =  :idDlc;');
		$stmt->bindParam(':title', $datas['title'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $datas['description'], PDO::PARAM_STR);
		$stmt->bindParam(':price', $datas['price'], PDO::PARAM_STR);
		$stmt->bindParam(':devise', $datas['devise'], PDO::PARAM_STR);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->bindParam(':idDlc', $idDlc, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete a dlc by it's ID
	 *
	 * @param int $idDlc Dlc's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteDlc($idDlc)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `dlc` 
								   WHERE `idDlc` =  :idDlc;');
			$stmt->bindParam(':idDlc', $idDlc, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->rowCount();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}