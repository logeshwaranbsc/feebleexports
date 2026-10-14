<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;
use App\Models\Product;
use App\Models\Category;

class ProductController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $selectedCategory = $_GET['category'] ?? '';
        $allProducts = Product::getAll();

        if (!empty($selectedCategory) && $selectedCategory !== 'all') {
            $products = array_values(array_filter($allProducts, function ($p) use ($selectedCategory) {
                return ($p['category_slug'] ?? '') === $selectedCategory || ($p['category_id'] ?? '') === $selectedCategory;
            }));
        } else {
            $products = $allProducts;
        }

        $categories = Category::getAll();

        View::render('admin/products/index', [
            'title' => 'Product Management - FEEBLE EXPORTS Admin',
            'currentPage' => 'products',
            'products' => $products,
            'allProductsCount' => count($allProducts),
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'flash_success' => $_SESSION['flash_success'] ?? null,
            'admin' => Auth::user()
        ], true, 'admin');

        unset($_SESSION['flash_success']);
    }

    public function create(): void
    {
        Auth::requireAdmin();

        View::render('admin/products/create', [
            'title' => 'Create New Product - FEEBLE EXPORTS Admin',
            'currentPage' => 'products',
            'categories' => Product::getCategories(),
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['form_old'] ?? [],
            'admin' => Auth::user()
        ], true, 'admin');

        unset($_SESSION['form_errors'], $_SESSION['form_old']);
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $result = Product::save($_POST);

        if ($result['success']) {
            Auth::startSession();
            $_SESSION['flash_success'] = 'Product created successfully!';
            Response::redirect('/admin/products');
        } else {
            Auth::startSession();
            $_SESSION['form_errors'] = $result['errors'];
            $_SESSION['form_old'] = $_POST;
            Response::redirect('/admin/products/create');
        }
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $product = Product::getById($id);

        if (!$product) {
            http_response_code(404);
            View::render('404', ['title' => 'Product Not Found'], true, 'admin');
            return;
        }

        View::render('admin/products/edit', [
            'title' => 'Edit Product - ' . $product['name'],
            'currentPage' => 'products',
            'product' => $product,
            'categories' => Product::getCategories(),
            'errors' => $_SESSION['form_errors'] ?? [],
            'admin' => Auth::user()
        ], true, 'admin');

        unset($_SESSION['form_errors']);
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();

        $result = Product::update($id, $_POST);

        if ($result['success']) {
            Auth::startSession();
            $_SESSION['flash_success'] = 'Product updated successfully!';
            Response::redirect('/admin/products');
        } else {
            Auth::startSession();
            $_SESSION['form_errors'] = $result['errors'];
            Response::redirect("/admin/products/{$id}/edit");
        }
    }

    public function delete(string $id): void
    {
        Auth::requireAdmin();

        Product::delete($id);
        Auth::startSession();
        $_SESSION['flash_success'] = 'Product deleted successfully.';

        Response::redirect('/admin/products');
    }

    public function deleteAll(): void
    {
        Auth::requireAdmin();

        Product::deleteAll();
        Auth::startSession();
        $_SESSION['flash_success'] = 'All products have been deleted successfully.';

        Response::redirect('/admin/products');
    }

    public function bulkDelete(): void
    {
        Auth::requireAdmin();

        $ids = $_POST['selected_ids'] ?? [];
        if (!is_array($ids)) {
            $ids = [];
        }

        if (!empty($ids)) {
            $count = Product::deleteMultiple($ids);
            Auth::startSession();
            $_SESSION['flash_success'] = "{$count} product(s) deleted successfully.";
        } else {
            Auth::startSession();
            $_SESSION['flash_success'] = 'No products were selected for deletion.';
        }

        Response::redirect('/admin/products');
    }

    public function resetDefaults(): void
    {
        Auth::requireAdmin();

        Product::resetDefaults();
        Auth::startSession();
        $_SESSION['flash_success'] = 'Sample default products catalog restored successfully.';

        Response::redirect('/admin/products');
    }
}
