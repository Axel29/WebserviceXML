<?php
class Console extends BaseModel
{
	/**
	 * Retrieve every available consoles or consoles by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $consoles array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'console';
		
		$fields = [
			'`idConsole`',
			'`business_model`',
			'`pegi`',
			'`release`',
			'`name`',
			'`description`',
			'`cover_front`',
			'`cover_back`',
			'`game_idGame`',
		];

		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		$consoles = $this->select($fields, $where);

		return $consoles;
	}

    /**
     * Retrieve number of available consoles for a game by console ID.
     * Used to check that there is at least one console left before deleting as they are required.
     *
     * @param $consoleId int Console's ID
     * @return $result['nbrConsole'] int Number of existing consoles
     */
    public function getNumberOfConsolesLeft($consoleId)
    {
        try {
            $pdo  = $this->db;
            $stmt = $pdo->prepare('SELECT COUNT(`c`.`idConsole`) AS `nbrConsoles`
								   FROM `console` `c`
								   LEFT JOIN `game` `g` ON `c`.`game_idGame` = `g`.`idGame`
								   WHERE `c`.`game_idGame` = (SELECT `c`.`game_idGame`
                                                             FROM `console` `c`
                                                             WHERE `c`.`idConsole` = :idConsole
                                                            );
                                  ');
            $stmt->bindParam(':idConsole', $consoleId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch();
            return $result['nbrConsoles'];
        } catch (PDOException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

	/**
	 * Insert a new console in database.
	 *
	 * @param $datas array Console's name
	 * @return $id int Console's ID
	 */
	public function insertConsole($datas)
	{
		$pdo  = $this->db;
		try {
		    // Begin transaction to avoid inserting wrong or partial datas
		    $pdo->beginTransaction();

		    // Insert datas into 'console' table
			$stmt = $pdo->prepare('INSERT INTO `console` (`business_model`, `pegi`, `release`, `name`, `description`, `cover_front`, `cover_back`, `game_idGame`) 
								   VALUES (:business_model, :pegi, :release, :name, :description, :cover_front, :cover_back, :game_idGame);');
			$stmt->bindParam(':business_model', $datas['business_model'], PDO::PARAM_STR);
			$stmt->bindParam(':pegi', $datas['pegi'], PDO::PARAM_STR);
			$stmt->bindParam(':release', $datas['release'], PDO::PARAM_STR);
			$stmt->bindParam(':name', $datas['name'], PDO::PARAM_INT);
			$stmt->bindParam(':description', $datas['description'], PDO::PARAM_INT);
			$stmt->bindParam(':cover_front', $datas['cover_front'], PDO::PARAM_INT);
			$stmt->bindParam(':cover_back', $datas['cover_back'], PDO::PARAM_INT);
			$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
			$stmt->execute();

			$consoleId = $pdo->lastInsertId();

			// Insert datas into 'modes' table
			$modeModel = new Mode();
			foreach ($datas['modes'] as $mode) {
				$insertedMode = $modeModel->directInsert($mode);

				// Adding relation links
				$stmt = $pdo->prepare('INSERT INTO `console_has_mode` (`console_idConsole`, `mode_idMode`) 
									   VALUES (:console_idConsole, :mode_idMode);');
				$stmt->bindParam(':console_idConsole', $consoleId, PDO::PARAM_INT);
				$stmt->bindParam(':mode_idMode', $insertedMode, PDO::PARAM_INT);
				$stmt->execute();
			}

			// Insert datas into 'supports' table
			$supportModel = new Support();
			foreach ($datas['supports'] as $support) {
				$insertedSupport = $supportModel->insertSupport($support);

				// Adding relation links
				$stmt = $pdo->prepare('INSERT INTO `console_has_support` (`console_idConsole`, `support_idSupport`) 
									   VALUES (:console_idConsole, :support_idSupport);');
				$stmt->bindParam(':console_idConsole', $consoleId, PDO::PARAM_INT);
				$stmt->bindParam(':support_idSupport', $insertedSupport, PDO::PARAM_INT);
				$stmt->execute();
			}

			// Insert datas into 'edition' table and it's sub-table (shop)
			$editionModel = new Edition();
			foreach ($datas['editions'] as $edition) {
				$insertedEdition = $editionModel->directInsert($edition);
			}

			// Insert datas into 'dlc' table
			$dlcModel = new Dlc();
			foreach ($datas['dlcs'] as $dlc) {
				$insertedDlc = $dlcModel->directInsert($dlc);
			}

			// Insert datas into 'config' table
			$configModel = new Config();
			foreach ($datas['configs'] as $config) {
				$insertedConfig = $configModel->directInsert($config);
			}

			// Insert datas into 'test' table and it's sub-tables (comment, analyse)
			$testModel = new Test();
			foreach ($datas['test'] as $test) {
				$insertedTest = $testModel->directInsert($test);
			}

			// If everything went well, commit the transaction
			$pdo->commit();

			return $consoleId;
		} catch (PDOException $e) {
			// Cancel the transaction
		    $pdo->rollback();

			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Uppegi console
	 *
	 * @param $idConsole int Console's ID
	 * @param $datas array Datas to update
	 */
	public function updateConsole($idConsole, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `console`
								   SET `report` = :report,
								   	   `date` = :date,
								       `user_name` = :user_name,
								       `note` = :note,
								       `console_idConsole` = :console_idConsole
								   WHERE `idConsole` =  :idConsole;');
			$stmt->bindParam(':report', $datas['report'], PDO::PARAM_STR);
			$stmt->bindParam(':date', $datas['date'], PDO::PARAM_STR);
			$stmt->bindParam(':user_name', $datas['user_name'], PDO::PARAM_STR);
			$stmt->bindParam(':note', $datas['note'], PDO::PARAM_INT);
			$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
			$stmt->bindParam(':idConsole', $idConsole, PDO::PARAM_INT);
			$stmt->execute();
			/*
			 * Check that the update was performed on an existing console.
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
	 * Delete a console by it's ID
	 *
	 * @param $id int Console's ID
	 */
	public function deleteConsole($idConsole)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `console` 
								   WHERE `idConsole` =  :idConsole;');
			$stmt->bindParam(':idConsole', $idConsole, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing console.
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