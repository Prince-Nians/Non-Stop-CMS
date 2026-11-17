<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Which website page a section belongs to, so the CMS can give every page its own tab. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->string('page', 40)->default('home')->after('key')->index();
        });

        DB::table('page_sections')
            ->whereIn('key', ['contact_hero', 'contact_info', 'contact_form', 'faq', 'marquee'])
            ->update(['page' => 'contact']);
    }

    public function down(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropColumn('page');
        });
    }
};
