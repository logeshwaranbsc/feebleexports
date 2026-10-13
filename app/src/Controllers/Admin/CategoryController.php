<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;
use App\Models\Category;
use App\Models\Product;

class CategoryController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $categories = Category::getWithProductCounts();
        $totalProducts = count(Product::getAll());

        View::render('admin/categories/index', [
            'title' => 'Category Management - FEEBLE EXPORTS Admin',
            'currentPage' => 'categories',
            'categories' => $categories,
            'totalProducts' => $totalProducts,
            'flash_success' => $_SESSION['flash_success'] ?? null,
            'flash_error' => $_SESSION['flash_error'] ?? null,
            'admin' => Auth::user()
        ], true, 'admin');

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function create(): void
    {
        Auth::requireAdmin();

        View::render('admin/categories/create', [
            'title' => 'Add New Category - FEEBLE EXPORTS Admin',
            'currentPage' => 'categories',
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['form_old'] ?? [],
            'admin' => Auth::user()
        ], true, 'admin');

        unset($_SESSION['form_errors'], $_SESSION['form_old']);
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $result = Category::save($_POST);

        if ($result['success']) {
            Auth::startSession();
            $_SESSION['flash_success'] = 'Category "' . htmlspecialchars($_POST['name'] ?? '') . '" created successfully!';
            Response::redirect('/admin/categories');
        } else {
            Auth::startSession();
            $_SESSION['form_errors'] = $result['errors'];
            $_SESSION['form_old'] = $_POST;
            Response::redirect('/admin/categories/create');
        }
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $category = Category::getById($id);

        if (!$category) {
            http_response_code(404);
            View::render('404', ['title' => 'Category Not Found'], true, 'admin');
            return;
        }

        $mappedProducts = Category::getProducts($category['slug']);

        View::render('admin/categories/edit', [
            'title' => 'Edit Category - ' . $category['name'],
            'currentPage' => 'categories',
            'category' => $category,
            'mappedProducts' => $mappedProducts,
            'errors' => $_SESSION['form_errors'] ?? [],
            'flash_success' => $_SESSION['flash_success'] ?? null,
            'admin' => Auth::user()
        ], true, 'admin');

        unset($_SESSION['form_errors'], $_SESSION['flash_success']);
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();

        $result = Category::update($id, $_POST);

        if ($result['success']) {
            Auth::startSession();
            $_SESSION['flash_success'] = 'Category updated successfully! Mapped products synchronized.';
            Response::redirect('/admin/categories');
        } else {
            Auth::startSession();
            $_SESSION['form_errors'] = $result['errors'];
            Response::redirect("/admin/categories/{$id}/edit");
        }
    }

    public function delete(string $id): void
    {
        Auth::requireAdmin();

        $result = Category::delete($id);

        Auth::startSession();
        if ($result['success']) {
            $_SESSION['flash_success'] = 'Category deleted successfully!';
        } else {
            $_SESSION['flash_error'] = $result['error'] ?? 'Failed to delete category.';
        }

        Response::redirect('/admin/categories');
    }
}
