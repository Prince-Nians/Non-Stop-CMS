<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One slide per flavour in the About Us banner slider.
        Schema::create('about_slides', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('can_image')->nullable();
            $t->string('ribbon_color_a', 9)->default('#0062a0');
            $t->string('ribbon_color_b', 9)->default('#21306a');
            $t->string('title_color', 9)->default('#053588');
            $t->string('number_color', 9)->default('#0062a0');
            $t->string('point_title_color', 9)->default('#21306a');
            $t->string('button_color', 9)->default('#008240');
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_slides');
    }
};
