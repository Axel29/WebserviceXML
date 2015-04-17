<?php
if (isset($_SERVER['IS_DEVELOPER_MODE'])) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', 0);
}

session_start();
date_default_timezone_set('Europe/Paris');

define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

require_once(ROOT . 'core/conf.php');
require_once(ROOT . 'core/parameters.php');
require_once(ROOT . 'core/Routing.php');
require_once(ROOT . 'core/Connection.php');
require_once(ROOT . 'core/BaseModel.php');
require_once(ROOT . 'core/BaseController.php');

// Instanciate PDO's connection in order to use it everywhere
$pdo = new Connection();
$pdo->getPDO();

$routing = new Routing();
$route   = $routing->parseUri();

$controllerName = ucfirst($route['controller']);
$controllerFile = 'controllers/' . $controllerName . 'Controller.php';
if (file_exists($controllerFile)) {
	require_once($controllerFile);
} else {
	echo '404 Controller Not Found';
}

$controller = new $controllerName;
if (method_exists($route['controller'], $route['action'])) {
	$routing->call_user_func_named($controller, $route['action'], $route['args']);
} else {
	echo '404 Page not found';
}

echo '<pre>';
	var_dump($route);
echo '</pre>';