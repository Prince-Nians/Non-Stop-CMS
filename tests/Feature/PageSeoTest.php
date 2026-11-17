<?php

namespace Tests\Feature;

use App\Filament\Resources\PageSeos\Pages\ManageHomeSeo;
use App\Models\PageSeo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PageSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_add_extra_tags_and_invalid_json_ld_is_rejected(): void
    {
        $this->actingAs(User::create(['name' => 'Admin', 'email' => 'a@example.com', 'password' => 'secret-pass-123']));

        Livewire::test(ManageHomeSeo::class)
            ->fillForm([
                'meta_title' => 'Home',
                'custom_meta' => [['attr' => 'name', 'name' => 'google-site-verification', 'content' => 'abc123']],
                'json_ld' => '{ not json',
            ])
            ->call('save')
            ->assertHasFormErrors(['json_ld']);

        Livewire::test(ManageHomeSeo::class)
            ->fillForm([
                'meta_title' => 'Home',
                'custom_meta' => [['attr' => 'name', 'name' => 'google-site-verification', 'content' => 'abc123']],
                'json_ld' => '{"@context":"https://schema.org","@type":"Organization","name":"X"}',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $seo = PageSeo::where('page', 'home')->first();
        $this->assertSame('abc123', $seo->custom_meta[0]['content']);
        $this->assertStringContainsString('Organization', $seo->json_ld);
    }
}
