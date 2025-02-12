<?php

namespace app\models\DAO;

use PDO;
use app\core\Database;
use app\models\DTO\ItemDTO;
use app\models\entity\ItemEntity;
use app\models\entity\ExternalIdEntity;

class ItemDAO {

    private $db;


    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Sin usar entidades
    // public function getAllItems() {

    //     $conn = $this->db->getConnection();
    //     $query = "SELECT item.*, JSON_OBJECTAGG(externalid.supplier, externalid.value) AS externalids ";
    //     $query .= "FROM item JOIN externalid ON item.id = externalid.itemid GROUP BY item.id";
    //     $stmt = $conn->query($query);
    //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     // Genera y devuelve un array de DTOs con los datos recibidos de la BD
        
    //     $itemsDTO = [];

    //     for($i = 0; $i < count($result); $i++) {
    //         $fila = $result[$i];
    //         $itemsDTO[] = new ItemDTO(
    //             $fila['id'],
    //             $fila['title'],
    //             $fila['artist'],
    //             $fila['format'],
    //             $fila['year'],
    //             $fila['origyear'],
    //             $fila['label'],
    //             $fila['rating'],
    //             $fila['comment'],
    //             $fila['buyprice'],
    //             $fila['condition'],
    //             $fila['sellPrice'],
    //             json_decode($fila['externalids'])
    //         );
    //     }

    //     return $itemsDTO;

    // }

    // Usando entidades

    public function getAllItems() {

        $conn = $this->db->getConnection();
        $queryItem = "SELECT * FROM item";
        $stmtItem = $conn->query($queryItem);
        $resultItem = $stmtItem->fetchAll(PDO::FETCH_ASSOC);

        
        $itemsDTO = [];

        for($i = 0; $i < count($resultItem); $i++) {
            $filaItem = $resultItem[$i];

            // Primero se recogen los externalIds de la entidad
            $queryExternalId = "SELECT * FROM externalid WHERE `itemid` = {$filaItem['id']}";
            $stmtExternalId = $conn->query($queryExternalId);
            $resultExternalId = $stmtExternalId->fetchAll(PDO::FETCH_ASSOC);

            $externalIdArray = [];

            for($j = 0; $j < count($resultExternalId); $j++) {
                $filaExternalId = $resultExternalId[$j];

                // Se modelan las entidades con los datos recibidos de la BD
                $externalIdEntidad = new ExternalIdEntity(
                    $filaExternalId['id'], $filaExternalId['supplier'], $filaExternalId['value'], $filaExternalId['itemid'],
                );

                // Se acumulan en un array
                $externalIdArray[$externalIdEntidad->getSupplier()] = $externalIdEntidad->getValue();
            }


            // Ahora se modela la entidad del Item
            $itemEntidad = new ItemEntity(
                $filaItem['id'], $filaItem['title'], $filaItem['artist'], $filaItem['format'],
                $filaItem['year'], $filaItem['origyear'], $filaItem['label'], $filaItem['rating'],
                $filaItem['comment'], $filaItem['buyprice'], $filaItem['condition'], $filaItem['sellprice']
            );

            // Por último se mapea todo al DTO
            $itemDTO = new ItemDTO(
                $itemEntidad->getId(),
                $itemEntidad->getTitle(),
                $itemEntidad->getArtist(),
                $itemEntidad->getFormat(),
                $itemEntidad->getYear(),
                $itemEntidad->getOrigYear(),
                $itemEntidad->getLabel(),
                $itemEntidad->getRating(),
                $itemEntidad->getComment(),
                $itemEntidad->getBuyprice(),
                $itemEntidad->getCondition(),
                $itemEntidad->getSellPrice(),
                $externalIdArray
            );

            // Se acumula en el array de DTOs que se devuelve al final
            $itemsDTO[] = $itemDTO;

        }

        return $itemsDTO;

    }

    public function getItemById($id) {

        $conn = $this->db->getConnection();
        $query = "SELECT item.*, JSON_OBJECTAGG(externalid.supplier, externalid.value) AS externalids ";
        $query .= "FROM item JOIN externalid ON item.id = externalid.itemid GROUP BY item.id HAVING item.id = '{$id}'";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Modela y devuelve un DTO con los datos recibidos de la BD
        
        $itemDTO[] = new ItemDTO(
            $result['id'],
            $result['title'],
            $result['artist'],
            $result['format'],
            $result['year'],
            $result['origyear'],
            $result['label'],
            $result['rating'],
            $result['comment'],
            $result['buyprice'],
            $result['condition'],
            $result['sellPrice'],
            json_decode($result['externalids'])
        );

        return $itemDTO;

    }

    public function create($datosJson) {

        // Se modelan los datos recibidos a un DTO

        $itemEntidad = new ItemEntity(
                0, $datosJson['title'], $datosJson['artist'], $datosJson['format'],
                $datosJson['year'], $datosJson['origYear'], $datosJson['label'], 
                $datosJson['rating'], $datosJson['comment'], $datosJson['buyPrice'], 
                $datosJson['condition'], $datosJson['sellPrice']
        );

        $conn = $this->db->getConnection();

        // ------------------- Inserción en la tabla ITEM

        $query = "INSERT INTO item (`title`, `artist`, `format`, `year`, `origyear`, `label`, `rating`, `comment`, `buyprice`, `condition`, `sellprice`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        //$query .= "(title, format) VALUES (?, ?)";
        //$query .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        //$query .= "VALUES (?, ?)";


        $stmt = $conn->prepare($query);

        // Comienza la transacción
        $conn->beginTransaction();
        $stmt->execute(
            [
                $datosJson['title'], $datosJson['artist'], $datosJson['format'],
                $datosJson['year'], $datosJson['origYear'], $datosJson['label'], 
                $datosJson['rating'], $datosJson['comment'], $datosJson['buyPrice'], 
                $datosJson['condition'], $datosJson['sellPrice']
            ]
        );
        // Guarda el ID para usarlo como FK en la tabla de externalIds
        $lastId = $conn->lastInsertId();

        // Finaliza la transacción
        $conn->commit();


        // --------------- Inserciones en la tabla EXTERNALIDS
        $conn->beginTransaction();

        foreach($datosJson['externalIds'] as $clave => $valor) {

            $query = "INSERT INTO externalid (supplier, value, itemid) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);

            $stmt->execute(
                [
                    $clave, $valor, $lastId
                ]
            );
        }
        $conn->commit();

        return $this->getItemById($lastId);

    }

    public function update() {

    }

    public function delete() {

    }
}