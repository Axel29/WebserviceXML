<?php
class Edition extends BaseModel
{
	/**
	 * Retrieve every available editions or editions by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $editions Colleciton of Editions
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'edition';
		
		$fields = [
			'`idEdition`',
			'`name`',
			'`content`',
			'`console_idConsole`',
		];

		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		$editions = $this->select($fields, $where);

		return $editions;
	}

    /**
     * Retrieve number of available editions for a console by edition ID.
     * Used to check that there is at least one edition left before deleting as they are required.
     *
     * @param int $editionId Edition's ID
     * @return int $numberOfEditionsLeft Number of existing editions
     */
    public function getNumberOfEditionsLeft($editionId)
    {
        try {
            $pdo  = $this->db;
            $stmt = $pdo->prepare('SELECT COUNT(`e`.`idEdition`) AS `nbrEditions`
								   FROM `edition` `e`
								   LEFT JOIN `console` `c` ON `e`.`console_idConsole` = `c`.`idConsole`
								   WHERE `c`.`idConsole` = (SELECT `e`.`console_idConsole`
                                                            FROM `edition` `e`
                                                            WHERE `e`.`idEdition` = :idEdition
                                                            );'
                                  );
            $stmt->bindParam(':idEdition', $editionId, PDO::PARAM_INT);
            $stmt->execute();

			$result               = $stmt->fetch();
			$numberOfEditionsLeft = $result['nbrEditions'];
            return $numberOfEditionsLeft;
        } catch (PDOException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'name'              => 'string',
			'content'           => 'string',
			'console_idConsole' => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new edition in database.
	 *
	 * @param array $datas Edition's datas
	 * @return int|bool $editionId Edition's ID or false if an error has occurred
	 */
	public function insertEdition($datas)
	{
		$pdo  = $this->db;
		try {
			// Begin transaction to avoid inserting wrong or partial datas
			$pdo->beginTransaction();

			$editionId = $this->directInsert($datas);

			// If everything went well, commit the transaction
			$pdo->commit();

			return $editionId;
		} catch (PDOException $e) {
			// Cancel the transaction
		    $pdo->rollback();
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new edition in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Edition's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedEdition Inserted edition's ID
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

		$stmt = $pdo->prepare('INSERT INTO `edition` (`name`, `content`, `console_idConsole`) 
							   VALUES (:name, :content, :console_idConsole);');
		$stmt->bindParam(':name', $datas['name'], PDO::PARAM_STR);
		$stmt->bindParam(':content', $datas['content'], PDO::PARAM_STR);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->execute();

		$insertedEdition = $pdo->lastInsertId();

		// Insert shops
		if (isset($datas['shops'])) {
			$shopModel = new Shop();
			foreach ($datas['shops'] as $shop) {
				// Adding inserted edition's ID to shop's datas
				$shop['edition_idEdition'] = $insertedEdition;
				$insertedShop = $shopModel->directInsert($shop, $pdo);
			}
		}

		return $insertedEdition;
	}

	/**
	 * Update edition
	 *
	 * @param int $idEdition Edition's ID
	 * @param array $datas Edition's datas
	 * @return bool
	 */
	public function updateEdition($idEdition, $datas)
	{
		$pdo  = $this->db;
		try {
			// Begin transaction to avoid inserting wrong or partial datas
			$pdo->beginTransaction();

			$updatedEdition = $this->directUpdate($idEdition, $datas);

			// If everything went well, commit the transaction
			$pdo->commit();

			return true;
		} catch (PDOException $e) {
			// Cancel the transaction
			$pdo->rollback();
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update an edition without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idEdition Edition's ID
	 * @param array $datas Edition's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idEdition, $datas, $pdo = null)
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
		
		// Check that the edition's ID exists
		$stmt = $pdo->prepare('SELECT `idEdition`
							   FROM `edition`
							   WHERE `idEdition` = :idEdition;');
		$stmt->bindParam(':idEdition', $idEdition, PDO::PARAM_INT);
		$stmt->execute();

		$edition = $stmt->fetch();
		if (!count($edition) || !isset($edition['idEdition'])) {
			return false;
		}

		$stmt = $pdo->prepare('UPDATE `edition`
							   SET `name`              = :name,
								   `content`           = :content,
								   `console_idConsole` = :console_idConsole
							   WHERE `idEdition` =  :idEdition;');
		$stmt->bindParam(':name', $datas['name'], PDO::PARAM_STR);
		$stmt->bindParam(':content', $datas['content'], PDO::PARAM_STR);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->bindParam(':idEdition', $idEdition, PDO::PARAM_INT);
		$stmt->execute();

		// Update shops
		if (isset($datas['shops'])) {
			$shopModel = new Shop();
			foreach ($datas['shops'] as $shop) {
				$shop['edition_idEdition'] = $idEdition;
				$updatedShop = $shopModel->directUpdate($shop['idShop'], $shop, $pdo);
			}
		}

		return true;
	}

	/**
	 * Delete a edition by it's ID
	 *
	 * @param int $idEdition Edition's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteEdition($idEdition)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `edition` 
								   WHERE `idEdition` =  :idEdition;');
			$stmt->bindParam(':idEdition', $idEdition, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing edition.
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