<?php
class Role extends BaseModel
{
	const USER_ROLE  = 1;
	const ADMIN_ROLE = 2;

	/**
	 * Retrieve every available roles or roles by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $roles Collection of Roles
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'role';

		$fields = ['`idRole`', '`role`'];
		
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

		$roles = $this->select($fields, $where, [], [], [], $limit);

		return $roles;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'role' => 'string',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new role in database.
	 * If the role already exists, return the existing role's ID.
	 *
	 * @param array $datas Role's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int|bool $insertedRole Inserted role's ID or false if an error has occurred
	 */
	public function insertRole($datas, $pdo = null)
	{
		/*
		 * Check that the role doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingRole = $this->findBy('role', $datas['role'])) {
			$insertedRole = $existingRole[0]['idRole'];
			return $insertedRole;
		} else {
			try {
				if (!$pdo) {
					$pdo  = $this->db;
				}

				$stmt      = $pdo->prepare('INSERT INTO `role` (`role`) 
									   		VALUES (:role);');
				$stmt->bindParam(':role', $datas['role'], PDO::PARAM_STR);
				$stmt->execute();

				$insertedRole = $pdo->lastInsertId();
				return $insertedRole;
			} catch (PDOException $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}
	}

	/**
	 * Update role
	 *
	 * @param int $idRole Role's ID
	 * @param array $datas Role's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool true or false if an error has occurred
	 */
	public function updateRole($idRole, $datas, $pdo = null)
	{
		try {
			if (!$pdo) {
				$pdo  = $this->db;
			}

			$stmt = $pdo->prepare('UPDATE `role` SET `role` = :role WHERE `idRole` = :idRole;');
			$stmt->bindParam(':role', $datas['role'], PDO::PARAM_STR);
			$stmt->bindParam(':idRole', $idRole, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->rowCount();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}