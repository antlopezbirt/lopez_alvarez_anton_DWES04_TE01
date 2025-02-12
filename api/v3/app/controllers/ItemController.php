<?php

namespace app\controllers;

use app\models\DAO\ItemDAO;
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

    public function create($datosJson) {

        $itemCreado = $this->itemDao->create($datosJson);

        if ($itemCreado) {
            $response = new ApiResponse('Created', 201, 'Item guardado', $itemCreado);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR', 500, 'No se pudo guardar', $itemCreado);
            return $this->sendJsonResponse($response);
        }

        /* // Instancia un ItemModel con los datos para guardarlo
        $item = new ItemModel($newId, $datosJson['title'], $datosJson['artist'],
            $datosJson['format'], $datosJson['year'], $datosJson['origYear'], 
            $datosJson['label'], $datosJson['rating'], $datosJson['comment'], 
            $datosJson['buyPrice'], $datosJson['condition'], 
            $datosJson['sellPrice'], $datosJson['externalIds']
        );

        // Intenta guardar el ítem creado en el fichero
        if ($this->dataHandler->writeItem($item)) {
            $response = new ApiResponse('Created', 201, 'Item guardado', $item);
            return $this->sendJsonResponse($response);
        } else {
            $response = new ApiResponse('ERROR', 500, 'No se pudo guardar', $item);
            return $this->sendJsonResponse($response);
        } */
    }

    public function update($datosJson) {

        /* // Extrae el ID en una variable
        $id = $datosJson['id'];
        
        // Recoge todos los items
        $items = $this->dataHandler->readAllItems();

        foreach($items as $item) {
            // Si coincide el ID, modifica el item los datos recibidos
            if($item->getId() == $id) {

                foreach($datosJson as $dato => $valor) {
                    if($dato != 'id') {
                        $metodoSet = "set" . ucwords($dato);
                        $item->$metodoSet($valor);
                    }
                }

                if($this->dataHandler->saveAllItems($items)) {
                    $response = new ApiResponse('OK', 201, 'Item ' . $id . ' actualizado.', $item);
                    return $this->sendJsonResponse($response);
                } else {
                    $response = new ApiResponse('ERROR', 500, 'No se pudo acualizar el ítem ' . $id . '.', null);
                    return $this->sendJsonResponse($response);
                }

                break;
            }
        }

        // Si llega hasta aquí, no lo ha encontrado
        $response = new ApiResponse('ERROR: Ítem no encontrado', 404, 'No existe un ítem con ID ' . $id, null);
        return $this->sendJsonResponse($response);
 */
        
    }

    public function delete($datosJson) {
        
        /* $id = $datosJson['id'];

        $items = $this->dataHandler->readAllItems();

        foreach($items as $key => $item) {
            // Si coincide el ID, elimina el item del array
            if($item->getId() == $id) {
                unset($items[$key]);
                $items = array_values($items);

                if ($this->dataHandler->saveAllItems($items)) {
                    $response = new ApiResponse('OK', 201, 'Item ' . $id . ' eliminado.', $item);
                    return $this->sendJsonResponse($response);
                } else {
                    $response = new ApiResponse('ERROR', 500, 'Item ' . $id . ' no se pudo eliminar.', null);
                    return $this->sendJsonResponse($response);
                }

                break;
            }
        }

        // No se encontró el item
        $response = new ApiResponse('ERROR', 404, 'No existe un ítem con ID ' . $id . '.', null);
        return $this->sendJsonResponse($response); */
    }


    // Funciones auxiliares

    private function sendJsonResponse(ApiResponse $response) {
        header('Content-Type: application/json');
        http_response_code($response->getCode());
        echo $response->toJson();
    }

}