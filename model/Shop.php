<?php
class Shop extends BaseModel
{
	/**
	 * Retrieve every available shops or shops by some param
	 *
	 * @param string $paramName Param's name to find by
	 * @param mixed $paramValue Param's value
	 * @param bool $notPaginated Should paginate or not
	 * @param int $page Current page
	 * @return array $shops Collection of Shops
	 */
	public function findBy($paramName = null, $paramValue = null, $notPaginated = true, $page = 1)
	{
		$this->table = 'shop';
		
		$fields = [
			'`idShop`',
			'`url`',
			'`name`',
			'`price`',
			'`devise`',
			'`edition_idEdition`',
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
			$limit = $page - 1 . ', ' . $this->getLimit();
		}

		$shops = $this->select($fields, $where, [], [], [], $limit);

		return $shops;
	}

	/**
	 * Get list of required fields and their types
	 *
	 * @return array $requiredFields List of required fields as array
	 */
	public static function getRequiredFields()
	{
		$requiredFields = [
			'url'               => 'string',
			'name'              => 'string',
			'price'             => 'float',
			'devise'            => 'string',
			'edition_idEdition' => 'int',
		];

		return $requiredFields;
	}

	/**
	 * Insert a new shop in database.
	 *
	 * @param array $datas Shop's datas
	 * @return int|bool $insertedShop Inserted shop's ID or false if an error has occurred
	 */
	public function insertShop($datas)
	{
		try {
			$insertedShop = $this->directInsert($datas);
			return $insertedShop;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Insert a new shop in database without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param array $datas Shop's datas
	 * @return int $id Inserted shop's ID
	 */
	public function directInsert($datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}

		// Check that the edition's ID exists
		$stmt = $pdo->prepare('SELECT `idEdition`
							   FROM `edition`
							   WHERE `idEdition` = :idEdition;');
		$stmt->bindParam(':idEdition', $datas['edition_idEdition'], PDO::PARAM_INT);
		$stmt->execute();

		$edition = $stmt->fetch();
		if (!count($edition) || !isset($edition['idEdition'])) {
			return false;
		}

		$stmt = $pdo->prepare('INSERT INTO `shop` (`url`, `name`, `price`, `devise`, `edition_idEdition`) 
							   VALUES (:url, :name, :price, :devise, :edition_idEdition);');
		$stmt->bindParam(':url', $datas['url'], PDO::PARAM_STR);
		$stmt->bindParam(':name', $datas['name'], PDO::PARAM_STR);
		$stmt->bindParam(':price', $datas['price'], PDO::PARAM_STR);
		$stmt->bindParam(':devise', $datas['devise'], PDO::PARAM_STR);
		$stmt->bindParam(':edition_idEdition', $datas['edition_idEdition'], PDO::PARAM_INT);
		$stmt->execute();

		return $pdo->lastInsertId();
	}

	/**
	 * Update shop
	 *
	 * @param int $idShop Shop's ID
	 * @param array $datas Shop's datas
	 * @return int $updatedShop Updated shop's ID
	 */
	public function updateShop($idShop, $datas)
	{
		try {
			$updatedShop = $this->directUpdate($idShop, $datas);
			return $updatedShop;
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Update a shop without any try / catch.
	 * Used to make valid transactions for other models.
	 *
	 * @param int $idShop Shop's ID
	 * @param array $datas Shop's datas
	 * @param PDO $pdo Current's PDO object
	 * @return bool
	 */
	public function directUpdate($idShop, $datas, $pdo = null)
	{
		if (!$pdo) {
			$pdo  = $this->db;
		}

		if (isset($datas['edition_idEdition'])) {
			// Check that the edition's ID exists
			$stmt = $pdo->prepare('SELECT `idEdition`
								   FROM `edition`
								   WHERE `idEdition` = :idEdition;');
			$stmt->bindParam(':idEdition', $datas['edition_idEdition'], PDO::PARAM_INT);
			$stmt->execute();

			$edition = $stmt->fetch();
			if (!count($edition) || !isset($edition['idEdition'])) {
				return false;
			}
		}
		
		// Check that the shop's ID exists
		$stmt = $pdo->prepare('SELECT `idShop`
							   FROM `shop`
							   WHERE `idShop` = :idShop;');
		$stmt->bindParam(':idShop', $idShop, PDO::PARAM_INT);
		$stmt->execute();

		$shop = $stmt->fetch();
		if (!count($shop) || !isset($shop['idShop'])) {
			return false;
		}

		$stmt = $pdo->prepare('UPDATE `shop`
								SET `url`               = :url,
									`name`              = :name,
									`price`             = :price,
									`devise`            = :devise,
									`edition_idEdition` = :edition_idEdition
							   WHERE `idShop` =  :idShop;');
		$stmt->bindParam(':url', $datas['url'], PDO::PARAM_STR);
		$stmt->bindParam(':name', $datas['name'], PDO::PARAM_STR);
		$stmt->bindParam(':price', $datas['price'], PDO::PARAM_STR);
		$stmt->bindParam(':devise', $datas['devise'], PDO::PARAM_STR);
		$stmt->bindParam(':edition_idEdition', $datas['edition_idEdition'], PDO::PARAM_INT);
		$stmt->bindParam(':idShop', $idShop, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}

	/**
	 * Delete a shop by it's ID
	 *
	 * @param int $idShop Shop's ID
	 * @return int|bool Number of affected rows or false if an error has occurred
	 */
	public function deleteShop($idShop)
	{
		try {
			$pdo  = $this->db;
			$stmt = $pdo->prepare('DELETE 
								   FROM `shop` 
								   WHERE `idShop` =  :idShop;');
			$stmt->bindParam(':idShop', $idShop, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->rowCount();
		} catch (PDOException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}