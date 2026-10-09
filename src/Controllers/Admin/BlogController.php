<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;
use App\Models\Blog;

class BlogController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $blogs = Blog::getAll();

        View::render('admin/blogs/index', [
            'title' => 'Blog Management - FEEBLE EXPORTS Admin',
            'currentPage' => 'blogs',
            'blogs' => $blogs,
            'admin' => Auth::user()
        ], true, 'admin');
    }

    public function create(): void
    {
        Auth::requireAdmin();

        View::render('admin/blogs/create', [
            'title' => 'Create New Blog - FEEBLE EXPORTS Admin',
            'currentPage' => 'blogs',
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['form_old'] ?? [],
            'admin' => Auth::user()
        ], true, 'admin');

        unset($_SESSION['form_errors'], $_SESSION['form_old']);
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $result = Blog::save($_POST);

        if ($result['success']) {
            Auth::startSession();
            $_SESSION['flash_success'] = 'Blog post created successfully!';
            Response::redirect('/admin/blogs');
        } else {
            Auth::startSession();
            $_SESSION['form_errors'] = $result['errors'];
            $_SESSION['form_old'] = $_POST;
            Response::redirect('/admin/blogs/create');
        }
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $blog = Blog::getById($id);

        if (!$blog) {
            http_response_code(404);
            View::render('404', ['title' => 'Blog Not Found'], true, 'admin');
            return;
        }

        View::render('admin/blogs/edit', [
            'title' => 'Edit Blog - ' . $blog['title'],
            'currentPage' => 'blogs',
            'blog' => $blog,
            'errors' => $_SESSION['form_errors'] ?? [],
            'admin' => Auth::user()
        ], true, 'admin');

        unset($_SESSION['form_errors']);
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();

        $result = Blog::update($id, $_POST);

        if ($result['success']) {
            Auth::startSession();
            $_SESSION['flash_success'] = 'Blog post updated successfully!';
            Response::redirect('/admin/blogs');
        } else {
            Auth::startSession();
            $_SESSION['form_errors'] = $result['errors'];
            Response::redirect("/admin/blogs/{$id}/edit");
        }
    }

    public function delete(string $id): void
    {
        Auth::requireAdmin();

        Blog::delete($id);
        Auth::startSession();
        $_SESSION['flash_success'] = 'Blog post deleted successfully.';

        Response::redirect('/admin/blogs');
    }
}
