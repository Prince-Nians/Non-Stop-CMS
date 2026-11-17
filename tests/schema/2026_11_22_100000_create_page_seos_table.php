<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** One row of search-engine / social-sharing tags per website page (home, contact, and later about, ...). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_seos', function (Blueprint $t) {
            $t->id();
            $t->string('page', 40)->unique();
            $t->string('meta_title')->nullable();
            $t->text('meta_description')->nullable();
            $t->string('keywords')->nullable();
            $t->string('og_title')->nullable();
            $t->text('og_description')->nullable();
            $t->string('og_image')->nullable();
            $t->string('canonical_url')->nullable();
            $t->boolean('no_index')->default(false);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_seos');
    }
};
