<?php

namespace app\controllers;

use app\models\DAO\ItemDAO;
use app\models\DTO\ItemDTO;
use app\utils\ApiResponse;

class ItemController {

    private $itemDao;

    public function __construct() {
        $this->itemDao = new ItemDAO();
    }

    public function index() {
        echo "Hola desde el índice de ItemController.";
    }

    public function getAll() {

        $items = $this->itemDao->getAllItems();

        if(isset($items)) {
            $response = new ApiResponse('OK', 200, 'Todos los ítems', $items);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR ', 500, 'No hay ítems', null);
            return $this->sendJsonResponse($response);
        }

    }

    public function getById($id) {

        $item = $this->itemDao->getItemById($id);

        // Si coincide el ID, se envía la respuesta
        if(isset($item)) {
            $response = new ApiResponse('OK', 200, 'Ítem con ID ' . $id, $item);
            return $this->sendJsonResponse($response);
        } else {
            // Si llega hasta aquí, no lo ha encontrado
            $response = new ApiResponse('ERROR: Ítem no encontrado', 404, 'No existe un ítem con ID ' . $id, null);
            return $this->sendJsonResponse($response);
        }
    }


    public function getByArtist($artist) {

        $artist = ucwords(str_replace('-', ' ', $artist));

        $items = $this->itemDao->getItemsByArtist($artist);

        if(isset($items)) {
            $response = new ApiResponse('OK', 200, 'Todos los ítems del artista solicitado (' . $artist . ')', $items);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR ', 404, 'ERROR: Artista no encontrado (' . $artist . ')', null);
            return $this->sendJsonResponse($response);
        }
    }


    public function getByFormat($format) {

        $items = $this->itemDao->getItemsByFormat($format);

        if(isset($items)) {
            $response = new ApiResponse('OK', 200, 'Todos los ítems del formato solicitado (' . $format . ')', $items);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR ', 404, 'ERROR: Formato no encontrado (' . $format . ')', null);
            return $this->sendJsonResponse($response);
        }
    }




    public function sortByKey($key, $order) {

        if ($key === 'externalIds') {
            // No se puede ordenar por externalIds
            $response = new ApiResponse('ERROR ', 400, 'ERROR: No se puede ordenar por externalIds al ser un array', null);
            return $this->sendJsonResponse($response);
        }

        $items = $this->itemDao->sortItemsByKey($key, $order);

        if(isset($items)) {
            $response = new ApiResponse('OK', 200, 'Listado de ítems ordenados según el criterio solicitado (' . $key . ', ' . $order . ')', $items);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR ', 404, 'ERROR: La clave para ordenar no existe (' . $key . ')', null);
            return $this->sendJsonResponse($response);
        }
    }


    public function create($datosJson) {

        $idItemCreado = $this->itemDao->create($datosJson);

        $itemEntidadCreado = $this->itemDao->getItemById($idItemCreado);
        $externalIdsEntidadCreados = $this->itemDao->getExternalIdsByItemId($idItemCreado);

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

    public function update($datosJson) {

        if(array_key_exists('id', $datosJson)) {

            $itemId = $datosJson['id'];

            $itemEntidadActualizado = $this->itemDao->updateItem($datosJson);
            $externalIdsEntidadActualizados = $this->itemDao->updateExternalIds($datosJson);

            $arrayExternalIds = [];

            foreach($externalIdsEntidadActualizados as $unExternalId) {
                $arrayExternalIds[$unExternalId->getSupplier()] = $unExternalId->getValue();
            }

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
                $response = new ApiResponse('OK', 201, 'Item ' . $itemId . ' actualizado.', $itemDTO);
                    return $this->sendJsonResponse($response);
            } else {
                $response = new ApiResponse('ERROR', 500, 'No se pudo acualizar el ítem ' . $itemId . '.', null);
                return $this->sendJsonResponse($response);
            }
        }
    }

    public function delete($datosJson) {
        
        $itemId = $datosJson['id'];

        $itemAEliminar = $this->itemDao->getItemById($itemId);

        if($itemAEliminar) {
            $externalIdsAEliminar = $this->itemDao->getExternalIdsByItemId($itemId);

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

            $itemEntidadEliminado = $this->itemDao->deleteItem($itemId);

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

}