<?php

// PWA Icon Generator for FloorOS
$sizes = [
    72 => 'icon-72x72.png',
    96 => 'icon-96x96.png',
    128 => 'icon-128x128.png',
    144 => 'icon-144x144.png',
    152 => 'icon-152x152.png',
    180 => 'apple-touch-icon.png',
    192 => 'icon-192x192.png',
    384 => 'icon-384x384.png',
    512 => 'icon-512x512.png',
];

$outputDir = __DIR__ . '/public/icons';
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0777, true);
}

function generateFloorOSIcon($size, $isMaskable = false) {
    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);
    imagealphablending($img, true);

    // Dark forest emerald slate luxury background
    $bgDark = imagecolorallocate($img, 22, 32, 27);      // #16201B
    $brandGreen = imagecolorallocate($img, 46, 170, 31);   // #2EAA1F
    $brandLight = imagecolorallocate($img, 76, 206, 60);   // #4CCE3C
    $accentGold = imagecolorallocate($img, 226, 177, 104); // #E2B168
    $pureWhite  = imagecolorallocate($img, 255, 255, 255);
    $shadowColor = imagecolorallocatealpha($img, 10, 15, 12, 60);

    // Fill background
    imagefill($img, 0, 0, $bgDark);

    // Subtle background rounded frame if not maskable
    if (!$isMaskable) {
        $radius = (int)($size * 0.22);
        // Clean rounded appearance by drawing background
        // For standard PWA icon, filled dark background is standard
    }

    // Draw luxury layered flooring planks emblem (Isometric Herringbone Parquet Icon)
    $cx = $size / 2;
    $cy = $size / 2;
    $scale = $size / 512;

    // Outer subtle gold ring / accent glow
    imagefilledellipse($img, $cx, $cy, (int)(380 * $scale), (int)(380 * $scale), imagecolorallocatealpha($img, 226, 177, 104, 115));
    imagefilledellipse($img, $cx, $cy, (int)(320 * $scale), (int)(320 * $scale), imagecolorallocatealpha($img, 46, 170, 31, 110));

    // Three architectural interlocking flooring planks (Isometric Parquet "F" / Diamond)
    // Plank 1: Left Angled Plank (Emerald Brand)
    $p1 = [
        $cx - 130 * $scale, $cy - 20 * $scale,
        $cx - 30 * $scale,  $cy - 120 * $scale,
        $cx + 20 * $scale,  $cy - 70 * $scale,
        $cx - 80 * $scale,  $cy + 30 * $scale
    ];
    imagefilledpolygon($img, [
        (int)$p1[0], (int)$p1[1],
        (int)$p1[2], (int)$p1[3],
        (int)$p1[4], (int)$p1[5],
        (int)$p1[6], (int)$p1[7],
    ], $brandGreen);

    // Plank 2: Right Interlocking Plank (Gold Accent)
    $p2 = [
        $cx - 30 * $scale,  $cy - 120 * $scale,
        $cx + 110 * $scale, $cy + 20 * $scale,
        $cx + 60 * $scale,  $cy + 70 * $scale,
        $cx - 80 * $scale,  $cy - 70 * $scale
    ];
    imagefilledpolygon($img, [
        (int)$p2[0], (int)$p2[1],
        (int)$p2[2], (int)$p2[3],
        (int)$p2[4], (int)$p2[5],
        (int)$p2[6], (int)$p2[7],
    ], $accentGold);

    // Plank 3: Lower Supporting Plank (Lighter Emerald / Highlights)
    $p3 = [
        $cx - 90 * $scale,  $cy + 40 * $scale,
        $cx + 50 * $scale,  $cy + 80 * $scale,
        $cx + 20 * $scale,  $cy + 130 * $scale,
        $cx - 120 * $scale, $cy + 90 * $scale
    ];
    imagefilledpolygon($img, [
        (int)$p3[0], (int)$p3[1],
        (int)$p3[2], (int)$p3[3],
        (int)$p3[4], (int)$p3[5],
        (int)$p3[6], (int)$p3[7],
    ], $brandLight);

    // Central crisp "F" architectural monogram
    $fThickness = max(3, (int)(16 * $scale));
    // Vertical stem of F
    imagefilledrectangle($img, (int)($cx - 35 * $scale), (int)($cy - 65 * $scale), (int)($cx - 35 * $scale + $fThickness), (int)($cy + 65 * $scale), $pureWhite);
    // Top bar of F
    imagefilledrectangle($img, (int)($cx - 35 * $scale), (int)($cy - 65 * $scale), (int)($cx + 45 * $scale), (int)($cy - 65 * $scale + $fThickness), $pureWhite);
    // Middle bar of F
    imagefilledrectangle($img, (int)($cx - 35 * $scale), (int)($cy - 5 * $scale), (int)($cx + 25 * $scale), (int)($cy - 5 * $scale + $fThickness), $pureWhite);

    // Subtle edge highlight border
    imagesetthickness($img, max(1, (int)(2 * $scale)));
    imagerectangle($img, 0, 0, $size - 1, $size - 1, imagecolorallocatealpha($img, 255, 255, 255, 115));

    return $img;
}

// Generate all standard icons
foreach ($sizes as $size => $filename) {
    $im = generateFloorOSIcon($size, false);
    $path = $outputDir . '/' . $filename;
    imagepng($im, $path, 9);
    imagedestroy($im);
    echo "Generated {$filename} ({$size}x{$size})\n";
}

// Generate Maskable 512x512
$maskable = generateFloorOSIcon(512, true);
imagepng($maskable, $outputDir . '/icon-maskable-512x512.png', 9);
imagedestroy($maskable);
echo "Generated icon-maskable-512x512.png\n";

// Copy apple-touch-icon to public root as well
copy($outputDir . '/apple-touch-icon.png', __DIR__ . '/public/apple-touch-icon.png');
// Copy 192 icon as favicon fallback
copy($outputDir . '/icon-192x192.png', __DIR__ . '/public/favicon.png');
echo "PWA Icons generated successfully!\n";
