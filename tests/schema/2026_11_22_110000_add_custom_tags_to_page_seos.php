<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Lets editors add their own <meta> tags and structured data (JSON-LD) to a page. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_seos', function (Blueprint $t) {
            $t->json('custom_meta')->nullable()->after('no_index');
            $t->text('json_ld')->nullable()->after('custom_meta');
        });
    }

    public function down(): void
    {
        Schema::table('page_seos', function (Blueprint $t) {
            $t->dropColumn(['custom_meta', 'json_ld']);
        });
    }
};
