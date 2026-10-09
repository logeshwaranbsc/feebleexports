<?php

namespace App\Models;

use App\Core\Database;

class Seo
{
    private static string $storagePath = BASE_PATH . '/storage/seo.json';

    public static function getAll(): array
    {
        if (Database::isConnected()) {
            $rows = Database::fetchAll("SELECT * FROM seo_settings");
            if (!empty($rows)) {
                $mapped = [];
                foreach ($rows as $row) {
                    $mapped[$row['page_key']] = $row;
                }
                return array_merge(self::getDefaults(), $mapped);
            }
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists(self::$storagePath)) {
            $initialSeo = self::getDefaults();
            file_put_contents(self::$storagePath, json_encode($initialSeo, JSON_PRETTY_PRINT));
            return $initialSeo;
        }

        $seo = json_decode(file_get_contents(self::$storagePath), true);
        return is_array($seo) ? array_merge(self::getDefaults(), $seo) : self::getDefaults();
    }

    public static function getByPage(string $pageKey): array
    {
        if (Database::isConnected()) {
            $row = Database::fetch("SELECT * FROM seo_settings WHERE page_key = :key", ['key' => $pageKey]);
            if ($row) return $row;
        }

        $all = self::getAll();
        return $all[$pageKey] ?? [
            'meta_title' => 'FEEBLE EXPORTS - Premium Coir Products',
            'meta_description' => 'Leading manufacturer and exporter of natural coir mats and coconut fibre products from Namakkal, Tamil Nadu, India.',
            'meta_keywords' => 'coir mats, coconut fibre, door mats, eco friendly mats, India coir exporter',
            'og_title' => 'FEEBLE EXPORTS',
            'og_description' => 'Sustainable Coir Mats for a Greener Tomorrow',
            'og_image' => '/assets/images/01_plain_handloom.png'
        ];
    }

    public static function saveAll(array $data): bool
    {
        $existing = self::getAll();

        if (Database::isConnected()) {
            foreach ($data as $pageKey => $pageSeo) {
                if (is_array($pageSeo)) {
                    $sql = "INSERT INTO seo_settings (page_key, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, updated_at)
                            VALUES (:page_key, :meta_title, :meta_description, :meta_keywords, :og_title, :og_description, :og_image, NOW())
                            ON CONFLICT (page_key) DO UPDATE SET
                                meta_title = EXCLUDED.meta_title,
                                meta_description = EXCLUDED.meta_description,
                                meta_keywords = EXCLUDED.meta_keywords,
                                og_title = EXCLUDED.og_title,
                                og_description = EXCLUDED.og_description,
                                og_image = EXCLUDED.og_image,
                                updated_at = NOW()";
                    Database::execute($sql, [
                        'page_key' => $pageKey,
                        'meta_title' => htmlspecialchars(trim($pageSeo['meta_title'] ?? '')),
                        'meta_description' => htmlspecialchars(trim($pageSeo['meta_description'] ?? '')),
                        'meta_keywords' => htmlspecialchars(trim($pageSeo['meta_keywords'] ?? '')),
                        'og_title' => htmlspecialchars(trim($pageSeo['og_title'] ?? '')),
                        'og_description' => htmlspecialchars(trim($pageSeo['og_description'] ?? '')),
                        'og_image' => htmlspecialchars(trim($pageSeo['og_image'] ?? ''))
                    ]);
                }
            }
        }

        $dir = dirname(self::$storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($data as $pageKey => $pageSeo) {
            if (is_array($pageSeo)) {
                $existing[$pageKey] = [
                    'meta_title' => htmlspecialchars(trim($pageSeo['meta_title'] ?? '')),
                    'meta_description' => htmlspecialchars(trim($pageSeo['meta_description'] ?? '')),
                    'meta_keywords' => htmlspecialchars(trim($pageSeo['meta_keywords'] ?? '')),
                    'og_title' => htmlspecialchars(trim($pageSeo['og_title'] ?? '')),
                    'og_description' => htmlspecialchars(trim($pageSeo['og_description'] ?? '')),
                    'og_image' => htmlspecialchars(trim($pageSeo['og_image'] ?? ''))
                ];
            }
        }

        return file_put_contents(self::$storagePath, json_encode($existing, JSON_PRETTY_PRINT)) !== false;
    }

    private static function getDefaults(): array
    {
        return [
            'home' => [
                'meta_title' => 'FEEBLE EXPORTS - Sustainable Coir Mats for a Greener Tomorrow',
                'meta_description' => 'Leading manufacturer & exporter of natural coir mats, tufted mats, PVC backed mats, and coconut fibre products from Namakkal, Tamil Nadu, India.',
                'meta_keywords' => 'coir exporter India, natural coir mats, Namakkal coir exporter, eco friendly doormats, tufted coir matting',
                'og_title' => 'FEEBLE EXPORTS - Sustainable Coir Exporter',
                'og_description' => 'Premium natural coir mats and eco-friendly coconut fibre products crafted in Tamil Nadu, India.',
                'og_image' => '/assets/images/01_plain_handloom.png'
            ],
            'discover' => [
                'meta_title' => 'Discover Us - FEEBLE EXPORTS',
                'meta_description' => 'Learn about FEEBLE EXPORTS, our sustainability commitment, coir processing, and global export destinations.',
                'meta_keywords' => 'coir sustainability, global coir export, Namakkal export manufacturing',
                'og_title' => 'Discover FEEBLE EXPORTS',
                'og_description' => 'Our vision and sustainable coir production process in Namakkal, Tamil Nadu.',
                'og_image' => '/assets/images/04_rope_braided_round.png'
            ],
            'products' => [
                'meta_title' => 'Coir Mats Collection - FEEBLE EXPORTS',
                'meta_description' => 'Explore our complete product catalog: Plain Handloom, Tufted, Creel & Rod, PVC Backed, Rubber Backed, and Coir Carpets.',
                'meta_keywords' => 'coir mat collection, tufted coir, pvc backed coir, coir carpet rolls, rubber backed mats',
                'og_title' => 'Coir Products Catalog | FEEBLE EXPORTS',
                'og_description' => 'Wide collection of sustainable natural coir door mats and floor coverings.',
                'og_image' => '/assets/images/02_tufted_pattern.png'
            ],
            'story' => [
                'meta_title' => 'Our Story - A Simple Mat A Brighter Tomorrow | FEEBLE EXPORTS',
                'meta_description' => 'Read the journey of FEEBLE EXPORTS under the leadership of Kavimayil Venkatachalam in Namakkal, Tamil Nadu.',
                'meta_keywords' => 'FEEBLE EXPORTS story, Kavimayil Venkatachalam, Namakkal coir history',
                'og_title' => 'Our Story - FEEBLE EXPORTS',
                'og_description' => 'From traditional handloom weaving to global export standards.',
                'og_image' => '/assets/images/03_creel_and_rod.png'
            ],
            'contact' => [
                'meta_title' => 'Contact Us & Get a Quote - FEEBLE EXPORTS',
                'meta_description' => 'Reach out to FEEBLE EXPORTS in Namakkal, Tamil Nadu for export inquiries, custom size orders, and wholesale quotes.',
                'meta_keywords' => 'contact FEEBLE EXPORTS, coir quote request, coir exporter contact Namakkal',
                'og_title' => 'Contact FEEBLE EXPORTS',
                'og_description' => 'Get in touch with our export team for coir products quotes and inquiries.',
                'og_image' => '/assets/images/05_pvc_backed.png'
            ],
            'blogs' => [
                'meta_title' => 'Blog & Export Insights - FEEBLE EXPORTS',
                'meta_description' => 'Industry insights, eco-friendly matting trends, and coir manufacturing updates from FEEBLE EXPORTS.',
                'meta_keywords' => 'coir blog, coir export trends, coconut fibre insights',
                'og_title' => 'FEEBLE EXPORTS Blog & News',
                'og_description' => 'Stay updated with eco-friendly product trends and export news.',
                'og_image' => '/assets/images/01_plain_handloom.png'
            ]
        ];
    }
}
