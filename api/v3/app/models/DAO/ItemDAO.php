<?php

namespace app\models\DAO;

use PDO;
use app\core\Database;
use app\models\DTO\ItemDTO;

class ItemDAO {

    private $db;


    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {

        $conn = $this->db->getConnection();
        $query = "SELECT * FROM item JOIN artist ON item.artistid = artist.id";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $itemsDTO = [];

        for($i = 0; $i < count($result); $i++) {
            $fila = $result[$i];
            $itemsDTO[] = new ItemDTO(
                $fila['id'],
                $fila['title'],
                $fila['name'],
                $fila['format'],
                $fila['year'],
                $fila['label'],
                $fila['rating'],
                $fila['comment'],
                $fila['buyprice'],
                $fila['condition']
            );
        }

        return $itemsDTO;

    }

    public function getById() {

    }

    public function create() {

    }

    public function update() {

    }

    public function delete() {

    }
}