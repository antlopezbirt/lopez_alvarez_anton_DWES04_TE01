<?php


// phpinfo();

use app\core\Router;
use app\core\Database;
use app\models\DAO\ItemDAO;


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

$url = $_SERVER['REQUEST_URI'];

$router = new Router();
$router->match($url);

?>