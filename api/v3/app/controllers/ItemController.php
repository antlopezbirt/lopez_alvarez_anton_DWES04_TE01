<?php

namespace app\controllers;

use Exception;
use TypeError;
use Error;
use app\models\DAO\ItemDAO;
use app\models\DTO\ItemDTO;
use app\utils\ApiResponse;

class ItemController {

    private $itemDAO;

    public function __construct() {
        $this->itemDAO = new ItemDAO();
    }

    public function index() {
        $response = new ApiResponse('OK', 200, 'Hola, has llegado al indice de esta API, usa sus endpoints para obtener o modificar datos', null);
        return $this->sendJsonResponse($response);
    }

    public function getAll() {

        $items = $this->itemDAO->getAllItems();

        if(isset($items)) {
            $response = new ApiResponse('OK', 200, 'Todos los ítems (' . count($items) . ')', $items);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR', 500, 'No hay ítems', null);
            return $this->sendJsonResponse($response);
        }

    }

    // Busca un item por ID, recaba sus entidades, las mapea a un DTO y lo devuelve en la respuesta
    public function getById($id) {

        $itemEntidad = $this->itemDAO->getItemById($id);
        $externalIdsEntidades = $this->itemDAO->getExternalIdsByItemId($id);

        $arrayExternalIds = [];

        foreach($externalIdsEntidades as $unExternalId) {
            $arrayExternalIds[$unExternalId->getSupplier()] = $unExternalId->getValue();
        }

        // Si se encuentra el ítem, mapea todo a un DTO y lo envía como respuesta
        if($itemEntidad) {

            // Mapea todo al un DTO para devolverlo al cliente
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
                $arrayExternalIds
            );

            $response = new ApiResponse('OK', 200, 'Ítem con ID ' . $id, $itemDTO);
            return $this->sendJsonResponse($response);
        } else {
            // Si llega hasta aquí, no lo ha encontrado
            $response = new ApiResponse('ERROR', 404, 'No existe un ítem con ID ' . $id, null);
            return $this->sendJsonResponse($response);
        }
    }


    public function getByArtist($artist) {

        $artist = ucwords(str_replace('-', ' ', $artist));

        $items = $this->itemDAO->getItemsByArtist($artist);

        if($items) {
            $response = new ApiResponse('OK', 200, 'Todos los ítems del artista solicitado (' . $artist . ')', $items);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR', 404, 'Artista no encontrado (' . $artist . ')', null);
            return $this->sendJsonResponse($response);
        }
    }


    public function getByFormat($format) {

        $items = $this->itemDAO->getItemsByFormat($format);

        if($items) {
            $response = new ApiResponse('OK', 200, 'Todos los ítems del formato solicitado (' . $format . ')', $items);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR', 404, 'Formato no encontrado (' . $format . ')', null);
            return $this->sendJsonResponse($response);
        }
    }




    public function sortByKey($key, $order) {

        if ($key === 'externalIds') {
            // No se puede ordenar por externalIds
            $response = new ApiResponse('ERROR', 400, 'ERROR: No se puede ordenar por externalIds al ser un array', null);
            return $this->sendJsonResponse($response);
        }

        if (!in_array(strtolower($order), ['asc', 'desc'])) {
            // El tipo de orden es incorrecto
            $response = new ApiResponse('ERROR', 400, 'El tipo de orden solo puede ser ASC o DESC', null);
            return $this->sendJsonResponse($response);
        }

        try {
            
            // Intenta ordenar con la clave y el tipo de orden recibidos
            $items = $this->itemDAO->sortItemsByKey($key, $order);

            if($items) {
                $response = new ApiResponse('OK', 200, 'Listado de ítems ordenados según el criterio solicitado (' . $key . ', ' . $order . ')', $items);
                return $this->sendJsonResponse($response);
            } else {
                $response = new ApiResponse('ERROR', 404, 'No se han encontrado ítems', null);
                return $this->sendJsonResponse($response);
            }
    
        // Si la columna por la que se ha pedido ordenar no existe, o el tipo de orden es erroneo, llega una excepcion y se devuelve un 400
        } catch (Exception $e) {
            
            $response = new ApiResponse('ERROR', 400, 'La clave para ordenar (' . $key . ') no existe', null);
            return $this->sendJsonResponse($response);
        }

    }


    // Guarda un nuevo item en la BD y en caso de exito lo devuelve con 201
    public function create($datosJson) {

        // Intenta modelar los datos a un ItemDTO para ver si están bien formados
        try {
            @$itemDTOModelado = new ItemDTO(
                0, $datosJson['title'], $datosJson['artist'],
                $datosJson['format'], $datosJson['year'], $datosJson['origYear'],
                $datosJson['label'], $datosJson['rating'], $datosJson['comment'],
                $datosJson['buyPrice'], $datosJson['condition'], $datosJson['sellPrice'],
                $datosJson['externalIds']
            );
        } catch(TypeError) {
            $response = new ApiResponse('ERROR', 400, 'Los datos recibidos están mal formados', $datosJson);
            return $this->sendJsonResponse($response);
        }


        // Comprueba que los campos que no son string tengan buen formato
        if ($this->chequearValores($datosJson) !== true) {
                
            $textoRespuesta = $this->chequearValores($datosJson);

            $response = new ApiResponse('ERROR', 400, $textoRespuesta, $datosJson);
            return $this->sendJsonResponse($response);
        }

        $idItemCreado = $this->itemDAO->create($datosJson);

        $itemEntidadCreado = $this->itemDAO->getItemById($idItemCreado);
        $externalIdsEntidadCreados = $this->itemDAO->getExternalIdsByItemId($idItemCreado);

        $arrayExternalIds = [];

        foreach($externalIdsEntidadCreados as $unExternalId) {
            $arrayExternalIds[$unExternalId->getSupplier()] = $unExternalId->getValue();
        }

        if ($itemEntidadCreado) {

            // Mapea todo al un DTO para devolverlo al cliente
            $itemDTO = new ItemDTO(
                $itemEntidadCreado->getId(),
                $itemEntidadCreado->getTitle(),
                $itemEntidadCreado->getArtist(),
                $itemEntidadCreado->getFormat(),
                $itemEntidadCreado->getYear(),
                $itemEntidadCreado->getOrigYear(),
                $itemEntidadCreado->getLabel(),
                $itemEntidadCreado->getRating(),
                $itemEntidadCreado->getComment(),
                $itemEntidadCreado->getBuyprice(),
                $itemEntidadCreado->getCondition(),
                $itemEntidadCreado->getSellPrice(),
                $arrayExternalIds
            );

            $response = new ApiResponse('Created', 201, 'Item guardado', $itemDTO);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR', 500, 'No se pudo guardar el item', null);
            return $this->sendJsonResponse($response);
        }

    }

    // Actualiza datos de un item existente. No tienen por que recibir todos los campos, solo los que cambian.
    public function update($datosJson) {

        if(array_key_exists('id', $datosJson)) {

            // Comprueba que los campos que no son string tengan buen formato
            if ($this->chequearValores($datosJson) !== true) {
                    
                $textoRespuesta = $this->chequearValores($datosJson);

                $response = new ApiResponse('ERROR', 400, $textoRespuesta, $datosJson);
                return $this->sendJsonResponse($response);
            }

            $itemId = $datosJson['id'];

            try {
                $itemEntidadActualizado = $this->itemDAO->updateItem($datosJson);
                $externalIdsEntidadActualizados = $this->itemDAO->updateExternalIds($datosJson);
            } catch (Error) {
                $response = new ApiResponse('ERROR', 400, 'Los datos recibidos están mal formados', $datosJson);
                return $this->sendJsonResponse($response);
            }

            

            $arrayExternalIds = [];

            foreach($externalIdsEntidadActualizados as $unExternalId) {
                $arrayExternalIds[$unExternalId->getSupplier()] = $unExternalId->getValue();
            }

            if ($itemEntidadActualizado) {

                // Mapea el DTO para devolverlo al cliente
                $itemDTO = new ItemDTO(
                    $itemEntidadActualizado->getId(),
                    $itemEntidadActualizado->getTitle(),
                    $itemEntidadActualizado->getArtist(),
                    $itemEntidadActualizado->getFormat(),
                    $itemEntidadActualizado->getYear(),
                    $itemEntidadActualizado->getOrigYear(),
                    $itemEntidadActualizado->getLabel(),
                    $itemEntidadActualizado->getRating(),
                    $itemEntidadActualizado->getComment(),
                    $itemEntidadActualizado->getBuyprice(),
                    $itemEntidadActualizado->getCondition(),
                    $itemEntidadActualizado->getSellPrice(),
                    $arrayExternalIds
                );

                if($itemDTO) {
                    $response = new ApiResponse('OK', 204, 'Item ' . $itemId . ' actualizado.', $itemDTO);
                        return $this->sendJsonResponse($response);
                } else {
                    $response = new ApiResponse('ERROR', 500, 'No se pudo acualizar el ítem ' . $itemId . '.', null);
                    return $this->sendJsonResponse($response);
                }
            } else {
                // No ha encontrado el item
                $response = new ApiResponse('ERROR', 404, 'No existe un ítem con ID ' . $itemId, null);
                return $this->sendJsonResponse($response);
            }
        } else {
            // No ha encontrado el item
            $response = new ApiResponse('ERROR', 400, 'Es necesario un ID para actualizar un ítem', null);
            return $this->sendJsonResponse($response);
        }
    }

    public function delete($datosJson) {
        
        $itemId = $datosJson['id'];

        // Comprueba si los datos están bien formados ("id" con valor entero)
        try {
            $itemAEliminar = $this->itemDAO->getItemById($itemId);
        } catch (TypeError) {
            $response = new ApiResponse('ERROR', 400, 'TypeError: Los datos recibidos están mal formados', $datosJson);
            return $this->sendJsonResponse($response);
        }

        if($itemAEliminar) {
            $externalIdsAEliminar = $this->itemDAO->getExternalIdsByItemId($itemId);

            // Genera el DTO para devolverlo al cliente
            $itemDTO = new ItemDTO(
                $itemAEliminar->getId(),
                $itemAEliminar->getTitle(),
                $itemAEliminar->getArtist(),
                $itemAEliminar->getFormat(),
                $itemAEliminar->getYear(),
                $itemAEliminar->getOrigYear(),
                $itemAEliminar->getLabel(),
                $itemAEliminar->getRating(),
                $itemAEliminar->getComment(),
                $itemAEliminar->getBuyprice(),
                $itemAEliminar->getCondition(),
                $itemAEliminar->getSellPrice(),
                $externalIdsAEliminar
            );

            $itemEntidadEliminado = $this->itemDAO->deleteItem($itemId);

            if ($itemEntidadEliminado) {
                $response = new ApiResponse('OK', 200, 'Item ' . $itemId . ' eliminado.', null);
                return $this->sendJsonResponse($response);
            } else {
                $response = new ApiResponse('ERROR', 500, 'No se pudo eliminar el ítem con ID ' . $itemId . '.', null);
                return $this->sendJsonResponse($response);
            }
        } else {
            $response = new ApiResponse('ERROR', 404, 'No existe un ítem con ID ' . $itemId . '.', null);
            return $this->sendJsonResponse($response);
        }
        
    }


    // Funciones auxiliares

    private function sendJsonResponse(ApiResponse $response) {
        header('Content-Type: application/json');
        http_response_code($response->getCode());
        echo $response->toJson();
    }

    // Comprueba que los valores recibidos cumplan los requisitos, si no genera el mensaje que se enviará en la respuesta HTTP
    public function chequearValores($item) {
        $respuesta = 'ERROR: El campo ';
        if (array_key_exists('year', $item) && (!filter_var($item['year'], FILTER_VALIDATE_INT) || intval($item['year']) <= 1900 || intval($item['year']) >= 2156)) return $respuesta . 'year debe ser un entero entre 1901 y 2155';
        if (array_key_exists('origYear', $item) && (!filter_var($item['origYear'], FILTER_VALIDATE_INT) || intval($item['origYear']) <= 1900 || intval($item['year']) >= 2156)) return $respuesta . 'origYear debe ser un entero entre 1901 y 2155';
        if (array_key_exists('rating', $item) && (!filter_var($item['rating'], FILTER_VALIDATE_INT) || intval($item['rating']) < 1 || intval($item['rating']) > 10)) return $respuesta . 'rating debe ser un entero entre 1 y 10';
        if (array_key_exists('buyPrice', $item) && (!is_numeric($item['buyPrice']) || intval($item['buyPrice']) < 0)) return $respuesta . 'buyPrice debe ser un número mayor o igual que cero';
        if (array_key_exists('condition', $item) && !in_array($item['condition'], ['M','NM','E','VG','G','P'])) return $respuesta . 'condition debe contener un valor de la Goldmine Grading Guide (M, NM, E, VG, G, P)';
        if (array_key_exists('sellPrice', $item) && (!is_numeric($item['sellPrice']) || intval($item['sellPrice']) < 0)) return $respuesta . 'sellPrice debe ser un número mayor o igual que cero';
        if (array_key_exists('externalIds', $item) && !is_array($item['externalIds'])) return $respuesta . 'externalIds debe ser un array asociativo de identificadores externos';

        return true;
    }

}