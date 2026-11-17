<?php

namespace App\Support;

use App\Models\{AboutPoint, AboutSlide, Faq, HeroSlide, SiteAsset, BlogPost, Event, NavLink, PageSection, PageSeo, Product, Shop, SiteSetting, SocialPhoto};
use Illuminate\Support\Facades\Event as Events;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Tells the Next.js site to refresh its cached homepage whenever CMS content changes. */
class FrontendRevalidator
{
    private const MODELS = [Faq::class, SiteAsset::class, HeroSlide::class, AboutPoint::class, AboutSlide::class, BlogPost::class, Event::class, NavLink::class, PageSection::class, PageSeo::class, Product::class, Shop::class, SiteSetting::class, SocialPhoto::class];

    private static bool $pending = false;

    public static function listen(): void
    {
        foreach (self::MODELS as $model) {
            foreach (['saved', 'deleted'] as $event) {
                Events::listen("eloquent.{$event}: {$model}", fn () => self::ping());
            }
        }
    }

    private static function ping(): void
    {
        $url = config('services.frontend.revalidate_url');
        if (! $url || self::$pending) {
            return; // not configured, or already pinged during this request (e.g. seeding)
        }
        self::$pending = true;
        // Fire once, after the response is sent, so saving in the admin stays fast.
        app()->terminating(function () use ($url) {
            self::$pending = false;
            try {
                Http::timeout(3)->withHeaders(['x-revalidate-secret' => (string) config('services.frontend.revalidate_secret')])->post($url);
            } catch (\Throwable $e) {
                Log::warning('Frontend revalidation failed: '.$e->getMessage());
            }
        });
    }
}
