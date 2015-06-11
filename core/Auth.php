<?php
require_once(ROOT . 'core/BaseModel.php');
require_once(ROOT . 'model/User.php');
require_once(ROOT . 'model/Role.php');
class Auth
{
	/**
	 * Check if the user can access datas
	 *
	 * @return bool
	 */
	public function authentificate()
	{
		if (!isset($_SERVER['HTTP_PUB']) || !isset($_SERVER['HTTP_USEREMAIL']) || !isset($_SERVER['HTTP_APIKEY'])) {
			return false;
		}
		$requestMethod = $_SERVER['REQUEST_METHOD'];

		$publicKey     = $_SERVER['HTTP_PUB'];
		$email         = $_SERVER['HTTP_USEREMAIL'];
		$apiKey        = $_SERVER['HTTP_APIKEY'];

		$userModel     = new User();
		$user          = $userModel->findBy('apiKey', $apiKey);

		// echo '<pre>';var_dump($user[0]);
		if ($user) {
			$privateKey    = hash_hmac("sha256", $user[0]['idUser'] . $user[0]['email'] . time() . $user[0]['apiKey'], $user[0]['apiSecret']);

			if ($user[0]['email'] == $email && $publicKey == $privateKey) {
				if ($user[0]['idRole'] == Role::USER_ROLE && $requestMethod != 'GET') {
					return false;
				} else {
					return true;
				}
			} else {
				return false;
			}
		}

		return false;
	}
}