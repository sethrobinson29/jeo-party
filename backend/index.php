<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $file = $baseDir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

error_reporting(E_ALL);
ini_set('display_errors', '0');

set_exception_handler(function (Throwable $e): void {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal server error']);
});

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$request = Request::createFromGlobals();

if (!$request->isValid()) {
    Response::error('Invalid request', 400)->send();
    exit;
}

(new Application())->handle($request)->send();
