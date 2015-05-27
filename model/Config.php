<?php
class Config extends BaseModel
{
	/**
	 * Retrieve every available configs or configs by some param
	 *
	 * @param $paramName string Param's name to find by
	 * @param $paramValue mixed Param's value
	 * @return $configs array
	 */
	public function findBy($paramName = null, $paramValue = null)
	{
		$this->table = 'config';
		
		$fields = [
			'`idConfig`',
			'`config`',
			'`type`',
			'`console_idConsole`',
		];

		$where = [];
		$join  = [];
		if ($paramName && $paramValue) {
			if ($paramName == 'idConsole') {
				$join = [
					[
						'type'  => 'INNER JOIN',
						'table' => 'console_has_config chc',
						'on'    => 'config.idConfig = chc.config_idConfig',
					],
					[
						'type'  => 'INNER JOIN',
						'table' => 'console c',
						'on'    => 'c.idConsole = chc.console_idConsole',
					],
				];

				$where = [
					'chc.console_idConsole' => $paramValue,
				];
			} else {
				$where = [
					$paramName => $paramValue,
				];
			}
		}

		$configs = $this->select($fields, $where);

		return $configs;
	}

    /**
     * Retrieve number of available configs for a console by config ID.
     * Used to check that there is at least one config left before deleting as they are required.
     *
     * @param $configId int Config's ID
     * @return $result['nbrConfig'] int Number of existing configs
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
                                                            )
                                  ');
            $stmt->bindParam(':idConfig', $configId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch();
            return $result['nbrConfigs'];
        } catch (PDOException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

	/**
	 * Insert a new config in database.
	 *
	 * @param $datas array Config's name
	 * @return $id int Config's ID
	 */
	public function insertConfig($datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('INSERT INTO `config` (`config`, `type`, `console_idConsole`) 
								   VALUES (:config, :type, :console_idConsole)');
			$stmt->bindParam(':config', $datas['config'], PDO::PARAM_STR);
			$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
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
	 * Update config
	 *
	 * @param $idConfig int Config's ID
	 * @param $datas array Datas to update
	 */
	public function updateConfig($idConfig, $datas)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('UPDATE `config`
								   SET `config` = :config,
								       `type` = :type,
								       `console_idConsole` = :console_idConsole
								   WHERE `idConfig` =  :idConfig');
			$stmt->bindParam(':config', $datas['config'], PDO::PARAM_STR);
			$stmt->bindParam(':type', $datas['type'], PDO::PARAM_STR);
			$stmt->bindParam(':console_idConsole', $datas['console_idConsole'], PDO::PARAM_INT);
			$stmt->bindParam(':idConfig', $idConfig, PDO::PARAM_INT);
			$stmt->execute();

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
	 * Delete a config by it's ID
	 *
	 * @param $id int Config's ID
	 */
	public function deleteConfig($idConfig)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `config` 
								   WHERE `idConfig` =  :idConfig');
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