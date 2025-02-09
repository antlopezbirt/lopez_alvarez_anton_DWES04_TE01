<?php


// phpinfo();

use app\core\Router;
use app\core\Database;
use app\models\DAO\ItemDAO;


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

$url = $_SERVER['REQUEST_URI'];

// $db = Database::getInstance();
// $connection = $db->getConnection();

// $query = "SELECT * FROM participante";
// $stmt = $connection->query($query);
// $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$itemDAO = new ItemDAO();
$items = $itemDAO->getAll();

var_dump($items);

// print_r($result);


// $router = new Router();
// $router->match($url);

?>