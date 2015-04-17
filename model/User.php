<?php
class User extends BaseModel
{
	public function getUsers() 
	{
		$this->table = array('user U');
		$join = array('user_role UR ON UR.id_user_role = user_role');
		$users = $this->select(array(), array(), array(), $join);

		if (count($users) == 0) {
			echo "Il n'y a pas d'utilisateurs";
		}
		else {
			return $users;
		}
	}

	public function getRoles() 
	{
		$this->table = 'user_role';

		$roles = $this->select();

		if (count($roles) == 0) {
			echo "Il n'y a pas de droits utilisateurs";
		}
		else {
			return $roles;
		}
	}

	public function getUser($login,$password) 
	{
		$this->table = array('user U');
		$join = array('user_role UR ON UR.id_user_role = user_role');
		$query = $this->select(array(), array(), array(), $join, array(), null, 'one');
		if (!count($query)) {
			echo "Utilisateur ou mot de passe inccorect";
		}
		else {
			$_SESSION['userlogged'] = sha1($login);
			$user  = new User();
			$role = $user->getUserRoleById($query['id_user_role']);
			$_SESSION['userId']			= $query['id_user'];
			$_SESSION['userName'] 		= $query['name'];	 
			$_SESSION['userRoleEdit'] 	= $role['can_edit'];
			$_SESSION['userRoleDelete'] = $role['can_delete'];
			$_SESSION['userRoleCreate'] = $role['can_create'];
			return true;
		}
	}

	public function getUserById($id)
	{
		$this->table = array('user');
		$where = array('id_user' => $id);
		$user = $this->select(array(), $where, array(), array(), array(), null, 'one');
		if (!count($user)) {
			echo "Aucun utilisateur avec cet id";
		}
		else {
			return $user;
		}	
	}

	/**
	 * Get every user with a specific role
	 * @param int $roleId
	 * @return array $users
	 */
	public function getUsersByRole($roleId)
	{
		/*
		 * SQL query example:
		 * SELECT *
		 * FROM user U
		 * INNER JOIN user_role UR ON U.user_role = UR.id_user_role
		 * WHERE U.user_role = 1;
		 */
		$this->table = array('user U');
		$fields = array('U.id_user', 'U.name_user', 'U.email');
		$join = array('user_role UR ON U.user_role = UR.id_user_role');
		$where = array('U.user_role' => 1);
		$users = $this->select($fields, $where, array(), $join);

		if (!count($users) || count($users) == 0) {
			return false;
		} else {
			return $users;
		}
	}

	public function getUserRoleById($id)
	{
		$this->table = array('user_role');
		$where = array('id_user_role' => $id);
		$user = $this->select(array(), $where, array(), array(), array(), null, 'one');
		if (!count($user)) {
			echo "Aucun role utilisateur avec cet id";
		}
		else {
			return $user;
		}	
	}

	public function updateUser($user)
	{
		var_dump($user);
		$fields = [];
		$this->table = 'user';
		$where = array('id_user' => $user['id']);

		$query = $this->select($fields, $where);

		if(count($query) == 0){
			self::doInsertUser($user);
		}else{
			self::doUpdateUser($user);
		}
	}

	public function deleteUser($id)
	{
		$this->table = 'user';
		$where = array('id_user' => $id);
		$query = $this->delete($where);

		if(count($query) == 0) {
			echo "There are no user with this id";
		}
		else {
			$deleted = true;
		}
	}
	
	public function insertUser($user)
	{
		$userExists  = false;
		$fields      = [];
		$this->table = 'user';
		$where       = array('email' => $user['email']);
		
		$query       = $this->select($fields, $where, array(), array(), array(), null, 'one');

		// If the user doesn't exist, we insert him
		if(count($query) == 0 || !$query){
			self::doInsertUser($user);
		}else{
			$userExists = true;
		}
	}
	
	/**
	 * Insert the user into database
	 * @param array $user
	 * @return ADMX_Model_User
	 */
	public function doInsertUser($user)
	{
		$fields = array(
						'name_user' => $user['name'],
						'email'     => $user['email'],
						'password'  => sha1($user['password']),
						'hash'      => sha1(uniqid(rand())),
						'user_role' => $user['role']
							);
		$this->table = 'user';
		$this->insert($fields);
		return $this;
	}

	/**
	 * Edit the user values
	 * @param array $user
	 */
	public function editUser($user)
	{
		$userExists  = false;
		$fields      = [];
		$this->table = 'user';
		$where       = array('id_user' => $user['id_user']);
		
		$query       = $this->select($fields, $where, array(), array(), array(), null, 'one');
		var_dump($user);
		// If the user doesn't exist, we insert him
		if(count($query) == 0 || !$query){
			$userExists = true;
		}else{
			self::doUpdateUser($user);
		}
	}

	/**
	 * Update the user
	 * @param array $user
	 * @return ADMX_Model_User
	 */
	public function doUpdateUser($user)
	{
		$fields = array(
					'id_user'   => $user['id_user'],
					'name_user' => $user['name'],
					'email'     => $user['email'],
					'user_role' => $user['role']
				  );

		$where = array('id_user' => $user['id_user']);

		$this->update($fields, $where);
		return $this;
	}

	public function insertUserRole($role)
	{
		$fields = [];
		$this->table = 'user_role';
		$where = array('name' => $role['name']);

		$query = $this->select($fields, $where);

		if(count($query) == 0){
			self::doInsertUserRole($role);
		}else{
			self::doUpdateUserRole($role);
		}
	}

	public function updateUserRole($role)
	{
		$fields = [];
		$this->table = 'user_role';
		$where = array('id_user_role' => $role['id']);

		$query = $this->select($fields, $where);

		if(count($query) != 0){
			self::doUpdateUserRole($role);
		}
	}

	public function deleteUserRole($id)
	{
		$this->table = 'user_role';
		$where = array('id_user_role' => $id);
		$query = $this->delete($where);

		if(count($query) == 0) {
			echo "There are no user role with this id";
		}
		else {
			$deleted = true;
		}
	}
	

	public function doInsertUserRole($role)
	{
		$fields = array(
						'name' => $role['name'],
						'can_create' => $role['create'],
						'can_edit' => $role['edit'],
						'can_read' => 1,
						'can_delete' => $role['delete']
							);
		$this->table = 'user_role';
		$this->insert($fields);
		return $this;
	}


	public function doUpdateUserRole($role)
	{
		$fields = array(
						'can_create' => $role['create'],
						'can_edit' => $role['edit'],
						'can_delete' => $role['delete']
							);
		$where = array('id_user_role' => $role['id']);
		$this->table = 'user_role';
		$this->update($fields, $where);
		return $this;
	}
}