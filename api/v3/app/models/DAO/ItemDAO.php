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


    // Devuelve una ItemEntity a partir de un item buscado por ID

    public function getItemById(int $id) {

        $conn = $this->db->getConnection();
        $query = "SELECT * FROM item WHERE `id` = '{$id}'";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(count($result)>0) {

            $fila = $result[0];

            // Se modela la entidad del Item
            $itemEntidad = new ItemEntity(
                $fila['id'], $fila['title'], $fila['artist'], $fila['format'],
                $fila['year'], $fila['origyear'], $fila['label'], $fila['rating'],
                $fila['comment'], $fila['buyprice'], $fila['condition'], $fila['sellprice']
            );

            return $itemEntidad;

        }

        return false;

    }


    // Devuelve un array de entidades ExternalIdEntity a partir de un ID de item

    public function getExternalIdsByItemId(int $itemId): array {

        $conn = $this->db->getConnection();
        $query = "SELECT * FROM externalid WHERE `itemid` = '{$itemId}'";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $externalIdEntities = [];

        for($i = 0; $i < count($result); $i++) {

            $fila = $result[$i];

            // Se modela una entidad ExternalIdEntity con los datos recibidos de la BD
            $externalIdEntidad = new ExternalIdEntity(
                $fila['id'], $fila['supplier'], $fila['value'], $fila['itemid']
            );

            $externalIdEntities[] = $externalIdEntidad;
            

        }

        // Puede llegar vacío, no hay problema

        return $externalIdEntities;

    }


    // Devuelve todos los items de un artista (versión sin hacer uso de las entities)
    public function getItemsByArtist(string $artista) {

        $conn = $this->db->getConnection();
        $query = "SELECT item.*, GROUP_CONCAT(CONCAT_WS('_',externalid.supplier,externalid.value)) AS externalids ";
        $query .= "FROM item JOIN externalid ON item.id = externalid.itemid WHERE LOWER (item.artist) = LOWER('{$artista}') GROUP BY item.id";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if(count($result)>0) {

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

        return false;
    }


    // Devuelve todos los items de un artista (versión sin hacer uso de las entities)
    public function getItemsByFormat(string $formato) {

        $conn = $this->db->getConnection();
        $query = "SELECT item.*, GROUP_CONCAT(CONCAT_WS('_',externalid.supplier,externalid.value)) AS externalids ";
        $query .= "FROM item JOIN externalid ON item.id = externalid.itemid WHERE LOWER (item.format) = LOWER('{$formato}') GROUP BY item.id";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if(count($result)>0) {

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

        return false;
    }


    // Devuelve todos los items de un artista (versión sin hacer uso de las entities)
    public function sortItemsByKey(string $clave, string $orden) {

        $conn = $this->db->getConnection();
        $query = "SELECT item.*, GROUP_CONCAT(CONCAT_WS('_',externalid.supplier,externalid.value)) AS externalids ";
        $query .= "FROM item JOIN externalid ON item.id = externalid.itemid GROUP BY item.id ORDER BY `{$clave}` {$orden}";
        $stmt = $conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if(count($result)>0) {

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

        false;
    }


    public function create(array $datosJson) {

        $conn = $this->db->getConnection();

        // ------------------- Inserción en la tabla ITEM

        $query = "INSERT INTO item (`title`, `artist`, `format`, `year`, `origyear`, ";
        $query .= "`label`, `rating`, `comment`, `buyprice`, `condition`, `sellprice`) ";
        $query .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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

        return $lastId;

    }




    public function updateItem(array $datosJson) {

        // Extrae el ID en una variable
        $id = $datosJson['id'];
        
        // Busca el item y lo recibe ya modelado a entidad

        $itemEntidad = $this->getItemById($id);

        // Si existe el item, actualiza la entidad, si no devuelve false
        if($itemEntidad) {
            foreach($datosJson as $clave => $valor) {
                // Filtra los datos que no se deben actualizar aquí
                if($clave != 'id' && $clave != 'externalIds') {
                    $metodoSetter = 'set' . ucwords($clave);
                    $itemEntidad->$metodoSetter($valor);
                }
            }

            // Actualiza los valores en la BD a partir de la entidad

            $conn = $this->db->getConnection();

            $query = "UPDATE item SET `title` = ?, `artist` = ?, ";
            $query .= "`format` = ?, `year` = ?, `origyear` = ?, ";
            $query .= "`label` = ?, `rating` = ?, `comment` = ?, ";
            $query .= "`buyprice` = ?, `condition` = ?, ";
            $query .= "`sellprice` = ? WHERE `id` = ?";

            $stmt = $conn->prepare($query);

            // Comienza la transacción
            $conn->beginTransaction();

            $stmt->execute(
                [
                    $itemEntidad->getTitle(), $itemEntidad->getArtist(), $itemEntidad->getFormat(),
                    $itemEntidad->getYear(), $itemEntidad->getOrigYear(), $itemEntidad->getLabel(), 
                    $itemEntidad->getRating(), $itemEntidad->getComment(), $itemEntidad->getBuyPrice(), 
                    $itemEntidad->getCondition(), $itemEntidad->getSellPrice(), $id
                ]
            );

            // Finaliza la transacción
            $conn->commit();


            $itemEntidadActualizada = $this->getItemById($id);

            return ($itemEntidadActualizada);
        }

        return false;

    }



    public function updateExternalIds(array $datosJson) {


        $itemId = $datosJson['id'];

        if(array_key_exists('externalIds', $datosJson)) {

            /*
                Si en los datos para actualizar hay externalIds lo primero que hay que 
                hacer es eliminar de la BD todos los que pertenezcan al Item, ya que los 
                externaIds llegan sin ID propio (son solo clave-valor), y a continuación
                insertar los que hayan llegado.
            */

            $externalIds = $datosJson['externalIds'];

            $conn = $this->db->getConnection();

            // Eliminación externalIds existentes para el item en cuestion
            $query = "DELETE FROM externalid WHERE `itemid` = ?";
            $stmt = $conn->prepare($query);

            // Comienza la transacción
            $conn->beginTransaction();
            $stmt->execute(
                [
                    $itemId
                ]
            );
            // Finaliza la transacción
            $conn->commit();

            // Insercion de los externalIds recibidos

            foreach($externalIds as $clave => $valor) {
                
                // Eliminación externalIds existentes para el item en cuestion
                $query = "INSERT INTO externalid (`supplier`, `value`, `itemid`) ";
                $query .= "VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);

                // Comienza la transacción
                $conn->beginTransaction();
                $stmt->execute(
                    [
                        $clave, $valor, $itemId
                    ]
                );
                // Finaliza la transacción
                $conn->commit();
            }
        }

        // Devolvera los externalIds del item, se hayan actualizado o no, para poder construir el DTO de respuesta

        return $this->getExternalIdsByItemId($itemId);

    }

    public function deleteItem($id) {

        $conn = $this->db->getConnection();

        // Al estar configurado ON DELETE CASCADE, los externalIds asociados al Item se eliminan automaticamente
        $query = "DELETE FROM item WHERE `id` = ?";
        $stmt = $conn->prepare($query);

        // Comienza la transacción
        $conn->beginTransaction();
        $stmt->execute(
            [
                $id
            ]
        );
        // Finaliza la transacción
        $conn->commit();

        return true;

    }
}