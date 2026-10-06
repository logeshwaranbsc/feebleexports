<?php

namespace App\Models;

class Product
{
    public static function getAll(): array
    {
        return [
            [
                'id' => 'plain-handloom',
                'name' => 'Plain and Handloom',
                'category' => 'Plain & Handloom',
                'category_slug' => 'plain-handloom',
                'image' => '/assets/images/01_plain_handloom.png',
                'description' => 'Classic hand-woven natural coir mat made from high quality 100% natural coconut fibers.'
            ],
            [
                'id' => 'tufted',
                'name' => 'Tufted',
                'category' => 'Tufted',
                'category_slug' => 'tufted',
                'image' => '/assets/images/02_tufted_pattern.png',
                'description' => 'Durable cut-pile tufted coir bristles designed for heavy scraping and soil absorption.'
            ],
            [
                'id' => 'creel-rod',
                'name' => 'Creel and Rod',
                'category' => 'Creel & Rod',
                'category_slug' => 'creel-rod',
                'image' => '/assets/images/03_creel_and_rod.png',
                'description' => 'Heavy-duty steel or wood rod reinforced coir matting built for extreme foot traffic.'
            ],
            [
                'id' => 'rope-braided',
                'name' => 'Rope and Braided',
                'category' => 'Rope & Braided',
                'category_slug' => 'rope-braided',
                'image' => '/assets/images/04_rope_braided_round.png',
                'description' => 'Intricately hand-braided natural coir rope mats featuring rich artisanal patterns.'
            ],
            [
                'id' => 'pvc-backed',
                'name' => 'PVC - Backed',
                'category' => 'Backed Mats',
                'category_slug' => 'backed-mats',
                'image' => '/assets/images/05_pvc_backed.png',
                'description' => 'Non-slip PVC vinyl backing bonded with dense coir fibers for zero displacement.'
            ],
            [
                'id' => 'rubber-backed',
                'name' => 'Rubber-Backed',
                'category' => 'Backed Mats',
                'category_slug' => 'backed-mats',
                'image' => '/assets/images/06_rubber_backed_stack.png',
                'description' => 'Heavy molded rubber frame and base for outdoors weather resistance.'
            ],
            [
                'id' => 'latex-backed',
                'name' => 'Latex-Backed',
                'category' => 'Backed Mats',
                'category_slug' => 'backed-mats',
                'image' => '/assets/images/07_latex_backed.png',
                'description' => 'Eco-friendly natural latex spray backing for flexible anti-skid protection.'
            ],
            [
                'id' => 'printed-logo',
                'name' => 'Printed and Logo',
                'category' => 'Printed & Logo',
                'category_slug' => 'printed-logo',
                'image' => '/assets/images/08_printed_logo_border.png',
                'description' => 'Custom stencilled and screen-printed mats featuring welcome motifs or corporate logos.'
            ],
            [
                'id' => 'bleached-coloured',
                'name' => 'Bleached or Coloured',
                'category' => 'Printed & Logo',
                'category_slug' => 'printed-logo',
                'image' => '/assets/images/09_bleached_coloured_stack.png',
                'description' => 'Sun-bleached blonde coir yarn or AZO-free dyed rich color coir mats.'
            ],
            [
                'id' => 'coir-carpet',
                'name' => 'Coir Carpet',
                'category' => 'Carpet & Rolls',
                'category_slug' => 'carpet-rolls',
                'image' => '/assets/images/10_coir_carpet_roll.png',
                'description' => 'High-end coir runner mats and floor carpets for hallways and eco-interiors.'
            ],
            [
                'id' => 'entryways-rope-knot',
                'name' => 'Entryways Coir Rope Knot Doormat',
                'category' => 'Rope & Braided',
                'category_slug' => 'rope-braided',
                'image' => '/assets/images/11_entryway_coir_rope.png',
                'description' => 'Thick braided coir rope woven into timeless knot designs for luxury entrances.'
            ],
            [
                'id' => 'coir-mats-rolls',
                'name' => 'Coir Mat Rolls',
                'category' => 'Carpet & Rolls',
                'category_slug' => 'carpet-rolls',
                'image' => '/assets/images/12_coir_mat_rolls.png',
                'description' => 'Master roll stock coir matting available for bulk commercial custom cutting.'
            ],
            [
                'id' => 'colours-and-designs',
                'name' => 'Colours and Designs',
                'category' => 'Printed & Logo',
                'category_slug' => 'printed-logo',
                'image' => '/assets/images/13_colours_and_designs.png',
                'description' => 'Vibrant geometric, striped, and multi-color coir fiber combinations.'
            ]
        ];
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
}
