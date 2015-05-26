<?php
class Test extends BaseModel
{
	/**
	 * Retrieve every available tests or tests by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $tests array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'test t';
		
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
     * @param $testId int Test's ID
     * @return $result['nbrTest'] int Number of existing tests
     */
    public function getNumberOfTestsLeft($testId)
    {
        try {
            $pdo  = $this->db;
            $stmt = $pdo->prepare('SELECT COUNT(`t`.`idTest`) AS `nbrTests`
								   FROM `test` `t`
								   LEFT JOIN `console` `c` ON `t`.`console_idConsole` = `c`.`idConsole`
								   LEFT JOIN `game` `g` ON `c`.`game_idGame` = `g`.`idGame`
								   WHERE `c`.`idConsole` = (SELECT `t`.`console_idConsole`
                                                             FROM `test` `t`
                                                             WHERE `t`.`idTest` = :idTest
                                                            )
                                  ');
            $stmt->bindParam(':idTest', $testId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch();
            return $result['nbrTests'];
        } catch (PDOException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

	/**
	 * Insert a new test in database.
	 *
	 * @param $datas array Test's name
	 * @return $id int Test's ID
	 */
	public function insertTest($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `test` (`report`, `date`, `user_name`, `note`, `console_idConsole`) 
								   VALUES (:report, :date, :user_name, :note, :console_idConsole)');
			$stmt->bindParam(':report', $datas['report'], PDO::PARAM_STR);
			$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
			$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
			$stmt->bindParam(':note', $datas['note'], PDO::PARAM_INT);
			$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
			$stmt->execute();

			return $pdo->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update test
	 *
	 * @param $idTest int Test's ID
	 * @param $datas array Datas to update
	 */
	public function updateTest($idTest, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `test`
								   SET `report` = :report,
								   	   `date` = :date,
								       `user_name` = :user_name,
								       `note` = :note,
								       `console_idConsole` = :console_idConsole
								   WHERE `idTest` =  :idTest');
			$stmt->bindParam(':report', $datas['report'], PDO::PARAM_STR);
			$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
			$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
			$stmt->bindParam(':note', $datas['note'], PDO::PARAM_INT);
			$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
			$stmt->bindParam(':idTest', $idTest, PDO::PARAM_INT);
			$stmt->execute();
			/*
			 * Check that the update was performed on an existing test.
			 * MySQL won't send any error as, regarding to him, the request is correct, so we have to handle it manually.
			 */
			return $stmt->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
			return false;
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	/**
	 * Delete a test by it's ID
	 *
	 * @param $id int Test's ID
	 */
	public function deleteTest($idTest)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `test` 
								   WHERE `idTest` =  :idTest');
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