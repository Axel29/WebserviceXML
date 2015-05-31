<?php
class Edition extends BaseModel
{
	/**
	 * Retrieve every available editions or editions by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $editions array
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
     * @param $editionId int Edition's ID
     * @return $result['nbrEdition'] int Number of existing editions
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
                                                            )
                                  ');
            $stmt->bindParam(':idEdition', $editionId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch();
            return $result['nbrEditions'];
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
	 * @param $datas array Edition's name
	 * @return $id int Edition's ID
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
		    echo $e->getMessage();
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new edition in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Support's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $id Inserted mode's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('INSERT INTO `edition` (`name`, `content`, `console_idConsole`) 
							   VALUES (:name, :content, :console_idConsole)');
		$stmt->bindParam(':name', $datas['name'], PDO::PARAM_STR);
		$stmt->bindParam(':content', $datas['content'], PDO::PARAM_STR);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->execute();

		$editionId = $pdo->lastInsertId();

		// Insert shops
		if (isset($datas['shops'])) {
			$shopModel = new Shop();
			foreach ($datas['shops'] as $shop) {
				$insertedShop = $shopModel->directInsert($shop, $pdo);
			}
		}

		return $editionId;
	}

	/**
	 * Update edition
	 *
	 * @param $idEdition int Edition's ID
	 * @param $datas array Datas to update
	 */
	public function updateEdition($idEdition, $datas)
	{
		$pdo  = $this->db;
		try {
			// Begin transaction to avoid inserting wrong or partial datas
			$pdo->beginTransaction();

			$update = $this->directUpdate($idEdition, $datas);

			// If everything went well, commit the transaction
			$pdo->commit();

			/*
			 * Check that the update was performed on an existing edition.
			 * MySQL won't send any error as, regarding to him, the request is correct, so we have to handle it manually.
			 */
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
	 * @param array $datas Edition's datas
	 * @return int $id Inserted edition's ID
	 * @return bool
	 */
	public function directUpdate($idEdition, $datas)
	{
		$pdo  = $this->db;
		$stmt = $pdo->prepare('UPDATE `edition`
							   SET `name`              = :name,
								   `content`           = :content,
								   `console_idConsole` = :console_idConsole
							   WHERE `idEdition` =  :idEdition');
		$stmt->bindParam(':name', $datas['name'], PDO::PARAM_STR);
		$stmt->bindParam(':content', $datas['content'], PDO::PARAM_STR);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->bindParam(':idEdition', $idEdition, PDO::PARAM_INT);
		$stmt->execute();

		// Update shops
		if (isset($datas['shops'])) {
			$shopModel = new Shop();
			foreach ($datas['shops'] as $shop) {
				$updatedShop = $shopModel->directUpdate($shop['idShop'], $shop);
			}
		}

		return true;
	}

	/**
	 * Delete a edition by it's ID
	 *
	 * @param $id int Edition's ID
	 */
	public function deleteEdition($idEdition)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `edition` 
								   WHERE `idEdition` =  :idEdition');
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