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

// Import Core and Controllers
use App\Core\Router;
use App\Core\Response;
use App\Controllers\PageController;
use App\Controllers\ContactController;
use App\Controllers\QuoteController;

use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\EnquiryController;
use App\Controllers\Admin\BlogController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\SeoController;
use App\Controllers\Admin\ProfileController;

$router = new Router();

// Public Page routes
$router->get('/', [PageController::class, 'index']);
$router->get('/home', [PageController::class, 'index']);
$router->get('/discover', [PageController::class, 'discover']);
$router->get('/products', [PageController::class, 'products']);
$router->get('/our-story', [PageController::class, 'story']);
$router->get('/contact', [PageController::class, 'contact']);
$router->get('/all-pages', [PageController::class, 'allPages']);

// Public API / Form Submission routes
$router->post('/api/contact', [ContactController::class, 'submit']);
$router->post('/api/quote', [QuoteController::class, 'submit']);
$router->get('/api/products', [PageController::class, 'apiProducts']);

// Admin Authentication Routes
$router->get('/admin/login', [AuthController::class, 'showLogin']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->get('/admin/logout', [AuthController::class, 'logout']);
$router->post('/admin/logout', [AuthController::class, 'logout']);

// Admin Dashboard Route
$router->get('/admin', [DashboardController::class, 'index']);

// Admin Enquiry Routes
$router->get('/admin/enquiries', [EnquiryController::class, 'index']);
$router->get('/admin/enquiries/{id}', [EnquiryController::class, 'show']);
$router->post('/admin/enquiries/{id}/status', [EnquiryController::class, 'updateStatus']);
$router->post('/admin/enquiries/{id}/delete', [EnquiryController::class, 'delete']);

// Admin Blog Routes
$router->get('/admin/blogs', [BlogController::class, 'index']);
$router->get('/admin/blogs/create', [BlogController::class, 'create']);
$router->post('/admin/blogs/create', [BlogController::class, 'store']);
$router->get('/admin/blogs/{id}/edit', [BlogController::class, 'edit']);
$router->post('/admin/blogs/{id}/edit', [BlogController::class, 'update']);
$router->post('/admin/blogs/{id}/delete', [BlogController::class, 'delete']);

// Admin Product Routes
$router->get('/admin/products', [ProductController::class, 'index']);
$router->get('/admin/products/create', [ProductController::class, 'create']);
$router->post('/admin/products/create', [ProductController::class, 'store']);
$router->get('/admin/products/{id}/edit', [ProductController::class, 'edit']);
$router->post('/admin/products/{id}/edit', [ProductController::class, 'update']);
$router->post('/admin/products/{id}/delete', [ProductController::class, 'delete']);

// Admin SEO Route
$router->get('/admin/seo', [SeoController::class, 'index']);
$router->post('/admin/seo', [SeoController::class, 'update']);

// Admin Profile Route
$router->get('/admin/profile', [ProfileController::class, 'index']);
$router->post('/admin/profile', [ProfileController::class, 'update']);

// Dispatch Request
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestMethod, $requestUri);
