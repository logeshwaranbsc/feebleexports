<?php

namespace App\Models;

use App\Core\Database;

class Product
{
    private static string $storagePath = BASE_PATH . '/storage/products.json';

    public static function getAll(): array
    {
        if (Database::isConnected()) {
            $dbProducts = Database::fetchAll("SELECT * FROM products ORDER BY name ASC");
            if (!empty($dbProducts)) {
                return $dbProducts;
            }
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists(self::$storagePath)) {
            $defaults = self::getDefaultProducts();
            file_put_contents(self::$storagePath, json_encode($defaults, JSON_PRETTY_PRINT));
            return $defaults;
        }

        $products = json_decode(file_get_contents(self::$storagePath), true);
        if (empty($products)) {
            $defaults = self::getDefaultProducts();
            file_put_contents(self::$storagePath, json_encode($defaults, JSON_PRETTY_PRINT));
            return $defaults;
        }

        return is_array($products) ? $products : [];
    }

    public static function getById(string $id): ?array
    {
        if (Database::isConnected()) {
            $product = Database::fetch("SELECT * FROM products WHERE id = :id", ['id' => $id]);
            if ($product) {
                return $product;
            }
        }

        foreach (self::getAll() as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }
        return null;
    }

    public static function save(array $data): array
    {
        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $id = !empty($data['id']) ? Blog::slugify($data['id']) : Blog::slugify($data['name']);
        $price = isset($data['price']) && $data['price'] !== '' ? (float)$data['price'] : null;
        $offerPrice = isset($data['offer_price']) && $data['offer_price'] !== '' ? (float)$data['offer_price'] : null;

        if (Database::isConnected()) {
            $existing = Database::fetch("SELECT id FROM products WHERE id = :id", ['id' => $id]);
            if ($existing) {
                return ['success' => false, 'errors' => ['id' => 'Product ID/Slug already exists. Please choose a unique identifier.']];
            }

            $sql = "INSERT INTO products (id, name, category, category_slug, image, description, price, offer_price, status) VALUES (:id, :name, :category, :category_slug, :image, :description, :price, :offer_price, :status)";
            Database::execute($sql, [
                'id' => $id,
                'name' => htmlspecialchars(trim($data['name'])),
                'category' => htmlspecialchars(trim($data['category'])),
                'category_slug' => Blog::slugify($data['category']),
                'image' => htmlspecialchars(trim($data['image'] ?? '/assets/images/01_plain_handloom.png')),
                'description' => htmlspecialchars(trim($data['description'] ?? '')),
                'price' => $price,
                'offer_price' => $offerPrice,
                'status' => $data['status'] ?? 'active'
            ]);

            return ['success' => true, 'product' => ['id' => $id]];
        }

        $products = self::getAll();
        foreach ($products as $p) {
            if ($p['id'] === $id) {
                return ['success' => false, 'errors' => ['id' => 'Product ID/Slug already exists. Please choose a unique identifier.']];
            }
        }

        $newProduct = [
            'id' => $id,
            'name' => htmlspecialchars(trim($data['name'])),
            'category' => htmlspecialchars(trim($data['category'])),
            'category_slug' => Blog::slugify($data['category']),
            'image' => htmlspecialchars(trim($data['image'] ?? '/assets/images/01_plain_handloom.png')),
            'description' => htmlspecialchars(trim($data['description'] ?? '')),
            'price' => $price,
            'offer_price' => $offerPrice,
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $products[] = $newProduct;
        file_put_contents(self::$storagePath, json_encode($products, JSON_PRETTY_PRINT));

        return ['success' => true, 'product' => $newProduct];
    }

    public static function update(string $id, array $data): array
    {
        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $price = isset($data['price']) && $data['price'] !== '' ? (float)$data['price'] : null;
        $offerPrice = isset($data['offer_price']) && $data['offer_price'] !== '' ? (float)$data['offer_price'] : null;

        if (Database::isConnected()) {
            $sql = "UPDATE products SET name = :name, category = :category, category_slug = :category_slug, image = :image, description = :description, price = :price, offer_price = :offer_price, status = :status, updated_at = NOW() WHERE id = :id";
            Database::execute($sql, [
                'id' => $id,
                'name' => htmlspecialchars(trim($data['name'])),
                'category' => htmlspecialchars(trim($data['category'])),
                'category_slug' => Blog::slugify($data['category']),
                'image' => htmlspecialchars(trim($data['image'] ?? '/assets/images/01_plain_handloom.png')),
                'description' => htmlspecialchars(trim($data['description'] ?? '')),
                'price' => $price,
                'offer_price' => $offerPrice,
                'status' => $data['status'] ?? 'active'
            ]);
            return ['success' => true];
        }

        $products = self::getAll();
        $found = false;
        $updatedProduct = null;

        foreach ($products as &$product) {
            if ($product['id'] === $id) {
                $product['name'] = htmlspecialchars(trim($data['name']));
                $product['category'] = htmlspecialchars(trim($data['category']));
                $product['category_slug'] = Blog::slugify($data['category']);
                if (!empty($data['image'])) {
                    $product['image'] = htmlspecialchars(trim($data['image']));
                }
                $product['description'] = htmlspecialchars(trim($data['description'] ?? ''));
                $product['price'] = $price;
                $product['offer_price'] = $offerPrice;
                $product['status'] = $data['status'] ?? 'active';
                $product['updated_at'] = date('Y-m-d H:i:s');

                $updatedProduct = $product;
                $found = true;
                break;
            }
        }

        if (!$found) {
            return ['success' => false, 'errors' => ['general' => 'Product not found.']];
        }

        file_put_contents(self::$storagePath, json_encode($products, JSON_PRETTY_PRINT));
        return ['success' => true, 'product' => $updatedProduct];
    }

    public static function delete(string $id): bool
    {
        if (Database::isConnected()) {
            return Database::execute("DELETE FROM products WHERE id = :id", ['id' => $id]);
        }

        $products = self::getAll();
        $filtered = array_values(array_filter($products, fn($p) => $p['id'] !== $id));
        file_put_contents(self::$storagePath, json_encode($filtered, JSON_PRETTY_PRINT));
        return true;
    }

    private static function validate(array $data): array
    {
        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = 'Product Name is required.';
        }
        if (empty($data['category'])) {
            $errors['category'] = 'Product Category is required.';
        }
        return $errors;
    }

    public static function getCategories(): array
    {
        return [
            ['slug' => 'all', 'name' => 'All'],
            ['slug' => 'plain-handloom', 'name' => 'Plain & Handloom'],
            ['slug' => 'tufted', 'name' => 'Tufted'],
            ['slug' => 'creel-rod', 'name' => 'Creel & Rod'],
            ['slug' => 'rope-braided', 'name' => 'Rope & Braided'],
            ['slug' => 'backed-mats', 'name' => 'Backed Mats'],
            ['slug' => 'printed-logo', 'name' => 'Printed & Logo'],
            ['slug' => 'carpet-rolls', 'name' => 'Carpet & Rolls']
        ];
    }

    public static function getStandardSizes(): array
    {
        return [
            '16" x 24"',
            '18" x 30"',
            '21" x 36"',
            '24" x 36"'
        ];
    }

    private static function getDefaultProducts(): array
    {
        return [
            [
                'id' => 'plain-handloom',
                'name' => 'Plain and Handloom',
                'category' => 'Plain & Handloom',
                'category_slug' => 'plain-handloom',
                'image' => '/assets/images/01_plain_handloom.png',
                'description' => 'Classic hand-woven natural coir mat made from high quality 100% natural coconut fibers.',
                'price' => 250.00,
                'offer_price' => 199.00,
                'status' => 'active'
            ],
            [
                'id' => 'tufted',
                'name' => 'Tufted',
                'category' => 'Tufted',
                'category_slug' => 'tufted',
                'image' => '/assets/images/02_tufted_pattern.png',
                'description' => 'Durable cut-pile tufted coir bristles designed for heavy scraping and soil absorption.',
                'price' => 350.00,
                'offer_price' => 299.00,
                'status' => 'active'
            ],
            [
                'id' => 'creel-rod',
                'name' => 'Creel and Rod',
                'category' => 'Creel & Rod',
                'category_slug' => 'creel-rod',
                'image' => '/assets/images/03_creel_and_rod.png',
                'description' => 'Heavy-duty steel or wood rod reinforced coir matting built for extreme foot traffic.',
                'price' => 450.00,
                'offer_price' => 399.00,
                'status' => 'active'
            ],
            [
                'id' => 'rope-braided',
                'name' => 'Rope and Braided',
                'category' => 'Rope & Braided',
                'category_slug' => 'rope-braided',
                'image' => '/assets/images/04_rope_braided_round.png',
                'description' => 'Intricately hand-braided natural coir rope mats featuring rich artisanal patterns.',
                'price' => 300.00,
                'offer_price' => null,
                'status' => 'active'
            ],
            [
                'id' => 'pvc-backed',
                'name' => 'PVC - Backed',
                'category' => 'Backed Mats',
                'category_slug' => 'backed-mats',
                'image' => '/assets/images/05_pvc_backed.png',
                'description' => 'Non-slip PVC vinyl backing bonded with dense coir fibers for zero displacement.',
                'price' => 280.00,
                'offer_price' => 220.00,
                'status' => 'active'
            ],
            [
                'id' => 'rubber-backed',
                'name' => 'Rubber-Backed',
                'category' => 'Backed Mats',
                'category_slug' => 'backed-mats',
                'image' => '/assets/images/06_rubber_backed_stack.png',
                'description' => 'Heavy molded rubber frame and base for outdoors weather resistance.',
                'price' => 320.00,
                'offer_price' => 260.00,
                'status' => 'active'
            ],
            [
                'id' => 'latex-backed',
                'name' => 'Latex-Backed',
                'category' => 'Backed Mats',
                'category_slug' => 'backed-mats',
                'image' => '/assets/images/07_latex_backed.png',
                'description' => 'Eco-friendly natural latex spray backing for flexible anti-skid protection.',
                'price' => 290.00,
                'offer_price' => null,
                'status' => 'active'
            ],
            [
                'id' => 'printed-logo',
                'name' => 'Printed and Logo',
                'category' => 'Printed & Logo',
                'category_slug' => 'printed-logo',
                'image' => '/assets/images/08_printed_logo_border.png',
                'description' => 'Custom stencilled and screen-printed mats featuring welcome motifs or corporate logos.',
                'price' => 400.00,
                'offer_price' => 349.00,
                'status' => 'active'
            ],
            [
                'id' => 'bleached-coloured',
                'name' => 'Bleached or Coloured',
                'category' => 'Printed & Logo',
                'category_slug' => 'printed-logo',
                'image' => '/assets/images/09_bleached_coloured_stack.png',
                'description' => 'Sun-bleached blonde coir yarn or AZO-free dyed rich color coir mats.',
                'price' => 270.00,
                'offer_price' => 219.00,
                'status' => 'active'
            ],
            [
                'id' => 'coir-carpet',
                'name' => 'Coir Carpet',
                'category' => 'Carpet & Rolls',
                'category_slug' => 'carpet-rolls',
                'image' => '/assets/images/10_coir_carpet_roll.png',
                'description' => 'High-end coir runner mats and floor carpets for hallways and eco-interiors.',
                'price' => 850.00,
                'offer_price' => 699.00,
                'status' => 'active'
            ],
            [
                'id' => 'entryways-rope-knot',
                'name' => 'Entryways Coir Rope Knot Doormat',
                'category' => 'Rope & Braided',
                'category_slug' => 'rope-braided',
                'image' => '/assets/images/11_entryway_coir_rope.png',
                'description' => 'Thick braided coir rope woven into timeless knot designs for luxury entrances.',
                'price' => 500.00,
                'offer_price' => 420.00,
                'status' => 'active'
            ],
            [
                'id' => 'coir-mats-rolls',
                'name' => 'Coir Mat Rolls',
                'category' => 'Carpet & Rolls',
                'category_slug' => 'carpet-rolls',
                'image' => '/assets/images/12_coir_mat_rolls.png',
                'description' => 'Master roll stock coir matting available for bulk commercial custom cutting.',
                'price' => 1200.00,
                'offer_price' => null,
                'status' => 'active'
            ],
            [
                'id' => 'colours-and-designs',
                'name' => 'Colours and Designs',
                'category' => 'Printed & Logo',
                'category_slug' => 'printed-logo',
                'image' => '/assets/images/13_colours_and_designs.png',
                'description' => 'Vibrant geometric, striped, and multi-color coir fiber combinations.',
                'price' => 380.00,
                'offer_price' => 319.00,
                'status' => 'active'
            ]
        ];
    }
}
