<?php

namespace app\utils;

use app\models\ItemModel;

class JsonFileHandler {

    private $rutaFichero;

    public function __construct($rutaFichero) {
        $this->rutaFichero = $rutaFichero;
    }

    public function readAllItems() {
        $json = file_get_contents($this->rutaFichero);
        $datosAr = json_decode($json, true);

        $items = array();

        foreach($datosAr as $elemento) {
            $items[] = new ItemModel(
                $elemento['id'],
                $elemento['title'],
                $elemento['artist'],
                $elemento['format'],
                $elemento['year'],
                $elemento['origYear'],
                $elemento['label'],
                $elemento['rating'],
                $elemento['comment'],
                $elemento['buyPrice'],
                $elemento['condition'],
                $elemento['sellPrice'],
                $elemento['externalIds']
            );
        }

        return $items;
    }

    public function writeItem(ItemModel $item) {

        $items = $this->readAllItems();
        array_push($items, $item);

        return $this->saveAllItems($items);
    }

    public function saveAllItems(array $items) {

        if(file_put_contents($this->rutaFichero, json_encode($items, JSON_PRETTY_PRINT))) {
            return true;
        } else {
            return false;
        }
    }
    
}