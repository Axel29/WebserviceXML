<?php
class Console extends BaseModel
{
	/**
	 * Retrieve every available consoles or consoles by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @return array $consoles Collection of consoles
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
     * @param int $consoleId Console's ID
     * @return int|bool $numberOfConsolesLeft Number of existing consoles or false if an error has occurred
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
                                                            );'
                                  );
            $stmt->bindParam(':idConsole', $consoleId, PDO::PARAM_INT);
            $stmt->execute();

			$result               = $stmt->fetch();
			$numberOfConsolesLeft = $result['nbrConsoles'];
            return $numberOfConsolesLeft;
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
			'business_model' => 'string',
			'pegi'           => 'string',
			'release'        => 'date',
			'name'           => 'string',
			'description'    => 'string',
			'cover_front'    => 'string',
			'cover_back'     => 'string',
			'game_idGame'    => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new console in database.
	 *
	 * @param array $datas Console's datas
	 * @return int|bool $consoleId Console's ID or false if an error has occurred
	 */
	public function insertConsole($datas)
	{
		$pdo  = $this->db;
		try {
			// Begin transaction to avoid inserting wrong or partial datas
			$pdo->beginTransaction();

			$insertedConsole = $this->directInsert($datas);

			// If everything went well, commit the transaction
			$pdo->commit();

			return $insertedConsole;
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
	 * @return int $consoleId Inserted console's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
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

	    // Insert datas into 'console' table
		$stmt = $pdo->prepare('INSERT INTO `console` (`business_model`, `pegi`, `release`, `name`, `description`, `cover_front`, `cover_back`, `game_idGame`) 
							   VALUES (:business_model, :pegi, :release, :name, :description, :cover_front, :cover_back, :game_idGame);');
		$stmt->bindParam(':business_model', $datas['business_model'], PDO::PARAM_STR);
		$stmt->bindParam(':pegi', $datas['pegi'], PDO::PARAM_STR);
		$stmt->bindParam(':release', $datas['release'], PDO::PARAM_STR);
		$stmt->bindParam(':name', $datas['name'], PDO::PARAM_INT);
		$stmt->bindParam(':description', $datas['description'], PDO::PARAM_STR);
		$stmt->bindParam(':cover_front', $datas['cover_front'], PDO::PARAM_STR);
		$stmt->bindParam(':cover_back', $datas['cover_back'], PDO::PARAM_STR);
		$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
		$stmt->execute();

		$consoleId = $pdo->lastInsertId();

		// Insert datas into 'mode' table
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

		// Insert datas into 'support' table
		$supportModel = new Support();
		foreach ($datas['supports'] as $support) {
			$insertedSupport = $supportModel->directInsert($support);

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
			$edition['console_idConsole'] = $consoleId;
			$insertedEdition              = $editionModel->directInsert($edition, $pdo);
		}

		// Insert datas into 'dlc' table
		if (isset($datas['dlcs'])) {
			$dlcModel = new Dlc();
			foreach ($datas['dlcs'] as $dlc) {
				$dlc['console_idConsole'] = $consoleId;
				$insertedDlc              = $dlcModel->directInsert($dlc, $pdo);
			}
		}

		// Insert datas into 'config' table
		if (isset($datas['configs'])) {
			$configModel = new Config();
			foreach ($datas['configs'] as $config) {
				$config['console_idConsole'] = $consoleId;
				$insertedConfig              = $configModel->directInsert($config, $pdo);
			}
		}

		// Insert datas into 'test' table and it's sub-tables (comment, analyse)
		if (isset($datas['tests'])) {
			$testModel = new Test();
			foreach ($datas['tests'] as $test) {
				$test['console_idConsole'] = $consoleId;
				$insertedTest              = $testModel->directInsert($test, $pdo);
			}
		}

		return $consoleId;
	}

	/**
	 * Uppegi console
	 *
	 * @param int $idConsole Console's ID
	 * @param array $datas Datas to update
	 */
	public function updateConsole($idConsole, $datas)
	{
		$pdo  = $this->db;
		try {
			// Begin transaction to avoid inserting wrong or partial datas
			$pdo->beginTransaction();

			$updatedConsole = $this->directUpdate($idConsole, $datas);

			// If everything went well, commit the transaction
			$pdo->commit();
			return true;
		} catch (PDOException $e) {
			// Cancel the transaction
		    $pdo->rollback(); echo $e->getMessage();
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update a console without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idConsole Console's ID
	 * @param array $datas Console's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idConsole, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}

		if (isset($datas['game_idGame'])) {
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
		}
		
		// Check that the console's ID exists
		$stmt = $pdo->prepare('SELECT `idConsole`
							   FROM `console`
							   WHERE `idConsole` = :idConsole;');
		$stmt->bindParam(':idConsole', $idConsole, PDO::PARAM_INT);
		$stmt->execute();

		$console = $stmt->fetch();

		if (!count($console) || !isset($console['idConsole'])) {
			return false;
		}

		$stmt = $pdo->prepare('UPDATE `console` 
							   SET `business_model` = :business_model,
							       `pegi` = :pegi,
							       `release` = :release,
							       `name` = :name,
							       `description` = :description,
							       `cover_front` = :cover_front,
							       `cover_back` = :cover_back,
							       `game_idGame` = :game_idGame
							   WHERE `idConsole` = :idConsole;');
		$stmt->bindParam(':business_model', $datas['business_model'], PDO::PARAM_STR);
		$stmt->bindParam(':pegi', $datas['pegi'], PDO::PARAM_STR);
		$stmt->bindParam(':release', $datas['release'], PDO::PARAM_STR);
		$stmt->bindParam(':name', $datas['name'], PDO::PARAM_INT);
		$stmt->bindParam(':description', $datas['description'], PDO::PARAM_STR);
		$stmt->bindParam(':cover_front', $datas['cover_front'], PDO::PARAM_STR);
		$stmt->bindParam(':cover_back', $datas['cover_back'], PDO::PARAM_STR);
		$stmt->bindParam(':game_idGame', $datas['game_idGame'], PDO::PARAM_INT);
		$stmt->bindParam(':idConsole', $idConsole, PDO::PARAM_INT);
		$stmt->execute();

		// Update datas from 'mode' table
		$modeModel = new Mode();

		// Stock every mode IDs to remove deleted ones from console_has_mode table
		$modeIds   = [];
		foreach ($datas['modes'] as $mode) {
			$modeIds[] = (int)$mode['idMode'];
		}

		foreach ($datas['modes'] as $mode) {
			$idMode      = $mode['idMode'];
			$updatedMode = $modeModel->directUpdate($idMode, $mode, $pdo);

			// Check that the relation link exists, otherwise, we add it.
			$stmt = $pdo->prepare('SELECT *
								   FROM `console_has_mode`
								   WHERE `console_idConsole` = :idConsole
								   AND mode_idMode = :idMode;');
			$stmt->bindParam(':idConsole', $idConsole, PDO::PARAM_INT);
			$stmt->bindParam(':idMode', $idMode, PDO::PARAM_INT);
			$stmt->execute();

			$consoleHasMode = $stmt->fetch();
			if (!$consoleHasMode || !isset($consoleHasMode['console_idConsole'])) {
				// Adding relation links
				$stmt = $pdo->prepare('INSERT INTO `console_has_mode` (`console_idConsole`, `mode_idMode`) 
									   VALUES (:console_idConsole, :mode_idMode);');
				$stmt->bindParam(':console_idConsole', $idConsole, PDO::PARAM_INT);
				$stmt->bindParam(':mode_idMode', $idMode, PDO::PARAM_INT);
				$stmt->execute();
			}

			// Remove deleted links
			$implodedModeIds = implode("','", $modeIds);
			$stmt = $pdo->prepare("DELETE
								   FROM `console_has_mode`
								   WHERE `console_idConsole` = :idConsole
								   AND `mode_idMode` NOT IN('" . $implodedModeIds . "');");
			$stmt->bindParam(':idConsole', $idConsole, PDO::PARAM_INT);
			$stmt->execute();
		}

		// Update datas from 'support' table
		$supportModel = new Support();

		// Stock every mode IDs to remove deleted ones from console_has_mode table
		$supportIds   = [];
		foreach ($datas['supports'] as $support) {
			$supportIds[] = (int)$support['idSupport'];
		}

		foreach ($datas['supports'] as $support) {
			$idSupport      = $support['idSupport'];
			$updatedSupport = $supportModel->directUpdate($idSupport, $support, $pdo);

			// Check that the relation link exists, otherwise, we add it.
			$stmt = $pdo->prepare('SELECT *
								   FROM `console_has_support`
								   WHERE `console_idConsole` = :idConsole
								   AND support_idSupport = :idSupport;');
			$stmt->bindParam(':idConsole', $idConsole, PDO::PARAM_INT);
			$stmt->bindParam(':idSupport', $idSupport, PDO::PARAM_INT);
			$stmt->execute();

			$consoleHasSupport = $stmt->fetch();
			if (!$consoleHasSupport || !isset($consoleHasSupport['console_idConsole'])) {
				// Adding relation links
				$stmt = $pdo->prepare('INSERT INTO `console_has_support` (`console_idConsole`, `support_idSupport`) 
									   VALUES (:console_idConsole, :support_idSupport);');
				$stmt->bindParam(':console_idConsole', $idConsole, PDO::PARAM_INT);
				$stmt->bindParam(':support_idSupport', $idSupport, PDO::PARAM_INT);
				$stmt->execute();
			}
		}

		// Remove deleted links
		$implodedSupportIds = implode("','", $supportIds);
		$stmt = $pdo->prepare("DELETE
							   FROM `console_has_support`
							   WHERE `console_idConsole` = :idConsole
							   AND `support_idSupport` NOT IN('" . $implodedSupportIds . "');");
		$stmt->bindParam(':idConsole', $idConsole, PDO::PARAM_INT);
		$stmt->execute();

		// Update datas from 'edition' table and it's sub-table (shop)
		$editionModel = new Edition();
		foreach ($datas['editions'] as $edition) {
			$edition['console_idConsole'] = $idConsole;
			$updatedEdition               = $editionModel->directUpdate($edition['idEdition'], $edition, $pdo);
		}

		// Update datas from 'dlc' table
		if (isset($datas['dlcs'])) {
			$dlcModel = new Dlc();
			foreach ($datas['dlcs'] as $dlc) {
				$dlc['console_idConsole'] = $idConsole;
				$updatedDlc               = $dlcModel->directUpdate($dlc['idDlc'], $dlc, $pdo);
			}
		}

		// Update datas from 'config' table
		if (isset($datas['configs'])) {
			$configModel = new Config();
			foreach ($datas['configs'] as $config) {
				$config['console_idConsole'] = $idConsole;
				$updatedConfig               = $configModel->directUpdate($config['idConfig'], $config, $pdo);
			}
		}

		// Update datas from 'test' table and it's sub-tables (comment, analyse)
		if (isset($datas['tests'])) {
			$testModel = new Test();
			foreach ($datas['tests'] as $test) {
				$test['console_idConsole'] = $idConsole;
				$updatedTest               = $testModel->directUpdate($test['idTest'], $test, $pdo);
			}
		}

		return true;
	}

	/**
	 * Delete a console by it's ID
	 *
	 * @param int $idConsole Console's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteConsole($idConsole)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `console` 
								   WHERE `idConsole` = :idConsole;');
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