<?php
class Config extends BaseModel
{
	/**
	 * Retrieve every available configs or configs by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $configs Collection of configs
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'config';
		
		$fields = [
			'`idConfig`',
			'`config`',
			'`type`',
			'`console_idConsole`',
		];

		$where = [];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		if ($notPaginated) {
			$limit = '';
		} else {
			$entriesPerPage = $this->getLimit();
			$firstEntry     = ($page - 1) * $entriesPerPage;
			$limit          = $firstEntry . ', ' . $entriesPerPage;
		}

		$configs = $this->select($fields, $where, [], [], [], $limit);

		return $configs;
	}

    /**
     * Retrieve number of available configs for a console by config ID.
     * Used to check that there is at least one config left before deleting as they are required.
     *
     * @param int $configId Config's ID
     * @return int $numberOfConfigsLeft Number of existing configs
     */
    public function getNumberOfConfigsLeft($configId)
    {
        try {
            $pdo  = $this->db;
            $stmt = $pdo->prepare('SELECT COUNT(`config`.`idConfig`) AS `nbrConfigs`
								   FROM `config`
								   LEFT JOIN `console` `c` ON `config`.`console_idConsole` = `c`.`idConsole`
								   WHERE `c`.`idConsole` = (SELECT `config`.`console_idConsole`
                                                            FROM `config` `config`
                                                            WHERE `config`.`idConfig` = :idConfig
                                                            );'
                                  );
            $stmt->bindParam(':idConfig', $configId, PDO::PARAM_INT);
            $stmt->execute();

			$result              = $stmt->fetch();
			$numberOfConfigsLeft = $result['nbrConfigs'];
            return $numberOfConfigsLeft;
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
			'config'            => 'string',
			'type'              => 'string',
			'console_idConsole' => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new config in database.
	 *
	 * @param array $datas Config's datas
	 * @return int|bool $insertedConfig Config's ID or false if an error has occurred
	 */
	public function insertConfig($datas)
	{
		try {
			$insertedConfig = $this->directInsert($datas);
			return $insertedConfig;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new config in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Config's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int $insertedConfig Inserted mode's ID
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

		$stmt = $pdo->prepare('INSERT INTO `config` (`config`, `type`, `console_idConsole`) 
							   VALUES (:config, :type, :console_idConsole);');
		$stmt->bindParam(':config', $datas['config'], PDO::PARAM_STR);
		$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->execute();

		$insertedConfig = $pdo->lastInsertId();
		return $insertedConfig;
	}

	/**
	 * Update config
	 *
	 * @param int $idConfig Config's ID
	 * @param array $datas Datas to update
	 * @return bool true or false if an error has occurred
	 */
	public function updateConfig($idConfig, $datas)
	{
		try {
			return $this->directUpdate($idConfig, $datas);;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update an config without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idConfig Config's ID
	 * @param array $datas Config's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idConfig, $datas, $pdo = null)
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
		
		// Check that the config's ID exists
		$stmt = $pdo->prepare('SELECT `idConfig`
							   FROM `config`
							   WHERE `idConfig` = :idConfig;');
		$stmt->bindParam(':idConfig', $idConfig, PDO::PARAM_INT);
		$stmt->execute();

		$config = $stmt->fetch();
		if (!count($config) || !isset($config['idConfig'])) {
			return false;
		}

		$stmt = $pdo->prepare('UPDATE `config`
							   SET `config` = :config,
							       `type` = :type,
							       `console_idConsole` = :console_idConsole
							   WHERE `idConfig` =  :idConfig;');
		$stmt->bindParam(':config', $datas['config'], PDO::PARAM_STR);
		$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
		$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
		$stmt->bindParam(':idConfig', $idConfig, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete a config by it's ID
	 *
	 * @param int $idConfig Config's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteConfig($idConfig)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `config` 
								   WHERE `idConfig` =  :idConfig;');
			$stmt->bindParam(':idConfig', $idConfig, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->rowCount();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}