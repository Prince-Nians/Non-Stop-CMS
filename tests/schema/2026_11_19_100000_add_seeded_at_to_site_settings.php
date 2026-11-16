<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Set when the default content has been loaded completely, so an interrupted first seed is simply resumed.
        Schema::table('site_settings', function (Blueprint $t) {
            $t->timestamp('seeded_at')->nullable();
        });

        // Databases that already hold content count as seeded (never re-seed over editors' work).
        DB::table('site_settings')->update(['seeded_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $t) {
            $t->dropColumn('seeded_at');
        });
    }
};
