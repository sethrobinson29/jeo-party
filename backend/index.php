<?php

declare(strict_types=1);

/**
 * Application Entry Point
 *
 * Validates incoming requests and bootstraps the application
 *
 * PHP Version 8.2.27
 */

// Composer autoloader (use this if you add Composer in the future)
// require_once __DIR__ . '/vendor/autoload.php';

// Simple PSR-4 autoloader
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', '1');

set_exception_handler(function (Throwable $e): void {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
});

// Set CORS headers
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Create request object
$request = Request::createFromGlobals();

// Basic security validation
if (!$request->isValid()) {
    error_log('Invalid request');
    Response::error('Invalid or malicious request detected', 400);
    exit;
}

// Rate limiting check (basic implementation)
if (!$request->checkRateLimit()) {
    error_log('Rate limit exceeded');
    Response::error('Rate limit exceeded', 429);
    exit;
}

// Bootstrap and run application
$app = new Application();
$response = $app->handle($request);
try {
    $response->send();
} catch (Throwable $e) {
    error_log('Error sending Response: ' . $e->getMessage());
}