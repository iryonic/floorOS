<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        HeroSlide::truncate();

        HeroSlide::create([
            'desktop_image' => '/images/hero/slide-1-desktop.jpg',
            'tablet_image' => '/images/hero/slide-1-tablet.jpg',
            'mobile_image' => '/images/hero/slide-1-mobile.jpg',
            'link_type' => 'catalog',
            'link_url' => '/catalog?category=wood-flooring',
            'alt_text' => 'Grandeur French Chevron Oak Parquet — Atelier Collection',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        HeroSlide::create([
            'desktop_image' => '/images/hero/slide-2-desktop.jpg',
            'tablet_image' => '/images/hero/slide-2-tablet.jpg',
            'mobile_image' => '/images/hero/slide-2-mobile.jpg',
            'link_type' => 'catalog',
            'link_url' => '/catalog?category=spc-flooring',
            'alt_text' => 'AquaShield Pro 100% Waterproof Stone Core SPC Flooring',
            'sort_order' => 2,
            'status' => 'active',
        ]);
    }
}
