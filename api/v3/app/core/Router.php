<?php

namespace app\core;

class Router {

    protected $routes = array();

    public function __construct() {
        $this->routes = [
            '/' => 'HomeController@index',
            '/items' => 'ItemController@index',
            '/items/get' => 'ItemController@getAll',
            '/item/get/{id}' => 'ItemController@getById',
            '/items/artist/{artist}' => 'ItemController@getByArtist',
            '/items/format/{format}' => 'ItemController@getByFormat',
            '/items/order/{key}/{order}' => 'ItemController@sortByKey',
            '/item/create' => 'ItemController@create',
            '/item/update' => 'ItemController@update',
            '/item/delete' => 'ItemController@delete'
        ];
    }

    public function add($route, $params) {
        $this->routes[$route] = $params;
    }

    public function match($url) {

        // Si la query contiene espacios no es válida. Se devuelve el error y se para la ejecución

        if (str_contains(urldecode($url), ' ')) {
            header("HTTP/1.0 403 Forbidden");
            http_response_code(403);
            echo 'ERROR 403: La URL no puede contener espacios: "' . urldecode($url) . '"';

            die();
        }

        $url = urldecode($url);

        // Elimina la base de la URL solicitada
        $url = str_replace(BASE_URL, '', $url);

        // Limpia la URL de elementos que no sean ruta, como querys (/ruta?param=x), fragments (ruta#fragmento)...
        $url = parse_url($url, PHP_URL_PATH);

        // Obtiene el método HTTP de la petición
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Recorre las rutas definidas
        foreach ($this->routes as $route => $params) {

            // Reemplaza los {parámetros} predefinidos por un patrón regex
            $routePattern = preg_replace('/\{[a-z0-9\-]+\}/', '([a-z0-9\-]+)?', $route);

            // Escapa las barras para la regex
            $routePattern = str_replace('/', '\/', $routePattern);

            // Ejecuta la regex sobre la URL recibida... (case insensitive)
            if (preg_match('/^' . $routePattern . '$/i', $url, $matches)) {

                // Si hay un parámetro por URL, se queda solo con él y desecha lo anterior
                array_shift($matches);

                // Divide el controlador y el método
                list($controller, $method) = explode('@', $params);

                // Agrega el namespace al controlador
                $controller = 'app\\controllers\\' . $controller;

                // Si existe dicho controlador y dicho método
                if(class_exists($controller) && method_exists($controller, $method)) {

                    // Instancia el controlador
                    $controllerInstance = new $controller();

                    // Guarda los datos JSON recibidos en caso de solicitud POST
                    if (in_array($requestMethod, ['POST', 'PUT', 'DELETE'])) {
                        $input = json_decode(file_get_contents('php://input'), true);
                        $matches[] = $input;
                    }

                    // Llama al método del controlador con los datos como parámetro
                    call_user_func_array([$controllerInstance, $method], $matches);
                    return;

                } else {
                    $this->sendNotFound();
                    return;
                }
            }
        }

        // Si llega aquí es que no ha encontrado la ruta
        $this->sendNotFound();
        return;
    }


    private function sendNotFound() {
        header("HTTP/1.0 404 Not Found");
        http_response_code(404);
        echo "404 Not Found";
    }
}