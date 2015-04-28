<?php
class BaseModel
{
	protected $db;
	protected $table;
	protected $id;

	public function __construct()
	{
		$this->db = new PDO(DB, USER, PASSWORD);
	}

	public function select($fields = [], $where = [], $or = [], $joins = [], $order = [], $limit = null, $fetchAll = true)
	{
		// Build the request
		$sql = 'SELECT ';

		// Getting fields to select. If $fields param is empty, we select '*'
		$fields = implode(', ', $fields);
		if (empty($fields)) $fields = '*';
		$sql .= $fields;

		if (is_array($this->table)) {
			$sql .= ' FROM ';
			$i = 0;
			foreach ($this->table as $table) {
				if ($i == 0) {
					$sql .= $table;
				} else {
					$sql .= ', ' . $table;
				}
				$i++;
			}
			$sql .= "\r\n";
		} else {
			$sql .= ' FROM ' . $this->table;
			$sql .= "\r\n";
		}

		// Join tables
		if (!empty($joins)) {
			foreach ($joins as $join) {
				$sql .= ' ' . $join['type'] . ' ' . $join['table'] . ' ON ' . $join['on'];
				$sql .= "\r\n";
			}
		}

		// Getting WHERE clause if not empty.
		if (!empty($where)) {
			$sql .= ' WHERE ';

			$i = 0;
			$sql .= '(';
			foreach ($where as $key => $value) {
				if ($i == 0) {
					$sql .= $key . ' = "' . $value . '"';
				} else {
					$sql .= ' AND ' . $key . ' = "' . $value . '"';
				}				
				$i++;
			}
			$sql .= ')';
			$sql .= "\r\n";
		}

		// Getting OR clause if not empty.
		if (!empty($or)) {
			$sql .= ' OR ';
			$i = 0;
			$sql .= '(';
			foreach ($or as $key => $value) {
				if ($i == 0) {
					$sql .= $key . ' = "' . $value . '"';	
				} else {
					$sql .= ' AND ' . $key . ' = "' . $value . '"';
				}				
				$i++;
			}
			$sql .= ')';
			$sql .= "\r\n";
		}

		if ($order != null) {
			$sql .= ' ORDER BY ';
			$i = 0;
			foreach ($order as $key => $value) {
				if ($i == 0) {
					$sql .= $key . ' ' . $value;	
				} else {
					$sql .= ', ' . $key . ' ' . $value;
				}				
				$i++;
			}
		}

		if ($limit != null) {
			$sql .= ' LIMIT ' . $limit;
			$sql .= "\r\n";
		}

		$sql .= ';';
		
		// Process the request
		try{
			// echo '<pre>'; var_dump($sql); echo '</pre>'; die;
			$query = $this->db->query($sql);
			if ($fetchAll && $query) {
				$query = $query->fetchAll(PDO::FETCH_ASSOC);
			} elseif ($query) {
				$query = $query->fetch(PDO::FETCH_ASSOC);
			}
		} catch(PDOException $e){
			echo 'There was an error processing the request: ' . $e->getMessage();
		} catch(Exception $e) {
			echo 'There was an error processing the request: ' . $e->getMessage();
		}
		return $query;
	}

	public function simpleselect($select, $fetch = 'all')
	{
		try{
			// echo '<pre>'; var_dump($select); echo '</pre>';
			$query = $this->db->query($select);
			if ($fetch == 'all') {
				$query = $query->fetchAll();
			} else {
				$query = $query->fetch();
			}
			}catch(PDOException $e){
			echo 'There was an error processing the request: ' . $e->getMessage();
		}
		catch(Exception $e) {
			echo 'There was an error processing the request: ' . $e->getMessage();
		}
		return $query;
	}


	public function delete($where = [])
	{
		$sql = 'DELETE FROM ' . $this->table;

		if (!empty($where)) {
			$sql .= ' WHERE ';
			$i = 0;
			foreach ($where as $key => $value) {
				if ($i == 0) {
					$sql .= $key . ' = ' . $value;
				} else {
					$sql .= ' AND ' . $key . ' = ' . $value;
				}
				$i++;
			}
		}

		try{
			$query = $this->db->query($sql);
		}catch(PDOException $e){
			echo 'There was an error processing the request: ' . $e->getMessage();
		}

		return $this;
	}

	public function insert($fields = [], $where = [])
	{
		// If we send an id, then we update the field in database
		if (
			( isset($fields['id']) && !empty($fields['id']) ) 
			|| 
			( isset($where['id']) && !empty($where['id']) )
		  ) {
			// UPDATE
			$sql = 'UPDATE ' . $this->table . ' SET ';
			$i = 0;
			foreach ($fields as $key => $value) {
				if ($key == 'id') continue;
				if ($i == 0) {
					$sql .= $key . ' = "' . $value . '"';
				} else {
					$sql .= ', ' . $key . ' = "' . $value . '" ';
				}
				$i++;
			}

			if (!empty($where)) {
				$sql .= ' WHERE ';
				$i = 0;
				foreach ($where as $key => $value) {
					if ($i == 0) {
						$sql .= $key . ' = "' . $value . '"';
					} else {
						$sql .= ' AND ' . $key . ' = "' . $value . '"';
					}
					$i++;
				}
			}

			try{
				$query = $this->db->query($sql);
			}catch(PDOException $e){
				echo 'There was an error processing the request: ' . $e->getMessage();
			}

			return $this;

		} else {
			// INSERT
			$sql = 'INSERT INTO ' . $this->table . ' (';
			$i = 0;
			foreach ($fields as $key => $value) {
				if ($i == 0) {
					$sql .= '`' . $key . '`';
				} else {
					$sql .= ', `' . $key . '`';
				}
				$i++;
			}
			
			$sql .= ') VALUES (';				
			$i = 0;
			foreach ($fields as $key => $value) {
				if ($i == 0) {
					$sql .= '"' . $value . '"';
				} else {
					$sql .= ', "' . $value . '"';
				}
				$i++;
			}
			$sql .= ')';

			try {
				$query = $this->db->query($sql);
			} catch (PDOException $e) {
				echo 'There was an error processing the request: ' . $e->getMessage();
			}
			
			return $this->db->lastInsertId();
		}
	}

	public function update($fields = [], $where = [], $or = [])
	{
		// UPDATE
		$sql = 'UPDATE ' . $this->table . ' SET ';
		$i = 0;
		foreach ($fields as $key => $value) {
			if ($i == 0) {
				$sql .= $key . ' = "' . $value . '"';
			} else {
				$sql .= ', ' . $key . ' = "' . $value . '" ';
			}
			$i++;
		}

		if (!empty($where)) {
			$sql .= ' WHERE ';
			$i = 0;
			$sql .= '(';
			foreach ($where as $key => $value) {
				if ($i == 0) {
					$sql .= $key . ' = ' . $value;
				} else {
					$sql .= ' AND ' . $key . ' = ' . $value;
				}
				$i++;
			}
			$sql .= ')';
		}

		// Getting OR clause if not empty.
		if (!empty($or)) {
			$sql .= ' OR ';
			$i = 0;
			$sql .= '(';
			foreach ($or as $key => $value) {
				if ($i == 0) {
					$sql .= $key . ' = "' . $value . '"';	
				} else {
					$sql .= ' AND ' . $key . ' = "' . $value . '"';
				}				
				$i++;
			}
			$sql .= ')';
		}

		// echo '<pre>'; var_dump($sql); echo '</pre>'; die;
		try {
			$query = $this->db->query($sql);
		} catch(PDOException $e) {
			echo 'There was an error processing the request: ' . $e->getMessage();
		}

		return $this;
	}
}
