<?php

namespace App\Models;

use App\Core\Database;

class Blog
{
    private static string $storagePath = BASE_PATH . '/storage/blogs.json';

    public static function getAll(): array
    {
        if (Database::isConnected()) {
            $dbBlogs = Database::fetchAll("SELECT * FROM blogs ORDER BY created_at DESC");
            if (!empty($dbBlogs)) {
                return $dbBlogs;
            }
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!file_exists(self::$storagePath)) {
            file_put_contents(self::$storagePath, json_encode([], JSON_PRETTY_PRINT));
            return [];
        }

        $blogs = json_decode(file_get_contents(self::$storagePath), true);
        return is_array($blogs) ? $blogs : [];
    }

    public static function getPublished(): array
    {
        if (Database::isConnected()) {
            return Database::fetchAll("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC");
        }
        return array_values(array_filter(self::getAll(), fn($b) => ($b['status'] ?? 'published') === 'published'));
    }

    public static function getById(string $id): ?array
    {
        if (Database::isConnected()) {
            $blog = Database::fetch("SELECT * FROM blogs WHERE id = :id", ['id' => $id]);
            if ($blog) return $blog;
        }

        foreach (self::getAll() as $blog) {
            if ($blog['id'] === $id) {
                return $blog;
            }
        }
        return null;
    }

    public static function getBySlug(string $slug): ?array
    {
        if (Database::isConnected()) {
            $blog = Database::fetch("SELECT * FROM blogs WHERE slug = :slug", ['slug' => $slug]);
            if ($blog) return $blog;
        }

        foreach (self::getAll() as $blog) {
            if (($blog['slug'] ?? '') === $slug) {
                return $blog;
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

        $id = 'BLOG-' . uniqid();
        $slug = !empty($data['slug']) ? self::slugify($data['slug']) : self::slugify($data['title']);
        $title = htmlspecialchars(trim($data['title']));
        $excerpt = htmlspecialchars(trim($data['excerpt'] ?? ''));
        $content = $data['content'] ?? '';
        $category = htmlspecialchars(trim($data['category'] ?? 'Coir Industry'));
        $author = htmlspecialchars(trim($data['author'] ?? 'Kavimayil Venkatachalam'));
        $image = htmlspecialchars(trim($data['image'] ?? '/assets/images/01_plain_handloom.png'));
        $status = $data['status'] ?? 'published';
        $metaTitle = htmlspecialchars(trim($data['meta_title'] ?? $title));
        $metaDesc = htmlspecialchars(trim($data['meta_description'] ?? $excerpt));

        if (Database::isConnected()) {
            $sql = "INSERT INTO blogs (id, title, slug, excerpt, content, category, author, image, status, meta_title, meta_description) 
                    VALUES (:id, :title, :slug, :excerpt, :content, :category, :author, :image, :status, :meta_title, :meta_description)";
            Database::execute($sql, [
                'id' => $id,
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content' => $content,
                'category' => $category,
                'author' => $author,
                'image' => $image,
                'status' => $status,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDesc
            ]);
            return ['success' => true, 'blog' => ['id' => $id]];
        }

        $blogs = self::getAll();
        $newBlog = [
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'category' => $category,
            'author' => $author,
            'image' => $image,
            'status' => $status,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $blogs[] = $newBlog;
        file_put_contents(self::$storagePath, json_encode($blogs, JSON_PRETTY_PRINT));

        return ['success' => true, 'blog' => $newBlog];
    }

    public static function update(string $id, array $data): array
    {
        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $slug = !empty($data['slug']) ? self::slugify($data['slug']) : self::slugify($data['title']);
        $title = htmlspecialchars(trim($data['title']));
        $excerpt = htmlspecialchars(trim($data['excerpt'] ?? ''));
        $content = $data['content'] ?? '';
        $category = htmlspecialchars(trim($data['category'] ?? 'Coir Industry'));
        $author = htmlspecialchars(trim($data['author'] ?? 'Kavimayil Venkatachalam'));
        $image = htmlspecialchars(trim($data['image'] ?? '/assets/images/01_plain_handloom.png'));
        $status = $data['status'] ?? 'published';
        $metaTitle = htmlspecialchars(trim($data['meta_title'] ?? $title));
        $metaDesc = htmlspecialchars(trim($data['meta_description'] ?? $excerpt));

        if (Database::isConnected()) {
            $sql = "UPDATE blogs SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, category = :category, author = :author, image = :image, status = :status, meta_title = :meta_title, meta_description = :meta_description, updated_at = NOW() WHERE id = :id";
            Database::execute($sql, [
                'id' => $id,
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content' => $content,
                'category' => $category,
                'author' => $author,
                'image' => $image,
                'status' => $status,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDesc
            ]);
            return ['success' => true];
        }

        $blogs = self::getAll();
        $found = false;
        $updatedBlog = null;

        foreach ($blogs as &$blog) {
            if ($blog['id'] === $id) {
                $blog['title'] = $title;
                $blog['slug'] = $slug;
                $blog['excerpt'] = $excerpt;
                $blog['content'] = $content;
                $blog['category'] = $category;
                $blog['author'] = $author;
                $blog['image'] = $image;
                $blog['status'] = $status;
                $blog['meta_title'] = $metaTitle;
                $blog['meta_description'] = $metaDesc;
                $blog['updated_at'] = date('Y-m-d H:i:s');

                $updatedBlog = $blog;
                $found = true;
                break;
            }
        }

        if (!$found) {
            return ['success' => false, 'errors' => ['general' => 'Blog post not found.']];
        }

        file_put_contents(self::$storagePath, json_encode($blogs, JSON_PRETTY_PRINT));
        return ['success' => true, 'blog' => $updatedBlog];
    }

    public static function delete(string $id): bool
    {
        if (Database::isConnected()) {
            return Database::execute("DELETE FROM blogs WHERE id = :id", ['id' => $id]);
        }

        $blogs = self::getAll();
        $filtered = array_values(array_filter($blogs, fn($b) => $b['id'] !== $id));
        file_put_contents(self::$storagePath, json_encode($filtered, JSON_PRETTY_PRINT));
        return true;
    }

    private static function validate(array $data): array
    {
        $errors = [];
        if (empty($data['title'])) {
            $errors['title'] = 'Blog Title is required.';
        }
        if (empty($data['content'])) {
            $errors['content'] = 'Blog Content cannot be empty.';
        }
        return $errors;
    }

    public static function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }

    private static function getInitialSeeds(): array
    {
        return [
            [
                'id' => 'BLOG-1',
                'title' => 'Global Eco-Friendly Matting Trends 2026',
                'slug' => 'global-eco-friendly-matting-trends-2026',
                'excerpt' => 'How natural coir fibre products are replacing synthetic rubber door mats in European and North American markets.',
                'content' => 'Natural coir fibre products harvested from coconut husks in Tamil Nadu are witnessing unprecedented global demand. Importers in North America and Europe are prioritizing biodegradable, plastic-free entrance matting solutions to fulfill strict environmental compliance standards.',
                'category' => 'Global Trade',
                'author' => 'Kavimayil Venkatachalam',
                'image' => '/assets/images/01_plain_handloom.png',
                'status' => 'published',
                'meta_title' => 'Global Eco-Friendly Matting Trends 2026 | FEEBLE EXPORTS',
                'meta_description' => 'Explore global market demand for eco-friendly coir products exported from Tamil Nadu, India.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ],
            [
                'id' => 'BLOG-2',
                'title' => 'The Craft of Handloom Coir Weaving in Namakkal',
                'slug' => 'craft-of-handloom-coir-weaving-namakkal',
                'excerpt' => 'Inside FEEBLE EXPORTS artisanal weaving process producing high durability coir products for international buyers.',
                'content' => 'Coir mat making in Namakkal blends generational artisanal skill with modern quality benchmarks. From raw golden fiber extraction to hand-cut finish, every mat offers supreme scraping power and longevity.',
                'category' => 'Craft & Manufacturing',
                'author' => 'Kavimayil Venkatachalam',
                'image' => '/assets/images/04_rope_braided_round.png',
                'status' => 'published',
                'meta_title' => 'Handloom Coir Weaving in Namakkal | FEEBLE EXPORTS',
                'meta_description' => 'Discover the craftsmanship and export quality standards of FEEBLE EXPORTS coir products.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ]
        ];
    }
}
