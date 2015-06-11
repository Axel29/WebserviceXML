<?php
if (isset($_SERVER['IS_DEVELOPER_MODE'])) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', 0);
}

if (!isset($_SESSION)) {
	session_start();
}

date_default_timezone_set('Europe/Paris');

define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

require_once(ROOT . 'core/conf.php');
require_once(ROOT . 'core/parameters.php');
require_once(ROOT . 'core/Auth.php');
require_once(ROOT . 'core/Routing.php');
require_once(ROOT . 'core/BaseModel.php');
require_once(ROOT . 'core/BaseController.php');

$routing = new Routing();
$route   = $routing->parseUri();

$controllerName = ucfirst($route['controller']) . 'Controller';
$controllerFile = 'controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
	require_once($controllerFile);
} else {
	header("HTTP/1.0 404 Not Found");
	exit('Oops an error has occured! Please try again later.');
}

$auth = new Auth();
if ($auth->authentificate()) {
	$controller = new $controllerName;
	if (method_exists($controller, $route['action'])) {
		$routing->call_user_func_named($controller, $route['action'], $route['args']);
	} else {
		header("HTTP/1.0 404 Not Found");
		exit('Oops an error has occured! Please try again later.');
	}
} else {
	header("HTTP/1.0 403 Forbidden");
	exit('You are not allowed to access this ressource.');
}