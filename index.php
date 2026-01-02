<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$routes = require __DIR__ . '/config/routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (isset($routes[$method][$uri])) {
    [$controllerClass, $action] = $routes[$method][$uri];

    $controller = new $controllerClass();
    $controller->$action();
} else {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'Endpoint not found',
        'requested' => $uri,
        'method' => $method
    ]);
}