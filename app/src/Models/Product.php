<?php

namespace App\Models;

use App\Core\Database;

class Product
{
    private static string $storagePath = BASE_PATH . '/storage/products.json';

    public static function ensureSchema(): void
    {
        static $schemaChecked = false;
        if ($schemaChecked || !Database::isConnected()) {
            return;
        }
        $schemaChecked = true;
        try {
            Database::execute("ALTER TABLE products ADD COLUMN IF NOT EXISTS price_usd NUMERIC(10,2) DEFAULT NULL");
            Database::execute("ALTER TABLE products ADD COLUMN IF NOT EXISTS offer_price_usd NUMERIC(10,2) DEFAULT NULL");
            Database::execute("ALTER TABLE products ADD COLUMN IF NOT EXISTS price_gbp NUMERIC(10,2) DEFAULT NULL");
            Database::execute("ALTER TABLE products ADD COLUMN IF NOT EXISTS offer_price_gbp NUMERIC(10,2) DEFAULT NULL");
        } catch (\Throwable $e) {
            // Table already migrated or permissions managed
        }
    }

    public static function getAll(): array
    {
        self::ensureSchema();

        if (Database::isConnected()) {
            $products = Database::fetchAll("SELECT * FROM products ORDER BY name ASC") ?? [];
            if (!empty($products)) {
                return $products;
            }
            // Auto-populate default catalog if database table is empty
            Category::getAll(); // ensure categories exist first
            $defaults = self::getDefaultProducts();
            foreach ($defaults as $p) {
                self::save($p);
            }
            return Database::fetchAll("SELECT * FROM products ORDER BY name ASC") ?? [];
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!file_exists(self::$storagePath)) {
            $defaults = self::getDefaultProducts();
            file_put_contents(self::$storagePath, json_encode($defaults, JSON_PRETTY_PRINT));
            return $defaults;
        }

        $products = json_decode(file_get_contents(self::$storagePath), true);
        return is_array($products) ? $products : [];
    }

    public static function getById(string $id): ?array
    {
        self::ensureSchema();

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
        self::ensureSchema();

        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $id = !empty($data['id']) ? Blog::slugify($data['id']) : Blog::slugify($data['name']);
        
        // Multi-currency pricing
        // 1. Rupees (INR)
        $price = isset($data['price']) && $data['price'] !== '' ? (float)$data['price'] : (isset($data['price_inr']) && $data['price_inr'] !== '' ? (float)$data['price_inr'] : null);
        $offerPrice = isset($data['offer_price']) && $data['offer_price'] !== '' ? (float)$data['offer_price'] : (isset($data['offer_price_inr']) && $data['offer_price_inr'] !== '' ? (float)$data['offer_price_inr'] : null);

        // 2. US Dollars (USD)
        $priceUsd = isset($data['price_usd']) && $data['price_usd'] !== '' ? (float)$data['price_usd'] : null;
        $offerPriceUsd = isset($data['offer_price_usd']) && $data['offer_price_usd'] !== '' ? (float)$data['offer_price_usd'] : null;

        // 3. British Pounds (GBP)
        $priceGbp = isset($data['price_gbp']) && $data['price_gbp'] !== '' ? (float)$data['price_gbp'] : null;
        $offerPriceGbp = isset($data['offer_price_gbp']) && $data['offer_price_gbp'] !== '' ? (float)$data['offer_price_gbp'] : null;

        // Resolve mapped Category
        [$categoryName, $categorySlug, $categoryId] = self::resolveCategory($data);

        if (Database::isConnected()) {
            $existing = Database::fetch("SELECT id FROM products WHERE id = :id", ['id' => $id]);
            if ($existing) {
                return ['success' => false, 'errors' => ['id' => 'Product ID/Slug already exists. Please choose a unique identifier.']];
            }

            $sql = "INSERT INTO products (
                id, name, category, category_slug, category_id, image, description,
                price, offer_price, price_usd, offer_price_usd, price_gbp, offer_price_gbp, status
            ) VALUES (
                :id, :name, :category, :category_slug, :category_id, :image, :description,
                :price, :offer_price, :price_usd, :offer_price_usd, :price_gbp, :offer_price_gbp, :status
            )";
            Database::execute($sql, [
                'id' => $id,
                'name' => htmlspecialchars(trim($data['name'])),
                'category' => $categoryName,
                'category_slug' => $categorySlug,
                'category_id' => $categoryId,
                'image' => htmlspecialchars(trim($data['image'] ?? '/assets/images/01_plain_handloom.png')),
                'description' => htmlspecialchars(trim($data['description'] ?? '')),
                'price' => $price,
                'offer_price' => $offerPrice,
                'price_usd' => $priceUsd,
                'offer_price_usd' => $offerPriceUsd,
                'price_gbp' => $priceGbp,
                'offer_price_gbp' => $offerPriceGbp,
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
            'category' => $categoryName,
            'category_slug' => $categorySlug,
            'category_id' => $categoryId,
            'image' => htmlspecialchars(trim($data['image'] ?? '/assets/images/01_plain_handloom.png')),
            'description' => htmlspecialchars(trim($data['description'] ?? '')),
            'price' => $price,
            'offer_price' => $offerPrice,
            'price_usd' => $priceUsd,
            'offer_price_usd' => $offerPriceUsd,
            'price_gbp' => $priceGbp,
            'offer_price_gbp' => $offerPriceGbp,
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $products[] = $newProduct;
        file_put_contents(self::$storagePath, json_encode($products, JSON_PRETTY_PRINT));

        return ['success' => true, 'product' => $newProduct];
    }

    public static function update(string $id, array $data): array
    {
        self::ensureSchema();

        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Multi-currency pricing
        // 1. Rupees (INR)
        $price = isset($data['price']) && $data['price'] !== '' ? (float)$data['price'] : (isset($data['price_inr']) && $data['price_inr'] !== '' ? (float)$data['price_inr'] : null);
        $offerPrice = isset($data['offer_price']) && $data['offer_price'] !== '' ? (float)$data['offer_price'] : (isset($data['offer_price_inr']) && $data['offer_price_inr'] !== '' ? (float)$data['offer_price_inr'] : null);

        // 2. US Dollars (USD)
        $priceUsd = isset($data['price_usd']) && $data['price_usd'] !== '' ? (float)$data['price_usd'] : null;
        $offerPriceUsd = isset($data['offer_price_usd']) && $data['offer_price_usd'] !== '' ? (float)$data['offer_price_usd'] : null;

        // 3. British Pounds (GBP)
        $priceGbp = isset($data['price_gbp']) && $data['price_gbp'] !== '' ? (float)$data['price_gbp'] : null;
        $offerPriceGbp = isset($data['offer_price_gbp']) && $data['offer_price_gbp'] !== '' ? (float)$data['offer_price_gbp'] : null;

        // Resolve mapped Category
        [$categoryName, $categorySlug, $categoryId] = self::resolveCategory($data);

        if (Database::isConnected()) {
            $sql = "UPDATE products SET 
                name = :name, 
                category = :category, 
                category_slug = :category_slug, 
                category_id = :category_id, 
                image = :image, 
                description = :description, 
                price = :price, 
                offer_price = :offer_price, 
                price_usd = :price_usd, 
                offer_price_usd = :offer_price_usd, 
                price_gbp = :price_gbp, 
                offer_price_gbp = :offer_price_gbp, 
                status = :status, 
                updated_at = NOW() 
            WHERE id = :id";

            Database::execute($sql, [
                'id' => $id,
                'name' => htmlspecialchars(trim($data['name'])),
                'category' => $categoryName,
                'category_slug' => $categorySlug,
                'category_id' => $categoryId,
                'image' => htmlspecialchars(trim($data['image'] ?? '/assets/images/01_plain_handloom.png')),
                'description' => htmlspecialchars(trim($data['description'] ?? '')),
                'price' => $price,
                'offer_price' => $offerPrice,
                'price_usd' => $priceUsd,
                'offer_price_usd' => $offerPriceUsd,
                'price_gbp' => $priceGbp,
                'offer_price_gbp' => $offerPriceGbp,
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
                $product['category'] = $categoryName;
                $product['category_slug'] = $categorySlug;
                $product['category_id'] = $categoryId;
                if (!empty($data['image'])) {
                    $product['image'] = htmlspecialchars(trim($data['image']));
                }
                $product['description'] = htmlspecialchars(trim($data['description'] ?? ''));
                $product['price'] = $price;
                $product['offer_price'] = $offerPrice;
                $product['price_usd'] = $priceUsd;
                $product['offer_price_usd'] = $offerPriceUsd;
                $product['price_gbp'] = $priceGbp;
                $product['offer_price_gbp'] = $offerPriceGbp;
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

    private static function resolveCategory(array $data): array
    {
        $categorySlug = !empty($data['category_slug']) ? trim($data['category_slug']) : '';
        $categoryName = !empty($data['category']) ? trim($data['category']) : '';
        $categoryId = !empty($data['category_id']) ? trim($data['category_id']) : '';

        if (!empty($categorySlug)) {
            $cat = Category::getBySlug($categorySlug) ?? Category::getById($categorySlug);
            if ($cat) {
                return [$cat['name'], $cat['slug'], $cat['id']];
            }
        }

        if (!empty($categoryId)) {
            $cat = Category::getById($categoryId);
            if ($cat) {
                return [$cat['name'], $cat['slug'], $cat['id']];
            }
        }

        if (!empty($categoryName)) {
            foreach (Category::getAll() as $c) {
                if (strcasecmp($c['name'], $categoryName) === 0 || strcasecmp($c['slug'], Blog::slugify($categoryName)) === 0) {
                    return [$c['name'], $c['slug'], $c['id']];
                }
            }
            return [$categoryName, Blog::slugify($categoryName), Blog::slugify($categoryName)];
        }

        return ['General', 'general', 'general'];
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

    public static function deleteAll(): bool
    {
        if (Database::isConnected()) {
            return Database::execute("DELETE FROM products");
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(self::$storagePath, json_encode([], JSON_PRETTY_PRINT));
        return true;
    }

    public static function deleteMultiple(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        if (Database::isConnected()) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            Database::execute("DELETE FROM products WHERE id IN ($placeholders)", $ids);
            return count($ids);
        }

        $products = self::getAll();
        $before = count($products);
        $filtered = array_values(array_filter($products, fn($p) => !in_array($p['id'], $ids, true)));
        file_put_contents(self::$storagePath, json_encode($filtered, JSON_PRETTY_PRINT));
        return $before - count($filtered);
    }

    public static function resetDefaults(): array
    {
        $defaults = self::getDefaultProducts();
        if (Database::isConnected()) {
            Database::execute("DELETE FROM products");
            foreach ($defaults as $p) {
                self::save($p);
            }
            return $defaults;
        }

        file_put_contents(self::$storagePath, json_encode($defaults, JSON_PRETTY_PRINT));
        return $defaults;
    }

    private static function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Product Name is required.';
        }
        if (empty(trim($data['category'] ?? '')) && empty(trim($data['category_slug'] ?? '')) && empty(trim($data['category_id'] ?? ''))) {
            $errors['category'] = 'Product Category is required.';
        }
        return $errors;
    }

    public static function getCategories(): array
    {
        $cats = Category::getAll();
        $result = [['slug' => 'all', 'name' => 'All']];
        foreach ($cats as $c) {
            $result[] = [
                'id' => $c['id'] ?? $c['slug'],
                'slug' => $c['slug'],
                'name' => $c['name']
            ];
        }
        return $result;
    }

    public static function getStandardSizes(): array
    {
        return [
            [
                'cm' => '40 × 60 cm',
                'inches' => '16″ × 24″',
                'label' => '40 × 60 cm (16″ × 24″)'
            ],
            [
                'cm' => '45 × 75 cm',
                'inches' => '18″ × 30″',
                'label' => '45 × 75 cm (18″ × 30″)'
            ],
            [
                'cm' => '53 × 90 cm',
                'inches' => '21″ × 36″',
                'label' => '53 × 90 cm (21″ × 36″)'
            ],
            [
                'cm' => '60 × 90 cm',
                'inches' => '24″ × 36″',
                'label' => '60 × 90 cm (24″ × 36″)'
            ]
        ];
    }

    public static function formatPrice(?float $amount, string $currency = 'INR'): string
    {
        if ($amount === null || $amount <= 0) {
            return '';
        }
        $currency = strtoupper($currency);
        switch ($currency) {
            case 'USD':
                return '$' . number_format($amount, 2);
            case 'GBP':
                return '£' . number_format($amount, 2);
            case 'INR':
            default:
                return '₹' . (floor($amount) == $amount ? number_format($amount, 0) : number_format($amount, 2));
        }
    }

    public static function getCurrencySymbol(string $currency = 'INR'): string
    {
        return match (strtoupper($currency)) {
            'USD' => '$',
            'GBP' => '£',
            default => '₹'
        };
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
                'price_usd' => 4.50,
                'offer_price_usd' => 3.50,
                'price_gbp' => 3.50,
                'offer_price_gbp' => 2.75,
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
                'price_usd' => 6.00,
                'offer_price_usd' => 5.00,
                'price_gbp' => 4.75,
                'offer_price_gbp' => 3.95,
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
                'price_usd' => 7.50,
                'offer_price_usd' => 6.50,
                'price_gbp' => 6.00,
                'offer_price_gbp' => 5.20,
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
                'price_usd' => 5.00,
                'offer_price_usd' => null,
                'price_gbp' => 4.00,
                'offer_price_gbp' => null,
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
                'price_usd' => 4.80,
                'offer_price_usd' => 3.80,
                'price_gbp' => 3.80,
                'offer_price_gbp' => 3.00,
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
                'price_usd' => 5.50,
                'offer_price_usd' => 4.50,
                'price_gbp' => 4.40,
                'offer_price_gbp' => 3.60,
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
                'price_usd' => 5.00,
                'offer_price_usd' => null,
                'price_gbp' => 4.00,
                'offer_price_gbp' => null,
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
                'price_usd' => 6.80,
                'offer_price_usd' => 5.90,
                'price_gbp' => 5.50,
                'offer_price_gbp' => 4.75,
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
                'price_usd' => 4.60,
                'offer_price_usd' => 3.75,
                'price_gbp' => 3.70,
                'offer_price_gbp' => 3.00,
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
                'price_usd' => 14.50,
                'offer_price_usd' => 11.90,
                'price_gbp' => 11.50,
                'offer_price_gbp' => 9.50,
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
                'price_usd' => 8.50,
                'offer_price_usd' => 7.20,
                'price_gbp' => 6.80,
                'offer_price_gbp' => 5.75,
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
                'price_usd' => 20.00,
                'offer_price_usd' => null,
                'price_gbp' => 16.00,
                'offer_price_gbp' => null,
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
                'price_usd' => 6.50,
                'offer_price_usd' => 5.50,
                'price_gbp' => 5.20,
                'offer_price_gbp' => 4.40,
                'status' => 'active'
            ]
        ];
    }
}
