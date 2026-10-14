<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Core\Response;
use App\Models\SeoPage;
use App\Models\Product;

class SeoController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? 'all');

        $pages = SeoPage::getAll([
            'search' => $search,
            'status' => $status
        ]);

        View::render('admin/seo/index', [
            'title' => 'SEO Pages - FEEBLE EXPORTS Admin',
            'currentPage' => 'seo',
            'pages' => $pages,
            'search' => $search,
            'status' => $status,
            'admin' => Auth::user()
        ], true, 'admin');
    }

    public function create(): void
    {
        Auth::requireAdmin();

        $products = Product::getAll();

        View::render('admin/seo/editor', [
            'title' => 'Create SEO Page - FEEBLE EXPORTS Admin',
            'currentPage' => 'seo',
            'isEdit' => false,
            'page' => [
                'id' => '',
                'title' => '',
                'slug' => '',
                'status' => 'draft',
                'canonical_url' => '',
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'custom_head_html' => '<meta name="robots" content="index, follow">',
                'hero_image' => '/assets/images/01_plain_handloom.png',
                'hero_heading' => '',
                'hero_form_title' => 'Get a Quote',
                'hero_subheading' => 'Sustainable. Durable. Eco-Friendly.',
                'why_choose_image' => '/assets/images/02_tufted_pattern.png',
                'why_choose_heading' => 'Why Choose Feeble Exports',
                'why_choose_content' => '<p>We are committed to delivering high-quality, eco-friendly coir products with global standards, sustainable practices, and customer-focused solutions.</p>',
                'about_image' => '/assets/images/03_creel_and_rod.png',
                'about_heading' => 'About Feeble Exports',
                'about_content' => '<p>Feeble Exports is a leading manufacturer and exporter of natural coir mats and coir fibre products from Namakkal, Tamil Nadu, India. With a focus on sustainability and quality, we serve global markets with a wide range of eco-friendly products.</p>',
                'content_block_1_heading' => 'Handcrafted Excellence & Export Quality Standards',
                'content_block_1_content' => '<p>Our natural coconut coir entrance mats are manufactured using the finest organic fibres extracted from harvested coconut husks.</p>',
                'selected_projects' => ['plain-handloom', 'tufted', 'creel-rod'],
                'content_block_2_content' => '<p>From residential custom sizes in centimetres and inches to large commercial entrance rolls, FEEBLE EXPORTS caters to international clients worldwide.</p>',
                'show_testimonials' => true,
                'testimonials' => [
                    [
                        'name' => 'Alexander Wright',
                        'role' => 'Procurement Director, EcoLiving UK',
                        'text' => 'The plain handloom and PVC backed mats from Feeble Exports exceeded our European retail standards.',
                        'rating' => 5
                    ]
                ],
                'show_faq' => true,
                'faqs' => [
                    [
                        'question' => 'What sizes are available for export coir mats?',
                        'answer' => 'We offer standard dimensions in both centimeters and inches, as well as custom-cut sizes.'
                    ],
                    [
                        'question' => 'Are your coir mats 100% biodegradable and eco-friendly?',
                        'answer' => 'Yes, crafted purely from 100% natural coconut husk fibres without synthetic additives.'
                    ]
                ]
            ],
            'products' => $products,
            'admin' => Auth::user()
        ], true, 'admin');
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $action = $_POST['submit_action'] ?? 'publish';
        if ($action === 'draft') {
            $_POST['status'] = 'draft';
        } elseif ($action === 'publish') {
            $_POST['status'] = 'published';
        }

        $result = SeoPage::create($_POST);

        Auth::startSession();
        if ($result['success']) {
            $_SESSION['flash_success'] = 'SEO page created successfully!';
            if ($action === 'preview') {
                Response::redirect('/admin/seo/' . $result['page']['id'] . '/preview');
                return;
            }
            Response::redirect('/admin/seo');
        } else {
            $_SESSION['flash_error'] = implode(' ', $result['errors'] ?? ['Failed to create SEO page.']);
            Response::redirect('/admin/seo/create');
        }
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $page = SeoPage::getById($id);
        if (!$page) {
            Auth::startSession();
            $_SESSION['flash_error'] = 'SEO page not found.';
            Response::redirect('/admin/seo');
            return;
        }

        $products = Product::getAll();

        View::render('admin/seo/editor', [
            'title' => 'Edit ' . htmlspecialchars($page['title']) . ' - FEEBLE EXPORTS Admin',
            'currentPage' => 'seo',
            'isEdit' => true,
            'page' => $page,
            'products' => $products,
            'admin' => Auth::user()
        ], true, 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();

        $action = $_POST['submit_action'] ?? 'publish';
        if ($action === 'draft') {
            $_POST['status'] = 'draft';
        } elseif ($action === 'publish') {
            $_POST['status'] = 'published';
        }

        $result = SeoPage::update($id, $_POST);

        Auth::startSession();
        if ($result['success']) {
            $_SESSION['flash_success'] = 'SEO page updated successfully!';
            if ($action === 'preview') {
                Response::redirect('/admin/seo/' . $id . '/preview');
                return;
            }
            Response::redirect('/admin/seo/' . $id . '/edit');
        } else {
            $_SESSION['flash_error'] = implode(' ', $result['errors'] ?? ['Failed to update SEO page.']);
            Response::redirect('/admin/seo/' . $id . '/edit');
        }
    }

    public function delete(string $id): void
    {
        Auth::requireAdmin();

        $page = SeoPage::getById($id);
        if ($page) {
            SeoPage::delete($id);
            Auth::startSession();
            $_SESSION['flash_success'] = "SEO page '{$page['title']}' was deleted successfully.";
        }

        Response::redirect('/admin/seo');
    }

    public function preview(string $id): void
    {
        Auth::requireAdmin();

        $page = SeoPage::getById($id);
        if (!$page) {
            http_response_code(404);
            View::render('404', ['title' => 'Page Not Found - FEEBLE EXPORTS']);
            return;
        }

        // Fetch selected products for landing page
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
            'isPreview' => true
        ]);
    }
}
