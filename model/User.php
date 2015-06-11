<?php
class User extends BaseModel
{
	/**
	 * Retrieve every available users or users by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $users Collection of Users
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'user u';

		$fields = ['`idUser`', '`email`', '`username`', '`password`', '`apiKey`', '`apiSecret`', '`r`.`idRole`', '`r`.`role`',];
		
		$where = [];
		$join = [
			[
				'type'  => 'INNER JOIN',
				'table' => 'role r',
				'on'    => 'u.role = r.idRole',
			],
		];
		if ($paramName && $paramValue) {
			$where = [
				$paramName => $paramValue,
			];
		}

		if ($notPaginated) {
			$limit = '';
		} else {
			$limit = $page - 1 . ', ' . $this->getLimit();
		}

		$users = $this->select($fields, $where, [], $join, [], $limit);

		return $users;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'email'    => 'string',
			'username' => 'string',
			'password' => 'string',
			'role'     => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new user in database.
	 * If the user already exists, return the existing user's ID.
	 *
	 * @param array $datas User's datas
	 * @param PDO $pdo Current's PDO object
	 * @return int|bool $insertedUser Inserted user's ID or false if an error has occurred
	 */
	public function insertUser($datas, $pdo = null)
	{
		/*
		 * Check that the user doesn't already exist.
		 * If so, return this ID
		 */
		if ($existingUser = $this->findBy('email', $datas['email'])) {
			$insertedUser = $existingUser[0]['idUser'];
			return $insertedUser;
		} else {
			try {
				if (!$pdo) {
					$pdo  = $this->db;
				}

				$apiKey    = uniqid();
				$apiSecret = substr(md5(uniqid() . uniqid()), 0, 15);

				$stmt      = $pdo->prepare('INSERT INTO `user` (`email`, `username`, `password`, `apiKey`, `apiSecret`, `role`) 
									   		VALUES (:email, :username, :password, :apiKey, :apiSecret, :role);');
				$stmt->bindParam(':email', $datas['email'], PDO::PARAM_STR);
				$stmt->bindParam(':username', $datas['username'], PDO::PARAM_STR);
				$stmt->bindParam(':password', $datas['password'], PDO::PARAM_STR);
				$stmt->bindParam(':apiKey', $apiKey, PDO::PARAM_STR);
				$stmt->bindParam(':apiSecret', $apiSecret, PDO::PARAM_STR);
				$stmt->bindParam(':role', $datas['role'], PDO::PARAM_INT);
				$stmt->execute();

				$insertedUser = $pdo->lastInsertId();
				return $insertedUser;
			} catch (PDOException $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}
	}

	/**
	 * Update user
	 *
	 * @param int $idUser User's ID
	 * @param array $datas User's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool true or false if an error has occurred
	 */
	public function updateUser($idUser, $datas, $pdo = null)
	{
		try {
			if (!$pdo) {
				$pdo  = $this->db;
			}
			
			$queryString = 'UPDATE `user` 
					  		SET `email` = :email, `username` = :username, `password` = :password';

			if (isset($datas['role'])) {
				$queryString .= ', role = :role';
			}

			$queryString .= ' WHERE `idUser` =  :idUser;';

			$stmt = $pdo->prepare($queryString);
			$stmt->bindParam(':email', $datas['email'], PDO::PARAM_STR);
			$stmt->bindParam(':username', $datas['username'], PDO::PARAM_STR);
			$stmt->bindParam(':password', $datas['password'], PDO::PARAM_STR);
			if (isset($datas['role'])) {
				$stmt->bindParam(':role', $datas['role'], PDO::PARAM_INT);
			}
			$stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Delete an user by it's ID
	 *
	 * @param int $idUser User's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteUser($idUser)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `user` 
								   WHERE `idUser` =  :idUser;');
			$stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
			$stmt->execute();

			/*
			 * Check that the update was performed on an existing user.
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