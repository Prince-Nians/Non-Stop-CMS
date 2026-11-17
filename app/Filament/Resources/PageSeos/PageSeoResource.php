<?php

namespace App\Filament\Resources\PageSeos;

use App\Models\PageSeo;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

/**
 * "SEO" tab of a website page: the title, description and social-sharing tags that the Next.js site writes into that page's <head>.
 * One concrete subclass per page (see HomeSeoResource, ContactSeoResource); each edits exactly one row.
 */
abstract class PageSeoResource extends Resource
{
    protected static ?string $model = PageSeo::class;

    /** Which website page this tab edits. */
    protected static string $page;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $navigationLabel = 'SEO & meta tags';

    protected static ?string $modelLabel = 'SEO';

    protected static ?string $pluralModelLabel = 'SEO & meta tags';

    protected static ?int $navigationSort = 99;

    public static function pageKey(): string
    {
        return static::$page;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('page', static::$page);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        $count = fn (int $limit) => fn (?string $state) => mb_strlen((string) $state).' / '.$limit.' characters';

        return $schema->components([
            Section::make('Google search result')
                ->description('How this page looks in search results.')
                ->schema([
                    TextInput::make('meta_title')->label('Page title')->live(onBlur: true)->hint($count(60))
                        ->helperText('Aim for under 60 characters. Put the main keyword first.'),
                    Textarea::make('meta_description')->label('Page description')->rows(3)->live(onBlur: true)->hint($count(160))
                        ->helperText('Aim for 120–160 characters. A short, clear summary that makes people want to click.'),
                    TextInput::make('keywords')->helperText('Optional, comma separated (e.g. energy drink, non stop).'),
                ]),
            Section::make('Social sharing (WhatsApp, Facebook, X, LinkedIn)')
                ->description('Shown when someone shares this page. Leave empty to reuse the title and description above.')
                ->schema([
                    TextInput::make('og_title')->label('Share title'),
                    Textarea::make('og_description')->label('Share description')->rows(2),
                    FileUpload::make('og_image')->label('Share image')->image()->disk('public')->directory('seo')->imageEditor()
                        ->helperText('Best size 1200 × 630 pixels.'),
                ]),
            Section::make('Extra tags (your own)')
                ->description('Add any other <meta> tag for this page — e.g. Google/Bing site verification, og:locale, a theme colour, article tags.')
                ->schema([
                    Repeater::make('custom_meta')->label('Extra meta tags')->addActionLabel('Add a tag')->defaultItems(0)->reorderable()->collapsible()
                        ->itemLabel(fn (array $state): ?string => filled($state['name'] ?? null) ? ($state['name'].' = '.\Illuminate\Support\Str::limit((string) ($state['content'] ?? ''), 40)) : null)
                        ->columns(3)
                        ->schema([
                            Select::make('attr')->label('Type')->options(['name' => 'name (most tags)', 'property' => 'property (Open Graph, e.g. og:locale)', 'http-equiv' => 'http-equiv'])->default('name')->required(),
                            TextInput::make('name')->label('Tag name')->placeholder('google-site-verification')->required(),
                            TextInput::make('content')->label('Value')->required(),
                        ]),
                    Textarea::make('json_ld')->label('Structured data (JSON-LD)')->rows(8)->columnSpanFull()
                        ->placeholder('{ "@context": "https://schema.org", "@type": "FAQPage", ... }')
                        ->helperText('Optional. Paste JSON-LD for rich results (FAQ, product, local business…). Must be valid JSON — the website ignores it otherwise.')
                        ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                            if (filled($value) && json_decode($value) === null) {
                                $fail('This is not valid JSON. Check the brackets, quotes and commas.');
                            }
                        }),
                ]),
            Section::make('Advanced')->columns(2)->collapsed()->schema([
                TextInput::make('canonical_url')->label('Canonical URL')->url()
                    ->helperText('Only if this page is also available at another address. Leave empty normally.'),
                Toggle::make('no_index')->label('Hide this page from search engines')
                    ->helperText('Adds “noindex”. Keep off for live pages.'),
            ]),
        ]);
    }
}
