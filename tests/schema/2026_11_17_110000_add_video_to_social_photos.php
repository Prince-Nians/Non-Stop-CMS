<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Optional uploaded reel video. When present the popup plays it (muted autoplay) instead of the Instagram embed.
        Schema::table('social_photos', function (Blueprint $t) {
            $t->string('video')->nullable()->after('instagram_url');
        });
    }

    public function down(): void
    {
        Schema::table('social_photos', function (Blueprint $t) {
            $t->dropColumn('video');
        });
    }
};
