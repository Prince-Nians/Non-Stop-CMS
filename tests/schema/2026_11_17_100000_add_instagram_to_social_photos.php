<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_photos', function (Blueprint $t) {
            // A reel/post can exist without a card photo (only three cards are shown on the page).
            $t->string('image')->nullable()->change();
            $t->string('instagram_url')->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('social_photos', function (Blueprint $t) {
            $t->dropColumn('instagram_url');
        });
    }
};
