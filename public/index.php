<?php

declare(strict_types=1);

// Define base paths for application
if (!defined('BASE_PATH')) {
    $appDir = getenv('APP_PATH') ?: ($_ENV['APP_PATH'] ?? null);
    if (!$appDir) {
        $candidate = dirname(__DIR__) . '/app';
        if (is_dir($candidate)) {
            $appDir = $candidate;
        } else {
            $appDir = dirname(__DIR__);
        }
    }
    define('BASE_PATH', rtrim($appDir, '/\\'));
}

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', __DIR__);
}


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
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\SeoController;
use App\Controllers\Admin\ProfileController;
use App\Controllers\Admin\UploadController;

$router = new Router();

// Public Page routes
$router->get('/', [PageController::class, 'index']);
$router->get('/home', [PageController::class, 'index']);
$router->get('/discover', [PageController::class, 'discover']);
$router->get('/products', [PageController::class, 'products']);
$router->get('/our-story', [PageController::class, 'story']);
$router->get('/contact', [PageController::class, 'contact']);
$router->get('/all-pages', [PageController::class, 'allPages']);
$router->get('/blogs', [PageController::class, 'blogs']);
$router->get('/blog/{slug}', [PageController::class, 'blogDetail']);

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
$router->post('/admin/products/delete-all', [ProductController::class, 'deleteAll']);
$router->post('/admin/products/bulk-delete', [ProductController::class, 'bulkDelete']);
$router->post('/admin/products/reset-defaults', [ProductController::class, 'resetDefaults']);
$router->get('/admin/products/{id}/edit', [ProductController::class, 'edit']);
$router->post('/admin/products/{id}/edit', [ProductController::class, 'update']);
$router->post('/admin/products/{id}/delete', [ProductController::class, 'delete']);

// Admin Category Routes
$router->get('/admin/categories', [CategoryController::class, 'index']);
$router->get('/admin/categories/create', [CategoryController::class, 'create']);
$router->post('/admin/categories/create', [CategoryController::class, 'store']);
$router->get('/admin/categories/{id}/edit', [CategoryController::class, 'edit']);
$router->post('/admin/categories/{id}/edit', [CategoryController::class, 'update']);
$router->post('/admin/categories/{id}/delete', [CategoryController::class, 'delete']);

// Admin SEO Routes
$router->get('/admin/seo', [SeoController::class, 'index']);
$router->get('/admin/seo/create', [SeoController::class, 'create']);
$router->post('/admin/seo/create', [SeoController::class, 'store']);
$router->get('/admin/seo/{id}/edit', [SeoController::class, 'edit']);
$router->post('/admin/seo/{id}/edit', [SeoController::class, 'update']);
$router->post('/admin/seo/{id}/delete', [SeoController::class, 'delete']);
$router->get('/admin/seo/{id}/preview', [SeoController::class, 'preview']);

// Admin Profile Route
$router->get('/admin/profile', [ProfileController::class, 'index']);
$router->post('/admin/profile', [ProfileController::class, 'update']);

// Admin Media & File Storage Routes
$router->get('/admin/uploads', [UploadController::class, 'index']);
$router->post('/admin/uploads', [UploadController::class, 'upload']);
$router->post('/admin/uploads/delete', [UploadController::class, 'delete']);
$router->get('/admin/media', [UploadController::class, 'index']);
$router->post('/admin/media', [UploadController::class, 'upload']);
$router->post('/admin/media/delete', [UploadController::class, 'delete']);
$router->get('/admin/media-uploads', [UploadController::class, 'index']);
$router->post('/admin/api/upload', [UploadController::class, 'apiUpload']);

// Dynamic Public SEO Landing Page Route (Evaluated after all specific and admin routes)
$router->get('/{slug}', [PageController::class, 'seoLanding']);

// Dispatch Request
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestMethod, $requestUri);
