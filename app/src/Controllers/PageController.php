<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Response;
use App\Models\Product;

class PageController
{
    public function index(): void
    {
        View::render('home', [
            'title' => 'FEEBLE EXPORTS - Sustainable Coir Mats for a Greener Tomorrow',
            'currentPage' => 'home'
        ]);
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
}
