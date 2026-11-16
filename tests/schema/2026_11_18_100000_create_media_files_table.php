<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Uploaded images/videos when PUBLIC_DISK_DRIVER=database (no bucket needed). Contents are base64 text.
        Schema::create('media_files', function (Blueprint $t) {
            $t->id();
            $t->string('path', 500)->unique();
            $t->string('mime')->default('application/octet-stream');
            $t->unsignedBigInteger('size')->default(0);
            $t->longText('contents');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
