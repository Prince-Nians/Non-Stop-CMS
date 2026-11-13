<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RevalidationTest extends TestCase
{
    public function test_saving_content_pings_the_frontend_once(): void
    {
        Http::fake();
        config(['services.frontend.revalidate_url' => 'http://frontend.test/api/revalidate', 'services.frontend.revalidate_secret' => 's3cret']);

        Product::create(['name' => 'A', 'slug' => 'a'])->update(['name' => 'Renamed']);
        Product::create(['name' => 'B', 'slug' => 'b']);
        $this->app->terminate();

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $r) => $r->url() === 'http://frontend.test/api/revalidate' && $r->header('x-revalidate-secret') === ['s3cret']);
    }

    public function test_no_ping_when_not_configured(): void
    {
        Http::fake();
        config(['services.frontend.revalidate_url' => null]);
        Product::create(['name' => 'X', 'slug' => 'x']);
        $this->app->terminate();
        Http::assertNothingSent();
    }
}
