<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;
use App\Models\Product;

class ProductController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $products = Product::getAll();

        View::render('admin/products/index', [
            'title' => 'Product Management - FEEBLE EXPORTS Admin',
            'currentPage' => 'products',
            'products' => $products,
            'admin' => Auth::user()
        ], true, 'admin');
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
}
