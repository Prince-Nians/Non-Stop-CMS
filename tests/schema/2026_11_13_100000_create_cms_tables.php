<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $t) {
            $t->id();
            $t->string('site_name')->default('NON STOP');
            $t->string('logo')->nullable();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->string('copyright_text')->nullable();
            $t->string('facebook_url')->nullable();
            $t->string('youtube_url')->nullable();
            $t->string('linkedin_url')->nullable();
            $t->string('twitter_url')->nullable();
            $t->string('contact_notify_email')->nullable();
            $t->string('partner_notify_email')->nullable();
            $t->string('seo_title')->nullable();
            $t->text('seo_description')->nullable();
            $t->timestamps();
        });

        Schema::create('shops', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('url');
            $t->string('logo')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('nav_links', function (Blueprint $t) {
            $t->id();
            $t->string('location'); // header | footer | legal
            $t->string('label');
            $t->string('url');
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->index(['location', 'sort_order']);
        });

        Schema::create('page_sections', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique(); // hero, products, partner, about, social, events, blogs
            $t->string('title')->nullable();
            $t->string('subtitle')->nullable();
            $t->text('description')->nullable();
            $t->string('button_label')->nullable();
            $t->string('button_url')->nullable();
            $t->string('image')->nullable();
            $t->string('image_secondary')->nullable();
            $t->json('extra')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->string('color', 9)->default('#053588');
            $t->string('border_color', 9)->default('#74c5e3');
            $t->string('background_image')->nullable();
            $t->string('can_image')->nullable();
            $t->string('button_label')->default('Explore Flavour');
            $t->string('button_url')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('about_points', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->text('body')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('social_photos', function (Blueprint $t) {
            $t->id();
            $t->string('image');
            $t->string('alt')->nullable();
            $t->string('url')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('events', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->text('excerpt')->nullable();
            $t->longText('body')->nullable();
            $t->string('image')->nullable();
            $t->timestamp('starts_at')->nullable();
            $t->string('location')->nullable();
            $t->string('url')->nullable();
            $t->boolean('is_published')->default(true);
            $t->timestamp('published_at')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->string('seo_title')->nullable();
            $t->text('seo_description')->nullable();
            $t->timestamps();
            $t->index(['is_published', 'published_at']);
        });

        Schema::create('blog_posts', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('tag')->default('Blog');
            $t->text('excerpt')->nullable();
            $t->longText('body')->nullable();
            $t->string('image')->nullable();
            $t->string('thumbnail')->nullable();
            $t->boolean('is_featured')->default(false);
            $t->boolean('is_published')->default(true);
            $t->timestamp('published_at')->nullable();
            $t->string('seo_title')->nullable();
            $t->text('seo_description')->nullable();
            $t->timestamps();
            $t->index(['is_published', 'published_at']);
        });

        Schema::create('enquiries', function (Blueprint $t) {
            $t->id();
            $t->string('type'); // contact | partner
            $t->string('name');
            $t->string('email');
            $t->string('phone')->nullable();
            $t->string('company')->nullable();
            $t->string('city')->nullable();
            $t->text('message')->nullable();
            $t->string('status')->default('new'); // new | read | replied | archived
            $t->json('meta')->nullable();
            $t->string('ip_address', 45)->nullable();
            $t->timestamps();
            $t->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        foreach (['enquiries', 'blog_posts', 'events', 'social_photos', 'about_points', 'products', 'page_sections', 'nav_links', 'shops', 'site_settings'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
