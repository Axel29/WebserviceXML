<?php
class Test extends BaseModel
{
	/**
	 * Retrieve every available tests or tests by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $tests Collection of Tests
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'test';
		
		$fields = [
			'`idTest`',
			'`report`',
			'DATE_FORMAT(`date`, "%Y-%m-%dT%H:%i:%s") as `date`',
			'`user_name`',
			'`note`',
			'`console_idConsole`',
		];

		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		$tests = $this->select($fields, $where);

		return $tests;
	}

    /**
     * Retrieve number of available tests for a console by test ID.
     * Used to check that there is at least one test left before deleting as they are required.
     *
     * @param int $testId Test's ID
     * @return int|bool $numberOfTestsLeft Number of existing tests or false if an error has occurred
     */
    public function getNumberOfTestsLeft($testId)
    {
        try {
            $pdo  = $this->db;
            $stmt = $pdo->prepare('SELECT COUNT(`t`.`idTest`) AS `nbrTests`
								   FROM `test` `t`
								   LEFT JOIN `console` `c` ON `t`.`console_idConsole` = `c`.`idConsole`
								   WHERE `c`.`idConsole` = (SELECT `t`.`console_idConsole`
                                                             FROM `test` `t`
                                                             WHERE `t`.`idTest` = :idTest
                                                            );'
                                  );
            $stmt->bindParam(':idTest', $testId, PDO::PARAM_INT);
            $stmt->execute();

			$result            = $stmt->fetch();
			$numberOfTestsLeft = $result['nbrTests'];
            return $numberOfTestsLeft;
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
			'report'            => 'string',
			'date'              => 'date',
			'user_name'         => 'string',
			'note'              => 'int',
			'console_idConsole' => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new test in database.
	 *
	 * @param array $datas Test's datas
	 * @return int|bool $insertedTest Test's ID or false if an error has occurred
	 */
	public function insertTest($datas)
	{
		$pdo  = $this->db;
		try {
			// Begin transaction to avoid inserting wrong or partial datas
			$pdo->beginTransaction();

			$insertedTest = $this->directInsert($datas);

			// If everything went well, commit the transaction
			$pdo->commit();

			return $insertedTest;
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
	 * @param array $datas Support's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedTest Inserted mode's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('INSERT INTO `test` (`report`, `date`, `user_name`, `note`, `console_idConsole`) 
							   VALUES (:report, :date, :user_name, :note, :console_idConsole)');
		$stmt->bindParam(':report', $datas['report'], PDO::PARAM_STR);
		$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
		$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
		$stmt->bindParam(':note', $datas['note'], PDO::PARAM_INT);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->execute();

		$insertedTest = $pdo->lastInsertId();

		// Insert comments
		if (isset($datas['comments'])) {
			$commentModel = new Comment();
			foreach ($datas['comments'] as $comment) {
				$comment['test_idTest'] = $insertedTest;
				$insertedComment = $commentModel->directInsert($comment, $pdo);
			}
		}

		// Insert analyses
		if (isset($datas['analyses'])) {
			$analyseModel = new Analyse();
			foreach ($datas['analyses'] as $analyse) {
				$analyse['test_idTest'] = $insertedTest;
				$insertedAnalyse = $analyseModel->directInsert($analyse, $pdo);
			}
		}
		return $insertedTest;
	}

	/**
	 * Update test
	 *
	 * @param int $idTest Test's ID
	 * @param array $datas Test's datas
	 * @return int|bool $updatedTest Updated test's ID or false if an error has occurred
	 */
	public function updateTest($idTest, $datas)
	{
		$pdo  = $this->db;
		try {
			// Begin transaction to avoid inserting wrong or partial datas
			$pdo->beginTransaction();

			$updatedTest = $this->directUpdate($idTest, $datas);

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
	 * @param int $idTest Test's ID
	 * @param array $datas Edition's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idTest, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}
		$stmt = $pdo->prepare('UPDATE `test`
							   SET `report`            = :report,
								   `date`              = :date,
								   `user_name`         = :user_name,
								   `note`              = :note,
								   `console_idConsole` = :console_idConsole
							   WHERE `idTest` =  :idTest;');
		$stmt->bindParam(':report', $datas['report'], PDO::PARAM_STR);
		$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
		$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
		$stmt->bindParam(':note', $datas['note'], PDO::PARAM_INT);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->bindParam(':idTest', $idTest, PDO::PARAM_INT);
		$stmt->execute();

		// Update comments
		if (isset($datas['comments'])) {
			$commentModel = new Comment();
			foreach ($datas['comments'] as $comment) {
				$commentModel->directUpdate($comment['idComment'], $comment);
			}
		}

		// Update analyses
		if (isset($datas['analyses'])) {
			$analyseModel = new Analyse();
			foreach ($datas['analyses'] as $analyse) {
				// Stock the update into a variable otherwise the transaction will be blocked forever !
				$updatedAnalyse = $analyseModel->directUpdate($analyse['idAnalyse'], $analyse);
			}
		}

		return true;
	}

	/**
	 * Delete a test by it's ID
	 *
	 * @param int $idTest Test's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteTest($idTest)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `test` 
								   WHERE `idTest` =  :idTest;');
			$stmt->bindParam(':idTest', $idTest, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing test.
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