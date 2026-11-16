<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fixed set of decorative images (hero cans, checker strips, ellipses). Rows are seeded; editors replace the image.
        Schema::create('site_assets', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->string('label');
            $t->string('used_in')->nullable();
            $t->string('image')->nullable();
            $t->string('alt')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_assets');
    }
};
