<?php

declare(strict_types=1);

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/src/';

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

// Import Router and Response
use App\Core\Router;
use App\Core\Response;
use App\Controllers\PageController;
use App\Controllers\ContactController;
use App\Controllers\QuoteController;

$router = new Router();

// Page routes
$router->get('/', [PageController::class, 'index']);
$router->get('/home', [PageController::class, 'index']);
$router->get('/discover', [PageController::class, 'discover']);
$router->get('/products', [PageController::class, 'products']);
$router->get('/our-story', [PageController::class, 'story']);
$router->get('/contact', [PageController::class, 'contact']);
$router->get('/all-pages', [PageController::class, 'allPages']);

// API / Form Submission routes
$router->post('/api/contact', [ContactController::class, 'submit']);
$router->post('/api/quote', [QuoteController::class, 'submit']);
$router->get('/api/products', [PageController::class, 'apiProducts']);

// Dispatch Request
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestMethod, $requestUri);
