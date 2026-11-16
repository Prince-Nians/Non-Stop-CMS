<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Slides of the main homepage banner. kind=banner is a flat image; kind=stadium is the
        // built-in stadium artwork (its images live in the "hero" page section).
        Schema::create('hero_slides', function (Blueprint $t) {
            $t->id();
            $t->string('kind')->default('banner'); // banner | stadium
            $t->string('name');
            $t->string('image')->nullable();
            $t->string('mobile_image')->nullable();
            $t->string('alt')->nullable();
            $t->string('url')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
