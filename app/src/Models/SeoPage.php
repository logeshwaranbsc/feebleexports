<?php

namespace App\Models;

use App\Core\Database;

class SeoPage
{
    private static string $storagePath = BASE_PATH . '/storage/seo_pages.json';

    public static function getAll(array $filters = []): array
    {
        $status = $filters['status'] ?? 'all';
        $search = trim($filters['search'] ?? '');

        if (Database::isConnected()) {
            $sql = "SELECT * FROM seo_pages WHERE 1=1";
            $params = [];

            if ($status !== 'all' && !empty($status)) {
                $sql .= " AND status = :status";
                $params['status'] = $status;
            }

            if (!empty($search)) {
                $sql .= " AND (LOWER(title) LIKE :search OR LOWER(slug) LIKE :search)";
                $params['search'] = '%' . strtolower($search) . '%';
            }

            $sql .= " ORDER BY updated_at DESC, created_at DESC";
            $rows = Database::fetchAll($sql, $params);

            if (!empty($rows)) {
                return array_map([self::class, 'hydrate'], $rows);
            }
        }

        // File-based fallback
        $pages = self::getFromFile();
        if ($status !== 'all' && !empty($status)) {
            $pages = array_filter($pages, fn($p) => ($p['status'] ?? '') === $status);
        }
        if (!empty($search)) {
            $s = strtolower($search);
            $pages = array_filter($pages, fn($p) => 
                str_contains(strtolower($p['title'] ?? ''), $s) || 
                str_contains(strtolower($p['slug'] ?? ''), $s)
            );
        }

        usort($pages, function ($a, $b) {
            return strcmp($b['updated_at'] ?? '', $a['updated_at'] ?? '');
        });

        return array_values(array_map([self::class, 'hydrate'], $pages));
    }

    public static function getById(string $id): ?array
    {
        if (Database::isConnected()) {
            $row = Database::fetch("SELECT * FROM seo_pages WHERE id = :id", ['id' => $id]);
            if ($row) {
                return self::hydrate($row);
            }
        }

        foreach (self::getFromFile() as $p) {
            if ($p['id'] === $id) {
                return self::hydrate($p);
            }
        }

        return null;
    }

    public static function getBySlug(string $slug): ?array
    {
        $slug = trim($slug, '/');

        if (Database::isConnected()) {
            $row = Database::fetch("SELECT * FROM seo_pages WHERE slug = :slug", ['slug' => $slug]);
            if ($row) {
                return self::hydrate($row);
            }
        }

        foreach (self::getFromFile() as $p) {
            if (($p['slug'] ?? '') === $slug) {
                return self::hydrate($p);
            }
        }

        return null;
    }

    public static function create(array $data): array
    {
        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $id = 'seo-' . uniqid();
        $title = trim($data['title']);
        $slug = self::slugify(!empty($data['slug']) ? $data['slug'] : $title);

        if (!self::isSlugAvailable($slug)) {
            return ['success' => false, 'errors' => ['slug' => 'The URL slug is already taken. Please choose another.']];
        }

        $status = in_array($data['status'] ?? '', ['published', 'draft']) ? $data['status'] : 'draft';
        $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

        $page = self::prepareFields($id, $data, $slug, $status, $publishedAt);

        if (Database::isConnected()) {
            $sql = "INSERT INTO seo_pages (
                id, title, slug, status, canonical_url, meta_title, meta_description, meta_keywords,
                custom_head_html, hero_image, hero_heading, hero_form_title, hero_subheading,
                why_choose_image, why_choose_heading, why_choose_content,
                about_image, about_heading, about_content,
                content_block_1_heading, content_block_1_content, selected_projects,
                content_block_2_content, show_testimonials, testimonials,
                show_faq, faqs, created_at, updated_at, published_at
            ) VALUES (
                :id, :title, :slug, :status, :canonical_url, :meta_title, :meta_description, :meta_keywords,
                :custom_head_html, :hero_image, :hero_heading, :hero_form_title, :hero_subheading,
                :why_choose_image, :why_choose_heading, :why_choose_content,
                :about_image, :about_heading, :about_content,
                :content_block_1_heading, :content_block_1_content, :selected_projects,
                :content_block_2_content, :show_testimonials, :testimonials,
                :show_faq, :faqs, NOW(), NOW(), :published_at
            )";

            Database::execute($sql, [
                'id' => $page['id'],
                'title' => $page['title'],
                'slug' => $page['slug'],
                'status' => $page['status'],
                'canonical_url' => $page['canonical_url'],
                'meta_title' => $page['meta_title'],
                'meta_description' => $page['meta_description'],
                'meta_keywords' => $page['meta_keywords'],
                'custom_head_html' => $page['custom_head_html'],
                'hero_image' => $page['hero_image'],
                'hero_heading' => $page['hero_heading'],
                'hero_form_title' => $page['hero_form_title'],
                'hero_subheading' => $page['hero_subheading'],
                'why_choose_image' => $page['why_choose_image'],
                'why_choose_heading' => $page['why_choose_heading'],
                'why_choose_content' => $page['why_choose_content'],
                'about_image' => $page['about_image'],
                'about_heading' => $page['about_heading'],
                'about_content' => $page['about_content'],
                'content_block_1_heading' => $page['content_block_1_heading'],
                'content_block_1_content' => $page['content_block_1_content'],
                'selected_projects' => json_encode($page['selected_projects']),
                'content_block_2_content' => $page['content_block_2_content'],
                'show_testimonials' => $page['show_testimonials'] ? 'true' : 'false',
                'testimonials' => json_encode($page['testimonials']),
                'show_faq' => $page['show_faq'] ? 'true' : 'false',
                'faqs' => json_encode($page['faqs']),
                'published_at' => $publishedAt
            ]);

            return ['success' => true, 'page' => $page];
        }

        // File-based save
        $all = self::getFromFile();
        $page['created_at'] = date('Y-m-d H:i:s');
        $page['updated_at'] = date('Y-m-d H:i:s');
        $all[] = $page;
        self::saveToFile($all);

        return ['success' => true, 'page' => $page];
    }

    public static function update(string $id, array $data): array
    {
        $existing = self::getById($id);
        if (!$existing) {
            return ['success' => false, 'errors' => ['general' => 'SEO page not found.']];
        }

        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $title = trim($data['title']);
        $slug = self::slugify(!empty($data['slug']) ? $data['slug'] : $title);

        if (!self::isSlugAvailable($slug, $id)) {
            return ['success' => false, 'errors' => ['slug' => 'The URL slug is already taken. Please choose another.']];
        }

        $status = in_array($data['status'] ?? '', ['published', 'draft']) ? $data['status'] : $existing['status'];
        $publishedAt = $existing['published_at'];
        if ($status === 'published' && empty($publishedAt)) {
            $publishedAt = date('Y-m-d H:i:s');
        } elseif ($status === 'draft') {
            $publishedAt = null;
        }

        $page = self::prepareFields($id, $data, $slug, $status, $publishedAt);

        if (Database::isConnected()) {
            $sql = "UPDATE seo_pages SET
                title = :title,
                slug = :slug,
                status = :status,
                canonical_url = :canonical_url,
                meta_title = :meta_title,
                meta_description = :meta_description,
                meta_keywords = :meta_keywords,
                custom_head_html = :custom_head_html,
                hero_image = :hero_image,
                hero_heading = :hero_heading,
                hero_form_title = :hero_form_title,
                hero_subheading = :hero_subheading,
                why_choose_image = :why_choose_image,
                why_choose_heading = :why_choose_heading,
                why_choose_content = :why_choose_content,
                about_image = :about_image,
                about_heading = :about_heading,
                about_content = :about_content,
                content_block_1_heading = :content_block_1_heading,
                content_block_1_content = :content_block_1_content,
                selected_projects = :selected_projects,
                content_block_2_content = :content_block_2_content,
                show_testimonials = :show_testimonials,
                testimonials = :testimonials,
                show_faq = :show_faq,
                faqs = :faqs,
                updated_at = NOW(),
                published_at = :published_at
            WHERE id = :id";

            Database::execute($sql, [
                'id' => $id,
                'title' => $page['title'],
                'slug' => $page['slug'],
                'status' => $page['status'],
                'canonical_url' => $page['canonical_url'],
                'meta_title' => $page['meta_title'],
                'meta_description' => $page['meta_description'],
                'meta_keywords' => $page['meta_keywords'],
                'custom_head_html' => $page['custom_head_html'],
                'hero_image' => $page['hero_image'],
                'hero_heading' => $page['hero_heading'],
                'hero_form_title' => $page['hero_form_title'],
                'hero_subheading' => $page['hero_subheading'],
                'why_choose_image' => $page['why_choose_image'],
                'why_choose_heading' => $page['why_choose_heading'],
                'why_choose_content' => $page['why_choose_content'],
                'about_image' => $page['about_image'],
                'about_heading' => $page['about_heading'],
                'about_content' => $page['about_content'],
                'content_block_1_heading' => $page['content_block_1_heading'],
                'content_block_1_content' => $page['content_block_1_content'],
                'selected_projects' => json_encode($page['selected_projects']),
                'content_block_2_content' => $page['content_block_2_content'],
                'show_testimonials' => $page['show_testimonials'] ? 'true' : 'false',
                'testimonials' => json_encode($page['testimonials']),
                'show_faq' => $page['show_faq'] ? 'true' : 'false',
                'faqs' => json_encode($page['faqs']),
                'published_at' => $publishedAt
            ]);

            return ['success' => true, 'page' => $page];
        }

        $all = self::getFromFile();
        foreach ($all as &$item) {
            if ($item['id'] === $id) {
                $page['created_at'] = $item['created_at'] ?? date('Y-m-d H:i:s');
                $page['updated_at'] = date('Y-m-d H:i:s');
                $item = $page;
                break;
            }
        }
        self::saveToFile($all);

        return ['success' => true, 'page' => $page];
    }

    public static function delete(string $id): bool
    {
        if (Database::isConnected()) {
            Database::execute("DELETE FROM seo_pages WHERE id = :id", ['id' => $id]);
            return true;
        }

        $all = self::getFromFile();
        $filtered = array_filter($all, fn($p) => $p['id'] !== $id);
        self::saveToFile(array_values($filtered));
        return true;
    }

    public static function isSlugAvailable(string $slug, ?string $excludeId = null): bool
    {
        $slug = trim($slug, '/');

        // Reserved routes that cannot be used as slug
        $reserved = [
            'admin', 'api', 'home', 'discover', 'products', 'our-story', 'contact',
            'all-pages', 'blogs', 'blog', 'login', 'logout', 'css', 'js', 'assets', 'uploads'
        ];
        if (in_array(strtolower($slug), $reserved)) {
            return false;
        }

        if (Database::isConnected()) {
            $sql = "SELECT id FROM seo_pages WHERE slug = :slug";
            $params = ['slug' => $slug];
            if ($excludeId !== null) {
                $sql .= " AND id != :id";
                $params['id'] = $excludeId;
            }
            $row = Database::fetch($sql, $params);
            return empty($row);
        }

        foreach (self::getFromFile() as $p) {
            if ($p['slug'] === $slug && ($excludeId === null || $p['id'] !== $excludeId)) {
                return false;
            }
        }

        return true;
    }

    public static function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'seo-page-' . substr(uniqid(), 0, 6) : $text;
    }

    private static function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['title'] ?? ''))) {
            $errors['title'] = 'Page title is required.';
        }
        return $errors;
    }

    private static function prepareFields(string $id, array $data, string $slug, string $status, ?string $publishedAt): array
    {
        // Parse selected projects array
        $selectedProjects = [];
        if (!empty($data['selected_projects'])) {
            if (is_array($data['selected_projects'])) {
                $selectedProjects = array_values(array_filter($data['selected_projects']));
            } elseif (is_string($data['selected_projects'])) {
                $decoded = json_decode($data['selected_projects'], true);
                if (is_array($decoded)) {
                    $selectedProjects = $decoded;
                }
            }
        }

        // Parse testimonials array
        $testimonials = [];
        if (!empty($data['testimonials'])) {
            if (is_array($data['testimonials'])) {
                foreach ($data['testimonials'] as $t) {
                    if (!empty(trim($t['name'] ?? '')) || !empty(trim($t['text'] ?? ''))) {
                        $testimonials[] = [
                            'name' => trim($t['name'] ?? ''),
                            'role' => trim($t['role'] ?? ''),
                            'text' => trim($t['text'] ?? ''),
                            'rating' => max(1, min(5, intval($t['rating'] ?? 5)))
                        ];
                    }
                }
            } elseif (is_string($data['testimonials'])) {
                $decoded = json_decode($data['testimonials'], true);
                if (is_array($decoded)) {
                    $testimonials = $decoded;
                }
            }
        }

        // Parse FAQs array
        $faqs = [];
        if (!empty($data['faqs'])) {
            if (is_array($data['faqs'])) {
                foreach ($data['faqs'] as $f) {
                    if (!empty(trim($f['question'] ?? '')) || !empty(trim($f['answer'] ?? ''))) {
                        $faqs[] = [
                            'question' => trim($f['question'] ?? ''),
                            'answer' => trim($f['answer'] ?? '')
                        ];
                    }
                }
            } elseif (is_string($data['faqs'])) {
                $decoded = json_decode($data['faqs'], true);
                if (is_array($decoded)) {
                    $faqs = $decoded;
                }
            }
        }

        return [
            'id' => $id,
            'title' => trim($data['title'] ?? ''),
            'slug' => $slug,
            'status' => $status,
            'canonical_url' => trim($data['canonical_url'] ?? ''),
            'meta_title' => trim($data['meta_title'] ?? ($data['title'] . ' | FEEBLE EXPORTS')),
            'meta_description' => trim($data['meta_description'] ?? ''),
            'meta_keywords' => trim($data['meta_keywords'] ?? ''),
            'custom_head_html' => trim($data['custom_head_html'] ?? ''),
            'hero_image' => trim($data['hero_image'] ?? '/assets/images/01_plain_handloom.png'),
            'hero_heading' => trim($data['hero_heading'] ?? $data['title']),
            'hero_form_title' => trim($data['hero_form_title'] ?? 'Get a Quote'),
            'hero_subheading' => trim($data['hero_subheading'] ?? ''),
            'why_choose_image' => trim($data['why_choose_image'] ?? '/assets/images/02_tufted_pattern.png'),
            'why_choose_heading' => trim($data['why_choose_heading'] ?? 'Why Choose Feeble Exports'),
            'why_choose_content' => $data['why_choose_content'] ?? '',
            'about_image' => trim($data['about_image'] ?? '/assets/images/03_creel_and_rod.png'),
            'about_heading' => trim($data['about_heading'] ?? 'About Feeble Exports'),
            'about_content' => $data['about_content'] ?? '',
            'content_block_1_heading' => trim($data['content_block_1_heading'] ?? ''),
            'content_block_1_content' => $data['content_block_1_content'] ?? '',
            'selected_projects' => $selectedProjects,
            'content_block_2_content' => $data['content_block_2_content'] ?? '',
            'show_testimonials' => isset($data['show_testimonials']) ? (bool)$data['show_testimonials'] : true,
            'testimonials' => $testimonials,
            'show_faq' => isset($data['show_faq']) ? (bool)$data['show_faq'] : true,
            'faqs' => $faqs,
            'published_at' => $publishedAt
        ];
    }

    private static function hydrate(array $row): array
    {
        // Decode JSON fields if stored as JSON strings
        if (isset($row['selected_projects']) && is_string($row['selected_projects'])) {
            $row['selected_projects'] = json_decode($row['selected_projects'], true) ?: [];
        } elseif (!isset($row['selected_projects'])) {
            $row['selected_projects'] = [];
        }

        if (isset($row['testimonials']) && is_string($row['testimonials'])) {
            $row['testimonials'] = json_decode($row['testimonials'], true) ?: [];
        } elseif (!isset($row['testimonials'])) {
            $row['testimonials'] = [];
        }

        if (isset($row['faqs']) && is_string($row['faqs'])) {
            $row['faqs'] = json_decode($row['faqs'], true) ?: [];
        } elseif (!isset($row['faqs'])) {
            $row['faqs'] = [];
        }

        $row['show_testimonials'] = filter_var($row['show_testimonials'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $row['show_faq'] = filter_var($row['show_faq'] ?? true, FILTER_VALIDATE_BOOLEAN);

        return $row;
    }

    private static function getFromFile(): array
    {
        if (!file_exists(self::$storagePath)) {
            return [];
        }
        $content = @file_get_contents(self::$storagePath);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    private static function saveToFile(array $data): void
    {
        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents(self::$storagePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
