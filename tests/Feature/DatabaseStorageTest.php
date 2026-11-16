<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseStorageTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('PUBLIC_DISK_DRIVER=database');
        $_ENV['PUBLIC_DISK_DRIVER'] = $_SERVER['PUBLIC_DISK_DRIVER'] = 'database';
        parent::setUp();
        // In production this is the backend's /media address (PUBLIC_DISK_URL).
        config(['filesystems.disks.public.url' => 'https://api.example.com/media']);
    }

    protected function tearDown(): void
    {
        putenv('PUBLIC_DISK_DRIVER');
        unset($_ENV['PUBLIC_DISK_DRIVER'], $_SERVER['PUBLIC_DISK_DRIVER']);
        parent::tearDown();
    }

    public function test_uploads_go_to_the_shared_database_and_urls_point_at_the_backend(): void
    {
        $disk = Storage::disk('public');
        $bytes = random_bytes(3000);

        $disk->put('site/new-hero.webp', $bytes);

        $this->assertSame($bytes, $disk->get('site/new-hero.webp'));
        $this->assertSame('https://api.example.com/media/site/new-hero.webp', $disk->url('site/new-hero.webp'));
        $this->assertSame(1, DB::table('media_files')->where('path', 'site/new-hero.webp')->count());
        $this->assertSame('public', $disk->getVisibility('site/new-hero.webp'));

        $disk->delete('site/new-hero.webp');
        $this->assertFalse($disk->exists('site/new-hero.webp'));
    }

    public function test_filament_style_stream_upload(): void
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, 'video-bytes');
        rewind($stream);

        Storage::disk('public')->writeStream('reels/up.mp4', $stream);

        $this->assertSame('video-bytes', Storage::disk('public')->get('reels/up.mp4'));
        $this->assertSame('video/mp4', Storage::disk('public')->mimeType('reels/up.mp4'));
    }
}
