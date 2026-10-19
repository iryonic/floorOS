<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Wood Flooring',
                'slug' => 'wood-flooring',
                'description' => 'European Engineered and Solid Hardwoods crafted for timeless luxury and enduring natural grain beauty.',
                'image' => '/images/flooring/category-wood.svg',
                'display_order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'seo_title' => 'Engineered Wood Flooring | FloorOS Gallery',
                'seo_description' => 'Discover authentic European engineered oak, walnut, and herringbone parquet for luxury residences.',
            ],
            [
                'name' => 'SPC Flooring',
                'slug' => 'spc-flooring',
                'description' => '100% Waterproof Stone Plastic Composite rigid core planks with acoustic underlay for high moisture resistance.',
                'image' => '/images/flooring/category-spc.svg',
                'display_order' => 2,
                'is_active' => true,
                'is_featured' => true,
                'seo_title' => 'Waterproof SPC Rigid Core Flooring | FloorOS',
                'seo_description' => 'Heavy duty 100% waterproof SPC flooring with click-lock installation and authentic stone and timber textures.',
            ],
            [
                'name' => 'Luxury Vinyl',
                'slug' => 'luxury-vinyl',
                'description' => 'Acoustic Luxury Vinyl Tiles and Planks (LVT) engineered for warmth, scratch resistance, and easy maintenance.',
                'image' => '/images/flooring/category-vinyl.svg',
                'display_order' => 3,
                'is_active' => true,
                'is_featured' => true,
                'seo_title' => 'Luxury Vinyl Tiles & Planks (LVT) | FloorOS',
                'seo_description' => 'Premium commercial and residential luxury vinyl flooring with advanced ceramic wear layer.',
            ],
            [
                'name' => 'Laminate Flooring',
                'slug' => 'laminate-flooring',
                'description' => 'AC4 and AC5 heavy commercial laminate boards with waxed moisture edges and embossed grain authenticity.',
                'image' => '/images/flooring/category-laminate.svg',
                'display_order' => 4,
                'is_active' => true,
                'is_featured' => true,
                'seo_title' => 'Commercial AC5 Laminate Flooring | FloorOS',
                'seo_description' => 'Durable high-traffic laminate flooring designed for homes, retail showrooms, and office suites.',
            ],
            [
                'name' => 'Carpet Tiles',
                'slug' => 'carpet-tiles',
                'description' => 'Architectural modular carpet tiles engineered for executive suites, boardrooms, and hospitality acoustics.',
                'image' => '/images/flooring/category-carpet.svg',
                'display_order' => 5,
                'is_active' => true,
                'is_featured' => false,
                'seo_title' => 'Modular Commercial Carpet Tiles | FloorOS',
                'seo_description' => 'Heavy duty acoustic modular carpet tiles for corporate offices and hotels.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
