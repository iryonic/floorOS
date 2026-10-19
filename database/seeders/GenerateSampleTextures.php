<?php

namespace Database\Seeders;

class GenerateSampleTextures
{
    public static function generate(): void
    {
        $dir = public_path('images/flooring');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $textures = [
            // Categories
            'category-wood.svg' => ['base' => '#B88A58', 'sec' => '#8C5E2D', 'label' => 'Wood Flooring', 'pattern' => 'planks'],
            'category-spc.svg' => ['base' => '#D8D4CC', 'sec' => '#A8A298', 'label' => 'SPC Rigid Core', 'pattern' => 'stone'],
            'category-vinyl.svg' => ['base' => '#9C8267', 'sec' => '#705841', 'label' => 'Luxury Vinyl', 'pattern' => 'herringbone'],
            'category-laminate.svg' => ['base' => '#A67C52', 'sec' => '#7D542E', 'label' => 'Laminate Flooring', 'pattern' => 'planks'],
            'category-carpet.svg' => ['base' => '#5C636A', 'sec' => '#3D434A', 'label' => 'Carpet Tiles', 'pattern' => 'weave'],

            // Wood Shades
            'oak-natural.svg' => ['base' => '#C49A6C', 'sec' => '#A3794D', 'label' => 'Natural Oak', 'code' => 'OAK-01', 'pattern' => 'planks'],
            'oak-honey.svg' => ['base' => '#B37D43', 'sec' => '#8D5B25', 'label' => 'Smoked Honey', 'code' => 'OAK-02', 'pattern' => 'planks'],
            'oak-grey.svg' => ['base' => '#8E877F', 'sec' => '#6B655D', 'label' => 'Nordic Ash Grey', 'code' => 'OAK-03', 'pattern' => 'planks'],
            'oak-walnut.svg' => ['base' => '#543C29', 'sec' => '#362416', 'label' => 'Espresso Walnut', 'code' => 'OAK-04', 'pattern' => 'planks'],

            // Chevron Shades
            'chevron-champagne.svg' => ['base' => '#D5BFA0', 'sec' => '#B89F7D', 'label' => 'Champagne Oak', 'code' => 'CHV-01', 'pattern' => 'herringbone'],
            'chevron-cognac.svg' => ['base' => '#9B5B2E', 'sec' => '#743C16', 'label' => 'Aged Cognac', 'code' => 'CHV-02', 'pattern' => 'herringbone'],
            'chevron-platinum.svg' => ['base' => '#B8B5AE', 'sec' => '#8E8A82', 'label' => 'Platinum Frost', 'code' => 'CHV-03', 'pattern' => 'herringbone'],

            // SPC Shades
            'spc-calacatta.svg' => ['base' => '#E8E7E4', 'sec' => '#B0AEA9', 'label' => 'Calacatta Marble', 'code' => 'SPC-01', 'pattern' => 'stone'],
            'spc-barnwood.svg' => ['base' => '#8A6E53', 'sec' => '#5F4631', 'label' => 'Rustic Barnwood', 'code' => 'SPC-02', 'pattern' => 'planks'],
            'spc-driftwood.svg' => ['base' => '#A3998C', 'sec' => '#7B7164', 'label' => 'Coastal Driftwood', 'code' => 'SPC-03', 'pattern' => 'planks'],
            'spc-graphite.svg' => ['base' => '#424548', 'sec' => '#2A2C2E', 'label' => 'Slate Graphite', 'code' => 'SPC-04', 'pattern' => 'stone'],

            // Vinyl Shades
            'lvt-birch.svg' => ['base' => '#CFBBA3', 'sec' => '#AC967D', 'label' => 'Scandinavian Birch', 'code' => 'LVT-01', 'pattern' => 'planks'],
            'lvt-teak.svg' => ['base' => '#966336', 'sec' => '#6E421B', 'label' => 'Vintage Teak', 'code' => 'LVT-02', 'pattern' => 'planks'],
            'lvt-concrete.svg' => ['base' => '#888B8D', 'sec' => '#616466', 'label' => 'Concrete Loft', 'code' => 'LVT-03', 'pattern' => 'stone'],

            // Laminate Shades
            'lam-bavarian.svg' => ['base' => '#AD8358', 'sec' => '#825931', 'label' => 'Bavarian Oak', 'code' => 'LAM-01', 'pattern' => 'planks'],
            'lam-chestnut.svg' => ['base' => '#804E27', 'sec' => '#573010', 'label' => 'Autumn Chestnut', 'code' => 'LAM-02', 'pattern' => 'planks'],
            'lam-ironwood.svg' => ['base' => '#4D3B30', 'sec' => '#31231A', 'label' => 'Ironwood Dark', 'code' => 'LAM-03', 'pattern' => 'planks'],

            // Carpet Shades
            'carpet-slate.svg' => ['base' => '#4B535C', 'sec' => '#2F353C', 'label' => 'Acoustic Slate', 'code' => 'CPT-01', 'pattern' => 'weave'],
            'carpet-charcoal.svg' => ['base' => '#2B3036', 'sec' => '#181C20', 'label' => 'Deep Charcoal', 'code' => 'CPT-02', 'pattern' => 'weave'],
        ];

        foreach ($textures as $filename => $info) {
            $path = $dir . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($path)) {
                continue;
            }

            $svg = self::renderSvg($info);
            file_put_contents($path, $svg);
        }
    }

    private static function renderSvg(array $info): string
    {
        $base = $info['base'];
        $sec = $info['sec'];
        $label = htmlspecialchars($info['label'] ?? '');
        $code = htmlspecialchars($info['code'] ?? '');
        $pattern = $info['pattern'] ?? 'planks';

        $grain = '';
        if ($pattern === 'planks') {
            $grain = <<<SVG
      <rect width="600" height="70" fill="{$base}" stroke="{$sec}" stroke-width="2"/>
      <line x1="20" y1="35" x2="560" y2="35" stroke="{$sec}" stroke-width="1" stroke-dasharray="20 15 40 25"/>
      <rect y="75" width="600" height="70" fill="{$base}" stroke="{$sec}" stroke-width="2"/>
      <line x1="10" y1="110" x2="580" y2="110" stroke="{$sec}" stroke-width="1" stroke-dasharray="30 20 15 35"/>
      <rect y="150" width="600" height="70" fill="{$base}" stroke="{$sec}" stroke-width="2"/>
      <line x1="40" y1="185" x2="540" y2="185" stroke="{$sec}" stroke-width="1" stroke-dasharray="15 30 45 15"/>
      <rect y="225" width="600" height="70" fill="{$base}" stroke="{$sec}" stroke-width="2"/>
      <rect y="300" width="600" height="70" fill="{$base}" stroke="{$sec}" stroke-width="2"/>
      <rect y="375" width="600" height="70" fill="{$base}" stroke="{$sec}" stroke-width="2"/>
SVG;
        } elseif ($pattern === 'herringbone') {
            $grain = <<<SVG
      <g stroke="{$sec}" stroke-width="2" fill="{$base}">
        <polygon points="50,0 200,100 150,150 0,50"/>
        <polygon points="200,100 350,0 400,50 250,150"/>
        <polygon points="50,150 200,250 150,300 0,200"/>
        <polygon points="200,250 350,150 400,200 250,300"/>
        <polygon points="50,300 200,400 150,450 0,350"/>
        <polygon points="200,400 350,300 400,350 250,450"/>
        <polygon points="350,0 500,100 450,150 300,50"/>
        <polygon points="500,100 650,0 700,50 550,150"/>
      </g>
SVG;
        } elseif ($pattern === 'stone') {
            $grain = <<<SVG
      <rect width="600" height="450" fill="{$base}"/>
      <!-- Marble Veining -->
      <path d="M 0 100 Q 150 120 280 80 T 500 160 T 600 120" stroke="{$sec}" stroke-width="4" fill="none" opacity="0.6"/>
      <path d="M 50 0 Q 180 180 320 220 T 550 350" stroke="{$sec}" stroke-width="2.5" fill="none" opacity="0.5"/>
      <path d="M 200 450 Q 280 300 420 260 T 600 290" stroke="{$sec}" stroke-width="3" fill="none" opacity="0.4"/>
      <path d="M 0 350 Q 120 320 220 380" stroke="{$sec}" stroke-width="2" fill="none" opacity="0.4"/>
SVG;
        } else {
            // Weave pattern for carpet
            $grain = <<<SVG
      <rect width="600" height="450" fill="{$base}"/>
      <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
        <rect width="10" height="10" fill="{$sec}" opacity="0.5"/>
        <rect x="10" y="10" width="10" height="10" fill="{$sec}" opacity="0.5"/>
      </pattern>
      <rect width="600" height="450" fill="url(#grid)"/>
SVG;
        }

        return <<<XML
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 450" width="100%" height="100%">
  <rect width="600" height="450" fill="{$base}"/>
  {$grain}
  <!-- Gradient Overlay for Depth -->
  <linearGradient id="overlay" x1="0" y1="0" x2="0" y2="1">
    <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.12"/>
    <stop offset="65%" stop-color="#000000" stop-opacity="0.0"/>
    <stop offset="100%" stop-color="#000000" stop-opacity="0.65"/>
  </linearGradient>
  <rect width="600" height="450" fill="url(#overlay)"/>
  
  <!-- Subtle Tag & Brand Watermark -->
  <g transform="translate(30, 360)">
    <rect width="180" height="52" rx="6" fill="#121815" opacity="0.85"/>
    <text x="16" y="24" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-size="14" font-weight="700">{$label}</text>
    <text x="16" y="42" fill="#D4AF37" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-size="12" font-weight="600" letter-spacing="1">{$code}</text>
  </g>
</svg>
XML;
    }
}
