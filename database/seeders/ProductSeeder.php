<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductShade;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $wood = Category::where('slug', 'wood-flooring')->first();
        $spc = Category::where('slug', 'spc-flooring')->first();
        $vinyl = Category::where('slug', 'luxury-vinyl')->first();
        $laminate = Category::where('slug', 'laminate-flooring')->first();
        $carpet = Category::where('slug', 'carpet-tiles')->first();

        // 1. Royal Oak Heritage
        $p1 = Product::updateOrCreate(
            ['slug' => 'royal-oak-heritage-engineered-hardwood'],
            [
                'category_id' => $wood->id,
                'name' => 'Royal Oak Heritage Collection',
                'brand' => 'Mikasa Atelier',
                'collection_name' => 'Heritage Reserve',
                'material_type' => 'Engineered European Oak',
                'thickness' => '14 mm (3mm real oak wear layer)',
                'dimensions' => '1820 mm x 190 mm (6 ft x 7.5 in)',
                'surface_finish' => 'Wire-brushed UV Matte Lacquer',
                'suitable_for' => 'Living Rooms, Master Suites, High-end Boutiques',
                'warranty_years' => '25 Years Residential / 10 Years Commercial',
                'installation_type' => 'Valinge 5G Click-Lock or Full Glue-Down',
                'unit_of_measure' => 'sq ft',
                'coverage_per_box' => 22.35,
                'primary_image' => '/images/flooring/oak-natural.svg',
                'gallery_images' => [
                    '/images/flooring/oak-natural.svg',
                    '/images/flooring/oak-honey.svg',
                    '/images/flooring/oak-grey.svg',
                    '/images/flooring/oak-walnut.svg',
                ],
                'description' => 'Precision-milled from sustainable European Oak forests, Royal Oak Heritage features a balanced 3-ply core construction that resists humidity shifts while providing deep, tactile grain texture.',
                'specifications' => [
                    'Origin' => 'Austria & Germany',
                    'Grade' => 'Select & Better Character',
                    'Bevel' => '4-sided micro-bevel',
                    'Formaldehyde Emission' => 'E1 Certified Non-toxic',
                ],
                'base_price' => 485.00,
                'is_featured' => true,
                'status' => 'published',
                'is_public' => true,
                'seo_title' => 'Royal Oak Heritage Engineered Hardwood | FloorOS',
                'seo_description' => '14mm engineered European oak flooring with 25-year warranty and Valinge 5G click-lock system.',
            ]
        );

        $shadesP1 = [
            [
                'shade_name' => 'Natural Oak',
                'shade_code' => 'OAK-01',
                'sku' => 'ROH-NAT-01',
                'color_family' => 'Natural Beige',
                'finish' => 'Matte Brushed',
                'image' => '/images/flooring/oak-natural.svg',
                'selling_price' => 485.00,
                'cost_price' => 320.00,
                'stock_quantity' => 1250.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 150.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Smoked Honey',
                'shade_code' => 'OAK-02',
                'sku' => 'ROH-HNY-02',
                'color_family' => 'Warm Amber',
                'finish' => 'Satin Brushed',
                'image' => '/images/flooring/oak-honey.svg',
                'selling_price' => 520.00,
                'cost_price' => 340.00,
                'stock_quantity' => 920.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 150.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Nordic Ash Grey',
                'shade_code' => 'OAK-03',
                'sku' => 'ROH-GRY-03',
                'color_family' => 'Cool Grey',
                'finish' => 'Ultra Matte',
                'image' => '/images/flooring/oak-grey.svg',
                'selling_price' => 530.00,
                'cost_price' => 345.00,
                'stock_quantity' => 450.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 100.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Espresso Walnut',
                'shade_code' => 'OAK-04',
                'sku' => 'ROH-WAL-04',
                'color_family' => 'Dark Walnut',
                'finish' => 'Deep Hand-scraped',
                'image' => '/images/flooring/oak-walnut.svg',
                'selling_price' => 560.00,
                'cost_price' => 360.00,
                'stock_quantity' => 60.00, // Low Stock!
                'unit' => 'sq ft',
                'reorder_threshold' => 100.00,
                'is_active' => true,
                'is_public' => true,
            ],
        ];

        foreach ($shadesP1 as $s) {
            ProductShade::updateOrCreate(['sku' => $s['sku']], array_merge($s, ['product_id' => $p1->id]));
        }

        // 2. Grandeur Chevron Parquet
        $p2 = Product::updateOrCreate(
            ['slug' => 'grandeur-french-chevron-parquet'],
            [
                'category_id' => $wood->id,
                'name' => 'Grandeur French Chevron Parquet',
                'brand' => 'Haute Boiserie',
                'collection_name' => 'Parisian Parquet',
                'material_type' => 'French White Oak Chevron (45°)',
                'thickness' => '15 mm (4mm top layer)',
                'dimensions' => '600 mm x 120 mm (Left & Right pairs)',
                'surface_finish' => 'Natural Oxidative Oil / Hardwax',
                'suitable_for' => 'Formal Dining, Art Galleries, Luxury Penthouses',
                'warranty_years' => '30 Years Structural',
                'installation_type' => 'Full Elastomeric Glue-down',
                'unit_of_measure' => 'sq ft',
                'coverage_per_box' => 18.60,
                'primary_image' => '/images/flooring/chevron-champagne.svg',
                'gallery_images' => [
                    '/images/flooring/chevron-champagne.svg',
                    '/images/flooring/chevron-cognac.svg',
                    '/images/flooring/chevron-platinum.svg',
                ],
                'description' => 'A timeless 45-degree angle Chevron pattern crafted from prime French Oak. Emulates 19th-century Haussmann Parisian apartments with acoustic stability and hand-rolled edges.',
                'specifications' => [
                    'Angle' => '45-degree precision bevel',
                    'Underfloor Heating' => 'Compatible up to 27°C',
                    'Pattern' => 'Matching Left & Right interlocking tongues',
                ],
                'base_price' => 690.00,
                'is_featured' => true,
                'status' => 'published',
                'is_public' => true,
                'seo_title' => 'French Chevron Parquet Engineered Flooring | FloorOS',
                'seo_description' => 'Architectural French chevron parquet in prime white oak with handwax finish.',
            ]
        );

        $shadesP2 = [
            [
                'shade_name' => 'Champagne Oak',
                'shade_code' => 'CHV-01',
                'sku' => 'CHV-CHM-01',
                'color_family' => 'Light Blonde',
                'finish' => 'Natural Oil',
                'image' => '/images/flooring/chevron-champagne.svg',
                'selling_price' => 690.00,
                'cost_price' => 450.00,
                'stock_quantity' => 800.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 150.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Aged Cognac',
                'shade_code' => 'CHV-02',
                'sku' => 'CHV-COG-02',
                'color_family' => 'Rich Chestnut',
                'finish' => 'Smoked Oil',
                'image' => '/images/flooring/chevron-cognac.svg',
                'selling_price' => 720.00,
                'cost_price' => 480.00,
                'stock_quantity' => 540.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 120.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Platinum Frost',
                'shade_code' => 'CHV-03',
                'sku' => 'CHV-PLT-03',
                'color_family' => 'Silver White',
                'finish' => 'Limed Brushed',
                'image' => '/images/flooring/chevron-platinum.svg',
                'selling_price' => 710.00,
                'cost_price' => 470.00,
                'stock_quantity' => 0.00, // Out of Stock!
                'unit' => 'sq ft',
                'reorder_threshold' => 100.00,
                'is_active' => true,
                'is_public' => true,
            ],
        ];

        foreach ($shadesP2 as $s) {
            ProductShade::updateOrCreate(['sku' => $s['sku']], array_merge($s, ['product_id' => $p2->id]));
        }

        // 3. AquaShield Pro Stone-Core SPC
        $p3 = Product::updateOrCreate(
            ['slug' => 'aquashield-pro-waterproof-spc-flooring'],
            [
                'category_id' => $spc->id,
                'name' => 'AquaShield Pro Stone-Core SPC',
                'brand' => 'ShieldCore Tech',
                'collection_name' => 'HydroGuard Titanium',
                'material_type' => '70% Limestone Composite + Virgin Polymer',
                'thickness' => '6.5 mm (5mm core + 1.5mm anti-microbial IXPE acoustic pad)',
                'dimensions' => '1220 mm x 180 mm (48 in x 7 in)',
                'surface_finish' => 'Embossed In Register (EIR) Texture',
                'suitable_for' => 'Kitchens, Bathrooms, Basements, Commercial Stores',
                'warranty_years' => 'Lifetime Residential / 15 Years Commercial',
                'installation_type' => 'Unilin Floating Click-Lock',
                'unit_of_measure' => 'sq ft',
                'coverage_per_box' => 24.50,
                'primary_image' => '/images/flooring/spc-calacatta.svg',
                'gallery_images' => [
                    '/images/flooring/spc-calacatta.svg',
                    '/images/flooring/spc-barnwood.svg',
                    '/images/flooring/spc-driftwood.svg',
                    '/images/flooring/spc-graphite.svg',
                ],
                'description' => 'Zero-swelling 100% waterproof flooring built with high-density limestone composite. Withstands heavy appliances, pet claws, and continuous water exposure without warping or denting.',
                'specifications' => [
                    'Water Resistance' => '100% Submersion Proof',
                    'Wear Layer' => '0.55 mm (22 mil) Ceramic Bead',
                    'Sound Insulation' => 'STC 72 / IIC 73 Acoustic Dampening',
                ],
                'base_price' => 175.00,
                'is_featured' => true,
                'status' => 'published',
                'is_public' => true,
                'seo_title' => 'AquaShield 100% Waterproof SPC Flooring | FloorOS',
                'seo_description' => 'Heavy duty stone-core SPC click-lock flooring with integrated acoustic pad and 22mil wear layer.',
            ]
        );

        $shadesP3 = [
            [
                'shade_name' => 'Calacatta Marble',
                'shade_code' => 'SPC-01',
                'sku' => 'SPC-CAL-01',
                'color_family' => 'White Marble',
                'finish' => 'Honed Stone Touch',
                'image' => '/images/flooring/spc-calacatta.svg',
                'selling_price' => 185.00,
                'cost_price' => 110.00,
                'stock_quantity' => 2400.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 250.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Rustic Barnwood',
                'shade_code' => 'SPC-02',
                'sku' => 'SPC-BRN-02',
                'color_family' => 'Warm Brown',
                'finish' => 'Registered Timber Grain',
                'image' => '/images/flooring/spc-barnwood.svg',
                'selling_price' => 175.00,
                'cost_price' => 105.00,
                'stock_quantity' => 1800.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 200.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Coastal Driftwood',
                'shade_code' => 'SPC-03',
                'sku' => 'SPC-CST-03',
                'color_family' => 'Muted Grey',
                'finish' => 'Brushed Grain',
                'image' => '/images/flooring/spc-driftwood.svg',
                'selling_price' => 180.00,
                'cost_price' => 108.00,
                'stock_quantity' => 95.00, // Low Stock!
                'unit' => 'sq ft',
                'reorder_threshold' => 150.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Slate Graphite',
                'shade_code' => 'SPC-04',
                'sku' => 'SPC-SLT-04',
                'color_family' => 'Charcoal Dark',
                'finish' => 'Chiseled Slate Texture',
                'image' => '/images/flooring/spc-graphite.svg',
                'selling_price' => 195.00,
                'cost_price' => 115.00,
                'stock_quantity' => 1500.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 150.00,
                'is_active' => true,
                'is_public' => true,
            ],
        ];

        foreach ($shadesP3 as $s) {
            ProductShade::updateOrCreate(['sku' => $s['sku']], array_merge($s, ['product_id' => $p3->id]));
        }

        // 4. Moderna Plank LVT
        $p4 = Product::updateOrCreate(
            ['slug' => 'moderna-acoustic-luxury-vinyl-planks'],
            [
                'category_id' => $vinyl->id,
                'name' => 'Moderna Acoustic Luxury Vinyl Planks',
                'brand' => 'Decoria Surfaces',
                'collection_name' => 'Urban Living',
                'material_type' => 'Commercial Grade Polyvinyl Chloride (LVT)',
                'thickness' => '4.5 mm (0.5mm diamond wear layer)',
                'dimensions' => '914 mm x 152 mm (36 in x 6 in)',
                'surface_finish' => 'Matte Textured Anti-Slip (R10)',
                'suitable_for' => 'Modern Apartments, Cafes, Fitness Studios, Clinics',
                'warranty_years' => '20 Years Residential / 10 Years Commercial',
                'installation_type' => 'Pressure-Sensitive Glue Down',
                'unit_of_measure' => 'sq ft',
                'coverage_per_box' => 36.00,
                'primary_image' => '/images/flooring/lvt-birch.svg',
                'gallery_images' => [
                    '/images/flooring/lvt-birch.svg',
                    '/images/flooring/lvt-teak.svg',
                    '/images/flooring/lvt-concrete.svg',
                ],
                'description' => 'Soft, warm, and whisper-quiet underfoot. Moderna LVT delivers outstanding durability with commercial-grade scuff shielding, making it ideal for high-traffic environments.',
                'specifications' => [
                    'Slip Rating' => 'R10 Slip Resistance',
                    'Flame Retardancy' => 'Bfl-s1 Fire Classified',
                    'Indoor Air Quality' => 'FloorScore Certified Low VOC',
                ],
                'base_price' => 145.00,
                'is_featured' => false,
                'status' => 'published',
                'is_public' => true,
                'seo_title' => 'Moderna Acoustic Luxury Vinyl Planks | FloorOS',
                'seo_description' => 'Commercial grade 4.5mm glue-down acoustic luxury vinyl planks with R10 slip rating.',
            ]
        );

        $shadesP4 = [
            [
                'shade_name' => 'Scandinavian Birch',
                'shade_code' => 'LVT-01',
                'sku' => 'LVT-BRC-01',
                'color_family' => 'Blonde Cream',
                'finish' => 'Fine Sanded',
                'image' => '/images/flooring/lvt-birch.svg',
                'selling_price' => 145.00,
                'cost_price' => 85.00,
                'stock_quantity' => 1400.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 150.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Vintage Teak',
                'shade_code' => 'LVT-02',
                'sku' => 'LVT-TEK-02',
                'color_family' => 'Golden Brown',
                'finish' => 'Oiled Grain',
                'image' => '/images/flooring/lvt-teak.svg',
                'selling_price' => 155.00,
                'cost_price' => 90.00,
                'stock_quantity' => 1100.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 150.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Concrete Loft',
                'shade_code' => 'LVT-03',
                'sku' => 'LVT-CON-03',
                'color_family' => 'Industrial Grey',
                'finish' => 'Smooth Concrete',
                'image' => '/images/flooring/lvt-concrete.svg',
                'selling_price' => 160.00,
                'cost_price' => 95.00,
                'stock_quantity' => 750.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 100.00,
                'is_active' => true,
                'is_public' => true,
            ],
        ];

        foreach ($shadesP4 as $s) {
            ProductShade::updateOrCreate(['sku' => $s['sku']], array_merge($s, ['product_id' => $p4->id]));
        }

        // 5. Titan Commercial AC5 Laminate
        $p5 = Product::updateOrCreate(
            ['slug' => 'titan-heavy-commercial-ac5-laminate'],
            [
                'category_id' => $laminate->id,
                'name' => 'Titan Commercial AC5 Laminate',
                'brand' => 'Kronos Floors Germany',
                'collection_name' => 'Titanium Fortis',
                'material_type' => 'High Density Fibreboard (HDF > 920 kg/m³)',
                'thickness' => '12 mm (Heavy Commercial AC5 Rating)',
                'dimensions' => '1380 mm x 193 mm (54 in x 7.6 in)',
                'surface_finish' => 'Synchronized Embossed Wood Pore',
                'suitable_for' => 'Department Stores, Showrooms, Busy Family Residences',
                'warranty_years' => '30 Years Residential / 15 Years Commercial',
                'installation_type' => 'Twin Clic Wax-Sealed Edges',
                'unit_of_measure' => 'sq ft',
                'coverage_per_box' => 20.10,
                'primary_image' => '/images/flooring/lam-bavarian.svg',
                'gallery_images' => [
                    '/images/flooring/lam-bavarian.svg',
                    '/images/flooring/lam-chestnut.svg',
                    '/images/flooring/lam-ironwood.svg',
                ],
                'description' => 'German-engineered AC5 laminate floor featuring wax-impregnated joint protection against accidental spills. Unsurpassed resistance to stiletto heels, rolling chairs, and abrasive grit.',
                'specifications' => [
                    'Abrasion Class' => 'AC5 / Class 33 Heavy Commercial',
                    'Impact Resistance' => 'IC3 Extreme Impact',
                    'Edge Protection' => 'HydroPlus Wax Coating',
                ],
                'base_price' => 125.00,
                'is_featured' => true,
                'status' => 'published',
                'is_public' => true,
                'seo_title' => 'German AC5 Heavy Commercial Laminate Flooring | FloorOS',
                'seo_description' => '12mm German AC5 commercial laminate flooring with wax-sealed moisture edges.',
            ]
        );

        $shadesP5 = [
            [
                'shade_name' => 'Bavarian Oak',
                'shade_code' => 'LAM-01',
                'sku' => 'LAM-BAV-01',
                'color_family' => 'Warm Oak',
                'finish' => 'Synchronized Pore',
                'image' => '/images/flooring/lam-bavarian.svg',
                'selling_price' => 125.00,
                'cost_price' => 70.00,
                'stock_quantity' => 2100.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 250.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Autumn Chestnut',
                'shade_code' => 'LAM-02',
                'sku' => 'LAM-AUT-02',
                'color_family' => 'Reddish Brown',
                'finish' => 'Matte Oiled Look',
                'image' => '/images/flooring/lam-chestnut.svg',
                'selling_price' => 130.00,
                'cost_price' => 72.00,
                'stock_quantity' => 1600.00,
                'unit' => 'sq ft',
                'reorder_threshold' => 200.00,
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'shade_name' => 'Ironwood Dark',
                'shade_code' => 'LAM-03',
                'sku' => 'LAM-IRN-03',
                'color_family' => 'Smoked Charcoal',
                'finish' => 'Deep Textured',
                'image' => '/images/flooring/lam-ironwood.svg',
                'selling_price' => 135.00,
                'cost_price' => 75.00,
                'stock_quantity' => 80.00, // Low stock!
                'unit' => 'sq ft',
                'reorder_threshold' => 150.00,
                'is_active' => true,
                'is_public' => true,
            ],
        ];

        foreach ($shadesP5 as $s) {
            ProductShade::updateOrCreate(['sku' => $s['sku']], array_merge($s, ['product_id' => $p5->id]));
        }

        // Initial stock movements record
        foreach (ProductShade::all() as $shade) {
            if ($shade->stock_quantity > 0) {
                StockMovement::firstOrCreate(
                    ['product_shade_id' => $shade->id, 'type' => 'opening'],
                    [
                        'reference_number' => 'MOV-INIT-' . $shade->sku,
                        'sku' => $shade->sku,
                        'type' => 'opening',
                        'direction' => 'in',
                        'quantity' => $shade->stock_quantity,
                        'unit' => $shade->unit,
                        'unit_cost' => $shade->cost_price,
                        'previous_stock' => 0,
                        'new_stock' => $shade->stock_quantity,
                        'reason' => 'Store opening balance inventory',
                        'occurred_at' => now()->subDays(60),
                    ]
                );
            }
        }
    }
}
