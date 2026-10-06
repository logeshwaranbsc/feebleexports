<?php

define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

use App\Models\Product;
use App\Models\Quote;
use App\Models\Contact;

echo "--- VERIFYING MODELS ---\n";
$products = Product::getAll();
echo "Products count: " . count($products) . "\n";

$categories = Product::getCategories();
echo "Categories count: " . count($categories) . "\n";

$sizes = Product::getStandardSizes();
echo "Sizes count: " . count($sizes) . "\n";

echo "--- VERIFYING QUOTE MODEL ---\n";
$quoteRes = Quote::save([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'product' => 'Tufted Coir Mats',
    'quantity' => '200'
]);
echo "Quote submission result: " . json_encode($quoteRes) . "\n";

echo "--- VERIFYING CONTACT MODEL ---\n";
$contactRes = Contact::save([
    'name' => 'Test Sender',
    'email' => 'sender@example.com',
    'message' => 'Hello Feeble Exports!'
]);
echo "Contact submission result: " . json_encode($contactRes) . "\n";

echo "--- VERIFYING VIEW RENDER ---\n";
ob_start();
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
require BASE_PATH . '/public/index.php';
$html = ob_get_clean();

echo "Rendered HTML length: " . strlen($html) . " bytes\n";
if (str_contains($html, 'Sustainable Mats for a Greener Tomorrow')) {
    echo "✓ Home View rendering verified successfully!\n";
} else {
    echo "❌ Home View rendering failed.\n";
}
