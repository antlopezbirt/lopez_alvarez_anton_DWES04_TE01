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


    // Devuelve todos los items (versión que usa las entities definidas)

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

    // Devuelve un DTO de un item buscado por ID (Versión sin entities)
    
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


    // Devuelve todos los items de un artista (versión sin hacer uso de las entities)
    public function getItemsByArtist($artista) {

        $conn = $this->db->getConnection();
        $query = "SELECT item.*, GROUP_CONCAT(CONCAT_WS('_',externalid.supplier,externalid.value)) AS externalids ";
        $query .= "FROM item JOIN externalid ON item.id = externalid.itemid WHERE LOWER (item.artist) = LOWER('{$artista}') GROUP BY item.id";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Genera y devuelve un array de DTOs con los datos recibidos de la BD
        
        $itemsDTO = [];

        for($i = 0; $i < count($result); $i++) {
            $fila = $result[$i];

            $externalIdsBruto = explode(',', $fila['externalids']);
            $externalIds = [];
            foreach($externalIdsBruto as $unExternalId) {
                list($supplier, $value) = explode('_', $unExternalId);
                $externalIds[$supplier] = $value;
            }

            echo "Original year: " . $fila['origyear'];
            
            $itemsDTO[] = new ItemDTO(
                $fila['id'],
                $fila['title'],
                $fila['artist'],
                $fila['format'],
                $fila['year'],
                $fila['origyear'],
                $fila['label'],
                $fila['rating'],
                $fila['comment'],
                $fila['buyprice'],
                $fila['condition'],
                $fila['sellprice'],
                $externalIds
            );
        }

        return $itemsDTO;
    }


    // Devuelve todos los items de un artista (versión sin hacer uso de las entities)
    public function getItemsByFormat($formato) {

        $conn = $this->db->getConnection();
        $query = "SELECT item.*, GROUP_CONCAT(CONCAT_WS('_',externalid.supplier,externalid.value)) AS externalids ";
        $query .= "FROM item JOIN externalid ON item.id = externalid.itemid WHERE LOWER (item.format) = LOWER('{$formato}') GROUP BY item.id";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Genera y devuelve un array de DTOs con los datos recibidos de la BD
        
        $itemsDTO = [];

        for($i = 0; $i < count($result); $i++) {
            $fila = $result[$i];

            $externalIdsBruto = explode(',', $fila['externalids']);
            $externalIds = [];

            foreach($externalIdsBruto as $unExternalId) {
                list($supplier, $value) = explode('_', $unExternalId);
                $externalIds[$supplier] = $value;
            }
            
            $itemsDTO[] = new ItemDTO(
                $fila['id'],
                $fila['title'],
                $fila['artist'],
                $fila['format'],
                $fila['year'],
                $fila['origyear'],
                $fila['label'],
                $fila['rating'],
                $fila['comment'],
                $fila['buyprice'],
                $fila['condition'],
                $fila['sellprice'],
                $externalIds
            );
        }

        return $itemsDTO;
    }


    // Devuelve todos los items de un artista (versión sin hacer uso de las entities)
    public function sortItemsByKey($clave, $orden) {

        $conn = $this->db->getConnection();
        $query = "SELECT item.*, GROUP_CONCAT(CONCAT_WS('_',externalid.supplier,externalid.value)) AS externalids ";
        $query .= "FROM item JOIN externalid ON item.id = externalid.itemid GROUP BY item.id ORDER BY `{$clave}` {$orden}";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Genera y devuelve un array de DTOs con los datos recibidos de la BD
        
        $itemsDTO = [];

        for($i = 0; $i < count($result); $i++) {
            $fila = $result[$i];

            $externalIdsBruto = explode(',', $fila['externalids']);
            $externalIds = [];
            foreach($externalIdsBruto as $unExternalId) {
                list($supplier, $value) = explode('_', $unExternalId);
                $externalIds[$supplier] = $value;
            }
            
            
            $itemsDTO[] = new ItemDTO(
                $fila['id'],
                $fila['title'],
                $fila['artist'],
                $fila['format'],
                $fila['year'],
                $fila['origyear'],
                $fila['label'],
                $fila['rating'],
                $fila['comment'],
                $fila['buyprice'],
                $fila['condition'],
                $fila['sellprice'],
                $externalIds
            );
        }

        return $itemsDTO;
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

        // Comienza la transacción
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

        // Finaliza la transacción
        $conn->commit();

        return $this->getItemById($lastId);

    }

    public function update() {

    }

    public function delete() {

    }
}