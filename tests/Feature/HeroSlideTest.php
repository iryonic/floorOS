<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HeroSlideTest extends TestCase
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

    public function test_home_page_displays_responsive_hero_sliding_banners(): void
    {
        $slide1 = HeroSlide::create([
            'desktop_image' => '/images/hero/banner-1-desktop.svg',
            'tablet_image' => '/images/hero/banner-1-tablet.svg',
            'mobile_image' => '/images/hero/banner-1-mobile.svg',
            'link_type' => 'catalog',
            'link_url' => '/catalog?category=wood-flooring',
            'alt_text' => 'Grandeur French Chevron Oak Parquet Banner',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        $slide2 = HeroSlide::create([
            'desktop_image' => '/images/hero/banner-2-desktop.svg',
            'tablet_image' => '/images/hero/banner-2-tablet.svg',
            'mobile_image' => '/images/hero/banner-2-mobile.svg',
            'link_type' => 'catalog',
            'link_url' => '/catalog?category=spc-flooring',
            'alt_text' => 'AquaShield Pro 100% Waterproof SPC Banner',
            'sort_order' => 2,
            'status' => 'active',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('heroBannerCarousel');
        $response->assertSee('heroSlidesTrack');
        $response->assertSee('banner-1-desktop.svg');
        $response->assertSee('banner-1-tablet.svg');
        $response->assertSee('banner-1-mobile.svg');
        $response->assertSee('min-width: 992px');
        $response->assertSee('min-width: 577px');
    }

    public function test_admin_can_access_hero_slides_management(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/hero-slides');

        $response->assertStatus(200);
        $response->assertSee('Hero Sliding Banners');
        $response->assertSee('Desktop Banner (1350×620 px recommended)');
        $response->assertSee('Tablet Banner (1024×640 px / Optional)');
        $response->assertSee('Mobile Banner (768×768 px Square / Optional)');
    }

    public function test_admin_can_create_hero_slide(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/hero-slides', [
            'desktop_banner' => '/images/hero/new-desktop.jpg',
            'tablet_banner' => '/images/hero/new-tablet.jpg',
            'mobile_banner' => '/images/hero/new-mobile.jpg',
            'link_destination' => '/catalog',
            'alt_text' => 'New Season Spring Collection',
            'sort_order' => 3,
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/hero-slides');
        $this->assertDatabaseHas('hero_slides', [
            'desktop_image' => '/images/hero/new-desktop.jpg',
            'tablet_image' => '/images/hero/new-tablet.jpg',
            'mobile_image' => '/images/hero/new-mobile.jpg',
            'link_url' => '/catalog',
            'alt_text' => 'New Season Spring Collection',
            'sort_order' => 3,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_update_hero_slide(): void
    {
        $slide = HeroSlide::create([
            'desktop_image' => '/images/hero/old-desktop.jpg',
            'tablet_image' => '/images/hero/old-tablet.jpg',
            'mobile_image' => '/images/hero/old-mobile.jpg',
            'link_type' => 'none',
            'link_url' => null,
            'alt_text' => 'Old Banner',
            'sort_order' => 1,
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->adminUser)->put("/admin/hero-slides/{$slide->id}", [
            'desktop_banner' => '/images/hero/updated-desktop.jpg',
            'tablet_banner' => '/images/hero/updated-tablet.jpg',
            'mobile_banner' => '/images/hero/updated-mobile.jpg',
            'link_destination' => 'custom',
            'custom_link_url' => 'https://flooros.com/deals',
            'alt_text' => 'Updated Banner Text',
            'sort_order' => 5,
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/hero-slides');
        $this->assertDatabaseHas('hero_slides', [
            'id' => $slide->id,
            'desktop_image' => '/images/hero/updated-desktop.jpg',
            'alt_text' => 'Updated Banner Text',
            'link_url' => 'https://flooros.com/deals',
            'sort_order' => 5,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_delete_hero_slide(): void
    {
        $slide = HeroSlide::create([
            'desktop_image' => '/images/hero/delete-me.jpg',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/hero-slides/{$slide->id}");

        $response->assertRedirect('/admin/hero-slides');
        $this->assertDatabaseMissing('hero_slides', [
            'id' => $slide->id,
        ]);
    }

    public function test_admin_can_reorder_hero_slides_via_drag_and_drop_api(): void
    {
        $slideA = HeroSlide::create([
            'desktop_image' => '/images/hero/slide-a.jpg',
            'sort_order' => 1,
            'status' => 'active',
        ]);
        $slideB = HeroSlide::create([
            'desktop_image' => '/images/hero/slide-b.jpg',
            'sort_order' => 2,
            'status' => 'active',
        ]);
        $slideC = HeroSlide::create([
            'desktop_image' => '/images/hero/slide-c.jpg',
            'sort_order' => 3,
            'status' => 'active',
        ]);

        // Drag slide C to first position: [C, A, B]
        $response = $this->actingAs($this->adminUser)->postJson('/admin/hero-slides/reorder', [
            'order' => [$slideC->id, $slideA->id, $slideB->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(1, $slideC->fresh()->sort_order);
        $this->assertEquals(2, $slideA->fresh()->sort_order);
        $this->assertEquals(3, $slideB->fresh()->sort_order);
    }
}
