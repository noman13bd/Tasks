<?php 

define('API_NAMESPACE',          'Task');
define('API_DIR_ROOT',            dirname(__FILE__));
define('API_DIR_CLASSES',         API_DIR_ROOT . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
define('API_DIR_CONTROLLERS',     API_DIR_ROOT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR);

require_once API_DIR_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'; 
require_once API_DIR_ROOT . DIRECTORY_SEPARATOR . 'autoload.php'; 
require_once API_DIR_ROOT . DIRECTORY_SEPARATOR . 'functions.php'; 

use Task\Api;
use Task\Database\DbQuery;
use Task\Database\DbCore;
use Task\Database\DbPDOCore;
use Task\Database\DbMySQLiCore;

abstract class Db extends DbCore {};
class DbPDO extends DbPDOCore {};
class DbMySQLi extends DbMySQLiCore {};

/** CORS Middleware */
$config = array(
	/** MySQL database name */
	'database_name' => 'tasksapi',
	/** MySQL hostname */
	'database_host' => 'localhost',
	/** MySQL database username */
	'database_user' => 'root',
	/** MySQL database password */ 
	'database_password' => '',
	/** MySQL Database Table prefix. */
	'database_prefix' => '',
	/** preferred database */
	'database_engine' => 'DbMySQLi',
	/** API CORS */
	'cors' => [
		'enabled' => true,
		'origin' => '*', // can be a comma separated value or array of hosts
		'headers' => [
			'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Authorization, Cache-Control, Content-Type, Access-Control-Allow-Origin',
			'Access-Control-Allow-Credentials' => 'true',
			'Access-Control-Allow-Methods' => 'GET,PUT,POST,DELETE,OPTIONS,PATCH'
		]
	]
);

define('_DB_SERVER_', $config['database_host']);
define('_DB_NAME_', $config['database_name']);

define('_DB_USER_', $config['database_user']);
define('_DB_PASSWD_', $config['database_password']);
define('_DB_PREFIX_',  $config['database_prefix']);
define('_MYSQL_ENGINE_',  $config['database_engine']);

/** API Construct */
$api = new Api([
	'mode' => 'development',
    'debug' => true
]);

$api->add(new \Task\Slim\CorsMiddleware());
$api->config('debug', true);

/**
 * Request Payload
 */
$params = $api->request->get();
$requestPayload = $api->request->post();

$api->group('/api', function () use ($api) {
		/** Add a Product */
		$api->post('/task?', '\Task\Task:addTask')->name('add_task');

		/** Update a single Product */
		$api->put('/task/:taskId?', '\Task\Task:updateTask')->name('update_task');
});

$api->notFound(function () use ($api) {
	$api->response([
		'success' => false,
		'error' => 'Resource Not Found'
	])->setStatus(500);
	return $api->stop();
});

$api->response()->header('Content-Type', 'application/json; charset=utf-8');
$api->run();