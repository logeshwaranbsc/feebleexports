<?php

namespace App\Models;

use App\Core\Database;

class Category
{
    private static string $storagePath = BASE_PATH . '/storage/categories.json';

    public static function getAll(): array
    {
        if (Database::isConnected()) {
            $categories = Database::fetchAll("SELECT * FROM categories ORDER BY display_order ASC, name ASC") ?? [];
            if (!empty($categories)) {
                return $categories;
            }
            $defaults = self::getDefaultCategories();
            foreach ($defaults as $c) {
                Database::execute("INSERT INTO categories (id, name, slug, description, display_order) VALUES (:id, :name, :slug, :description, :display_order) ON CONFLICT (id) DO NOTHING", [
                    'id' => $c['id'],
                    'name' => $c['name'],
                    'slug' => $c['slug'],
                    'description' => $c['description'] ?? '',
                    'display_order' => $c['display_order'] ?? 0
                ]);
            }
            return Database::fetchAll("SELECT * FROM categories ORDER BY display_order ASC, name ASC") ?? [];
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!file_exists(self::$storagePath)) {
            $defaults = self::getDefaultCategories();
            file_put_contents(self::$storagePath, json_encode($defaults, JSON_PRETTY_PRINT));
            return $defaults;
        }

        $categories = json_decode(file_get_contents(self::$storagePath), true);
        if (!is_array($categories)) {
            return [];
        }

        usort($categories, function ($a, $b) {
            $orderA = $a['display_order'] ?? 0;
            $orderB = $b['display_order'] ?? 0;
            if ($orderA === $orderB) {
                return strcmp($a['name'] ?? '', $b['name'] ?? '');
            }
            return $orderA <=> $orderB;
        });

        return $categories;
    }

    public static function getById(string $id): ?array
    {
        if (Database::isConnected()) {
            $category = Database::fetch("SELECT * FROM categories WHERE id = :id", ['id' => $id]);
            if ($category) {
                return $category;
            }
        }

        foreach (self::getAll() as $cat) {
            if ($cat['id'] === $id) {
                return $cat;
            }
        }

        return null;
    }

    public static function getBySlug(string $slug): ?array
    {
        if (Database::isConnected()) {
            $category = Database::fetch("SELECT * FROM categories WHERE slug = :slug", ['slug' => $slug]);
            if ($category) {
                return $category;
            }
        }

        foreach (self::getAll() as $cat) {
            if ($cat['slug'] === $slug) {
                return $cat;
            }
        }

        return null;
    }

    public static function getWithProductCounts(): array
    {
        $categories = self::getAll();
        $products = Product::getAll();

        // Calculate counts
        $counts = [];
        foreach ($products as $p) {
            $slug = $p['category_slug'] ?? '';
            $catId = $p['category_id'] ?? '';
            if ($slug) {
                $counts[$slug] = ($counts[$slug] ?? 0) + 1;
            }
            if ($catId && $catId !== $slug) {
                $counts[$catId] = ($counts[$catId] ?? 0) + 1;
            }
        }

        foreach ($categories as &$c) {
            $c['product_count'] = $counts[$c['slug']] ?? $counts[$c['id']] ?? 0;
        }

        return $categories;
    }

    public static function getProducts(string $categoryIdOrSlug): array
    {
        $allProducts = Product::getAll();
        return array_values(array_filter($allProducts, function ($p) use ($categoryIdOrSlug) {
            return ($p['category_slug'] ?? '') === $categoryIdOrSlug || ($p['category_id'] ?? '') === $categoryIdOrSlug;
        }));
    }

    public static function save(array $data): array
    {
        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $name = htmlspecialchars(trim($data['name']));
        $slug = !empty($data['slug']) ? Blog::slugify($data['slug']) : Blog::slugify($name);
        $id = !empty($data['id']) ? Blog::slugify($data['id']) : $slug;
        $description = htmlspecialchars(trim($data['description'] ?? ''));
        $displayOrder = isset($data['display_order']) && $data['display_order'] !== '' ? (int)$data['display_order'] : 0;

        // Check uniqueness
        if (Database::isConnected()) {
            $existing = Database::fetch("SELECT id FROM categories WHERE id = :id OR slug = :slug", [
                'id' => $id,
                'slug' => $slug
            ]);
            if ($existing) {
                return ['success' => false, 'errors' => ['name' => 'A category with this identifier or slug already exists.']];
            }

            $sql = "INSERT INTO categories (id, name, slug, description, display_order) VALUES (:id, :name, :slug, :description, :display_order)";
            Database::execute($sql, [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'display_order' => $displayOrder
            ]);

            return ['success' => true, 'category' => ['id' => $id, 'name' => $name, 'slug' => $slug]];
        }

        $categories = self::getAll();
        foreach ($categories as $cat) {
            if ($cat['id'] === $id || $cat['slug'] === $slug) {
                return ['success' => false, 'errors' => ['name' => 'A category with this identifier or slug already exists.']];
            }
        }

        $newCategory = [
            'id' => $id,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'display_order' => $displayOrder,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $categories[] = $newCategory;
        file_put_contents(self::$storagePath, json_encode($categories, JSON_PRETTY_PRINT));

        return ['success' => true, 'category' => $newCategory];
    }

    public static function update(string $id, array $data): array
    {
        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $existing = self::getById($id);
        if (!$existing) {
            return ['success' => false, 'errors' => ['general' => 'Category not found.']];
        }

        $name = htmlspecialchars(trim($data['name']));
        $slug = !empty($data['slug']) ? Blog::slugify($data['slug']) : Blog::slugify($name);
        $description = htmlspecialchars(trim($data['description'] ?? ''));
        $displayOrder = isset($data['display_order']) && $data['display_order'] !== '' ? (int)$data['display_order'] : 0;

        $oldSlug = $existing['slug'];

        if (Database::isConnected()) {
            $dup = Database::fetch("SELECT id FROM categories WHERE slug = :slug AND id != :id", [
                'slug' => $slug,
                'id' => $id
            ]);
            if ($dup) {
                return ['success' => false, 'errors' => ['slug' => 'Slug already in use by another category.']];
            }

            Database::execute("
                UPDATE categories 
                SET name = :name, slug = :slug, description = :description, display_order = :display_order, updated_at = NOW() 
                WHERE id = :id
            ", [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'display_order' => $displayOrder
            ]);

            // Sync mapped products with the updated category name and slug
            Database::execute("
                UPDATE products 
                SET category = :name, category_slug = :slug 
                WHERE category_id = :id OR category_slug = :old_slug
            ", [
                'name' => $name,
                'slug' => $slug,
                'id' => $id,
                'old_slug' => $oldSlug
            ]);

            return ['success' => true];
        }

        $categories = self::getAll();
        $found = false;
        foreach ($categories as &$c) {
            if ($c['id'] === $id) {
                $c['name'] = $name;
                $c['slug'] = $slug;
                $c['description'] = $description;
                $c['display_order'] = $displayOrder;
                $c['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }

        if (!$found) {
            return ['success' => false, 'errors' => ['general' => 'Category not found.']];
        }

        file_put_contents(self::$storagePath, json_encode($categories, JSON_PRETTY_PRINT));

        // Sync local storage products
        $products = Product::getAll();
        $changed = false;
        foreach ($products as &$p) {
            if (($p['category_id'] ?? '') === $id || ($p['category_slug'] ?? '') === $oldSlug) {
                $p['category'] = $name;
                $p['category_slug'] = $slug;
                $p['category_id'] = $id;
                $changed = true;
            }
        }
        if ($changed) {
            file_put_contents(BASE_PATH . '/storage/products.json', json_encode($products, JSON_PRETTY_PRINT));
        }

        return ['success' => true];
    }

    public static function delete(string $id): array
    {
        $category = self::getById($id);
        if (!$category) {
            return ['success' => false, 'error' => 'Category not found.'];
        }

        // Count products mapped to this category
        $mappedProducts = self::getProducts($category['slug']);
        if (!empty($mappedProducts)) {
            $count = count($mappedProducts);
            return [
                'success' => false, 
                'error' => "Cannot delete category '{$category['name']}': {$count} product(s) are currently mapped to it. Please re-assign them first."
            ];
        }

        if (Database::isConnected()) {
            Database::execute("DELETE FROM categories WHERE id = :id", ['id' => $id]);
            return ['success' => true];
        }

        $categories = self::getAll();
        $filtered = array_values(array_filter($categories, fn($c) => $c['id'] !== $id));
        file_put_contents(self::$storagePath, json_encode($filtered, JSON_PRETTY_PRINT));
        return ['success' => true];
    }

    private static function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Category Name is required.';
        }
        return $errors;
    }

    public static function getDefaultCategories(): array
    {
        return [
            [
                'id' => 'plain-handloom',
                'name' => 'Plain & Handloom',
                'slug' => 'plain-handloom',
                'description' => 'Classic hand-woven natural coir mat made from high quality 100% natural coconut fibers.',
                'display_order' => 1
            ],
            [
                'id' => 'tufted',
                'name' => 'Tufted',
                'slug' => 'tufted',
                'description' => 'Durable cut-pile tufted coir bristles designed for heavy scraping and soil absorption.',
                'display_order' => 2
            ],
            [
                'id' => 'creel-rod',
                'name' => 'Creel & Rod',
                'slug' => 'creel-rod',
                'description' => 'Heavy-duty steel or wood rod reinforced coir matting built for extreme foot traffic.',
                'display_order' => 3
            ],
            [
                'id' => 'rope-braided',
                'name' => 'Rope & Braided',
                'slug' => 'rope-braided',
                'description' => 'Intricately hand-braided natural coir rope mats featuring rich artisanal patterns.',
                'display_order' => 4
            ],
            [
                'id' => 'backed-mats',
                'name' => 'Backed Mats',
                'slug' => 'backed-mats',
                'description' => 'PVC, Rubber and Latex backed mats offering anti-skid floor protection.',
                'display_order' => 5
            ],
            [
                'id' => 'printed-logo',
                'name' => 'Printed & Logo',
                'slug' => 'printed-logo',
                'description' => 'Custom stencilled and screen-printed mats featuring welcome motifs or corporate logos.',
                'display_order' => 6
            ],
            [
                'id' => 'carpet-rolls',
                'name' => 'Carpet & Rolls',
                'slug' => 'carpet-rolls',
                'description' => 'High-end coir runner mats and master roll matting for halls and commercial custom cutting.',
                'display_order' => 7
            ]
        ];
    }
}
