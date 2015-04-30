<?php
define('WEBROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
define('BASE_URL', 'http://'.$_SERVER['SERVER_NAME']);

define('SITE_NAME', 'XML - Web Service');

define('DS', DIRECTORY_SEPARATOR);
define('SCHEMAS_PATH', ROOT . 'core' . DS . 'schemas' . DS);

