<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    foreach (['App\\' => __DIR__ . '/src/', 'Psr\\' => __DIR__ . '/src/Psr/'] as $prefix => $baseDir) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }
        $file = $baseDir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;
use App\Logging\FileLogger;
use App\Logging\Logger;

error_reporting(E_ALL);
ini_set('display_errors', '0');

Logger::init(new FileLogger(__DIR__ . '/logs/app.log'));

set_exception_handler(function (Throwable $e): void {
    Logger::critical('Unhandled exception', ['exception' => $e]);
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
    Logger::warning('Invalid request rejected', [
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
        'uri'    => substr($_SERVER['REQUEST_URI'] ?? '', 0, 200),
        'ip'     => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    ]);
    Response::error('Invalid request', 400)->send();
    exit;
}

(new Application())->handle($request)->send();
