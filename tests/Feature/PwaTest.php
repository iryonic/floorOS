<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin Manager',
            'email' => 'admin@flooros.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_manifest_json_returns_valid_pwa_configuration(): void
    {
        Setting::set('pwa_name', 'FloorOS Pro Atelier');
        Setting::set('pwa_short_name', 'FloorOS App');

        $response = $this->get('/manifest.json');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/manifest+json; charset=utf-8');

        $data = $response->json();
        $this->assertEquals('FloorOS Pro Atelier', $data['name']);
        $this->assertEquals('FloorOS App', $data['short_name']);
        $this->assertEquals('standalone', $data['display']);
        $this->assertNotEmpty($data['icons']);
        $this->assertNotEmpty($data['shortcuts']);
    }

    public function test_all_pwa_icon_assets_exist_on_disk(): void
    {
        $requiredIcons = [
            'icons/icon-72x72.png',
            'icons/icon-96x96.png',
            'icons/icon-128x128.png',
            'icons/icon-144x144.png',
            'icons/icon-152x152.png',
            'icons/icon-192x192.png',
            'icons/icon-384x384.png',
            'icons/icon-512x512.png',
            'icons/icon-maskable-512x512.png',
            'apple-touch-icon.png',
            'favicon.png',
        ];

        foreach ($requiredIcons as $icon) {
            $path = public_path($icon);
            $this->assertFileExists($path, "Expected PWA icon {$icon} to exist at {$path}");
            $this->assertGreaterThan(100, filesize($path), "Expected {$icon} to have non-zero size");
        }
    }

    public function test_service_worker_script_exists_and_is_valid(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);
        $content = file_get_contents($swPath);
        $this->assertStringContainsString('flooros-pwa', $content);
        $this->assertStringContainsString('addEventListener(\'fetch\'', $content);
    }

    public function test_public_pages_include_pwa_meta_tags_and_install_buttons(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('rel="manifest"', false);
        $response->assertSee('/manifest.json');
        $response->assertSee('pwaInstallBtnNavbar');
        $response->assertSee('pwaInstallBoxDrawer');
        $response->assertSee('triggerPwaInstall');
        $response->assertSee('serviceWorker');
    }

    public function test_admin_can_update_pwa_settings(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/settings', [
            'pwa_name' => 'Custom Wooden Floor App',
            'pwa_short_name' => 'CustomWood',
            'pwa_description' => 'Custom PWA for flooring sales',
            'pwa_theme_color' => '#123456',
            'pwa_background_color' => '#f0f0f0',
        ]);

        $response->assertRedirect('/admin/settings');

        $manifestResponse = $this->get('/manifest.json');
        $manifest = $manifestResponse->json();
        $this->assertEquals('Custom Wooden Floor App', $manifest['name']);
        $this->assertEquals('CustomWood', $manifest['short_name']);
        $this->assertEquals('#123456', $manifest['theme_color']);
        $this->assertEquals('#f0f0f0', $manifest['background_color']);
    }
}
