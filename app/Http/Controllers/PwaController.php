<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PwaController extends Controller
{
    /**
     * Generate dynamic PWA Web App Manifest configured from Admin Settings.
     */
    public function manifest(): JsonResponse
    {
        $name = Setting::get('pwa_name', Setting::get('store_name', 'FloorOS — Flooring Gallery & Atelier'));
        $shortName = Setting::get('pwa_short_name', 'FloorOS');
        $description = Setting::get('pwa_description', Setting::get('store_tagline', 'Premium Engineered Hardwood, Waterproof SPC & Luxury Surfaces Gallery'));
        $themeColor = Setting::get('pwa_theme_color', Setting::get('theme_primary_dark', '#1E2A24'));
        $bgColor = Setting::get('pwa_background_color', '#FFFFFF');
        $display = Setting::get('pwa_display', 'standalone');
        $orientation = Setting::get('pwa_orientation', 'portrait-primary');
        $startUrl = Setting::get('pwa_start_url', '/');

        $manifest = [
            'name' => $name,
            'short_name' => $shortName,
            'description' => $description,
            'id' => '/?source=pwa',
            'start_url' => $startUrl,
            'scope' => '/',
            'display' => $display,
            'orientation' => $orientation,
            'background_color' => $bgColor,
            'theme_color' => $themeColor,
            'categories' => ['shopping', 'business', 'lifestyle'],
            'icons' => [
                [
                    'src' => '/icons/icon-72x72.png',
                    'sizes' => '72x72',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => '/icons/icon-96x96.png',
                    'sizes' => '96x96',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => '/icons/icon-128x128.png',
                    'sizes' => '128x128',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => '/icons/icon-144x144.png',
                    'sizes' => '144x144',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => '/icons/icon-152x152.png',
                    'sizes' => '152x152',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => '/icons/icon-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => '/icons/icon-384x384.png',
                    'sizes' => '384x384',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => '/icons/icon-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => '/icons/icon-maskable-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable'
                ]
            ],
            'shortcuts' => [
                [
                    'name' => 'Flooring Catalog',
                    'short_name' => 'Catalog',
                    'description' => 'Browse wood, laminate, SPC, and carpet tile collections',
                    'url' => '/catalog?utm_source=pwa_shortcut',
                    'icons' => [
                        [
                            'src' => '/icons/icon-96x96.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ],
                [
                    'name' => 'Showroom Atelier',
                    'short_name' => 'Showroom',
                    'description' => 'Book consultation & showroom appointment',
                    'url' => '/contact?utm_source=pwa_shortcut',
                    'icons' => [
                        [
                            'src' => '/icons/icon-96x96.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ]
            ]
        ];

        return response()->json($manifest, 200, [
            'Content-Type' => 'application/manifest+json; charset=utf-8',
            'Cache-Control' => 'no-cache, private'
        ]);
    }
}
