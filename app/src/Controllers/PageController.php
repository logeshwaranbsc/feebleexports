<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Response;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Blog;
use App\Models\SeoPage;

class PageController
{
    public function index(): void
    {
        $blogs = Blog::getPublished();

        View::render('home', [
            'title' => 'FEEBLE EXPORTS - Sustainable Coir Mats for a Greener Tomorrow',
            'currentPage' => 'home',
            'blogs' => $blogs
        ]);
    }

    public function blogDetail(string $slug): void
    {
        $blog = Blog::getBySlug($slug);
        if (!$blog || ($blog['status'] ?? 'published') !== 'published') {
            http_response_code(404);
            View::render('404', ['title' => 'Blog Not Found - FEEBLE EXPORTS']);
            return;
        }

        $allPublished = Blog::getPublished();
        $recentBlogs = array_values(array_filter($allPublished, fn($b) => $b['slug'] !== $slug));

        View::render('blog_detail', [
            'title' => ($blog['meta_title'] ?: $blog['title']) . ' | FEEBLE EXPORTS',
            'currentPage' => 'blogs',
            'blog' => $blog,
            'recentBlogs' => array_slice($recentBlogs, 0, 3)
        ]);
    }

    public function blogs(): void
    {
        Response::redirect('/home#home-blogs');
    }

    public function discover(): void
    {
        View::render('discover', [
            'title' => 'Discover Us - FEEBLE EXPORTS',
            'currentPage' => 'discover'
        ]);
    }

    public function products(): void
    {
        $products = Product::getAll();
        $categories = Product::getCategories();
        $sizes = Product::getStandardSizes();

        View::render('products', [
            'title' => 'Coir Mats Collection - FEEBLE EXPORTS',
            'currentPage' => 'products',
            'products' => $products,
            'categories' => $categories,
            'sizes' => $sizes
        ]);
    }

    public function story(): void
    {
        View::render('story', [
            'title' => 'Our Story - A Simple Mat A Brighter Tomorrow | FEEBLE EXPORTS',
            'currentPage' => 'story'
        ]);
    }

    public function contact(): void
    {
        View::render('contact', [
            'title' => 'Contact Us - FEEBLE EXPORTS',
            'currentPage' => 'contact'
        ]);
    }

    public function allPages(): void
    {
        $products = Product::getAll();
        $categories = Product::getCategories();
        $sizes = Product::getStandardSizes();

        View::render('all_pages', [
            'title' => 'Full Presentation - All 5 Pages | FEEBLE EXPORTS',
            'currentPage' => 'all-pages',
            'products' => $products,
            'categories' => $categories,
            'sizes' => $sizes
        ]);
    }

    public function apiProducts(): void
    {
        Response::json([
            'products' => Product::getAll(),
            'categories' => Product::getCategories(),
            'sizes' => Product::getStandardSizes()
        ]);
    }

    public function seoLanding(string $slug): void
    {
        $slug = trim($slug, '/');
        $page = SeoPage::getBySlug($slug);

        if (!$page) {
            http_response_code(404);
            View::render('404', ['title' => 'Page Not Found - FEEBLE EXPORTS']);
            return;
        }

        // If draft, only allow if logged-in admin or preview requested
        $isPreview = isset($_GET['preview']) && $_GET['preview'] == '1';
        if ($page['status'] !== 'published') {
            if (!Auth::check()) {
                http_response_code(404);
                View::render('404', ['title' => 'Page Not Found - FEEBLE EXPORTS']);
                return;
            }
            $isPreview = true;
        }

        // Load associated projects / products
        $allProducts = Product::getAll();
        $selectedProjects = [];
        if (!empty($page['selected_projects'])) {
            foreach ($allProducts as $prod) {
                if (in_array($prod['id'], $page['selected_projects'])) {
                    $selectedProjects[] = $prod;
                }
            }
        }

        View::render('seo_landing', [
            'title' => ($page['meta_title'] ?: $page['title']),
            'metaDescription' => $page['meta_description'] ?? '',
            'metaKeywords' => $page['meta_keywords'] ?? '',
            'canonicalUrl' => $page['canonical_url'] ?? '',
            'ogTitle' => $page['meta_title'] ?: $page['title'],
            'ogDescription' => $page['meta_description'] ?? '',
            'ogImage' => $page['hero_image'] ?: '/assets/images/01_plain_handloom.png',
            'customHeadHtml' => $page['custom_head_html'] ?? '',
            'currentPage' => 'seo-landing',
            'page' => $page,
            'projects' => $selectedProjects,
            'isPreview' => $isPreview
        ]);
    }
}
