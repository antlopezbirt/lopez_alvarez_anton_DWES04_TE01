<?php

namespace app\core;
use PDO;
use PDOException;

ini_set('display_errors','On');


class Database {

    private static $instance;
    private $connection;

    private $config = [];

    private function __construct() {
        $this->loadConfig();

        try {
            $this->connection = new PDO(
                "mysql:host={$this->config['host']};dbname={$this->config['dbName']}",
                $this->config['user'],
                $this->config['password']
            );
        } catch(PDOException) {
            header("HTTP/1.0 500 Internal Server Error");
            http_response_code(500);
            echo 'ERROR 500: No se puede conectar a la base de datos, revise la configuración en config/db-config.json';

            die();
        }
        
    }

    public function loadConfig() {
        $json_file = file_get_contents(__DIR__ . '/../../config/db-config.json');
        $this->config = json_decode($json_file, true);
    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}


?>