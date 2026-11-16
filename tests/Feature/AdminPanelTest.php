<?php

namespace Tests\Feature;

use App\Models\{AboutPoint, AboutSlide, HeroSlide, BlogPost, Event, NavLink, PageSection, Product, Shop, SocialPhoto, User};
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    public function test_login_page_loads(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_guests_are_redirected(): void
    {
        $this->get('/admin/products')->assertRedirect('/admin/login');
    }

    public function test_every_resource_index_and_edit_page_renders(): void
    {
        User::create(['name' => 'Admin', 'email' => 'a@example.com', 'password' => 'secret-pass-123']);
        $this->actingAs(User::first());

        $records = [
            'products' => Product::create(['name' => 'Classic', 'slug' => 'classic']),
            'shops' => Shop::create(['name' => 'Zepto', 'slug' => 'zepto', 'url' => 'https://zepto.test']),
            'nav-links' => NavLink::create(['location' => 'header', 'label' => 'Home', 'url' => '#top']),
            'page-sections' => PageSection::create(['key' => 'hero', 'title' => 'Hello']),
            'hero-slides' => HeroSlide::create(['name' => 'Jungle', 'kind' => 'banner', 'image' => 'site/x.webp']),
            'about-slides' => AboutSlide::create(['name' => 'Classic']),
            'about-points' => AboutPoint::create(['title' => 'Point']),
            'social-photos' => SocialPhoto::create(['image' => null, 'instagram_url' => 'https://www.instagram.com/reel/AbC123/']),
            'events' => Event::create(['title' => 'Ev', 'slug' => 'ev']),
            'blog-posts' => BlogPost::create(['title' => 'Post', 'slug' => 'post']),
        ];

        foreach ($records as $slug => $record) {
            $this->get("/admin/$slug")->assertOk();
            $this->get("/admin/$slug/{$record->getKey()}/edit")->assertOk();
        }
        foreach (['enquiries', 'site-settings'] as $slug) {
            $this->get("/admin/$slug")->assertOk();
        }
        $this->get('/admin/products/create')->assertOk();
    }
}
