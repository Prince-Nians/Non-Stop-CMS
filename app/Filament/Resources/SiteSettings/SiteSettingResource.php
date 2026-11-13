<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static ?string $modelLabel = 'Site settings';

    protected static ?string $pluralModelLabel = 'Site settings';

    protected static ?int $navigationSort = -1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Brand')->columns(2)->schema([
                TextInput::make('site_name')->required(),
                FileUpload::make('logo')->image()->disk('public')->directory('site')->visibility('public'),
            ]),
            Section::make('Contact details')->columns(2)->schema([
                TextInput::make('phone')->tel(),
                TextInput::make('email')->email(),
                TextInput::make('copyright_text')->columnSpanFull(),
            ]),
            Section::make('Social media')->columns(2)->schema([
                TextInput::make('facebook_url')->label('Facebook')->url(),
                TextInput::make('youtube_url')->label('YouTube')->url(),
                TextInput::make('linkedin_url')->label('LinkedIn')->url(),
                TextInput::make('twitter_url')->label('Twitter / X')->url(),
            ]),
            Section::make('Form notifications')->description('Where new enquiries are emailed.')->columns(2)->schema([
                TextInput::make('contact_notify_email')->label('Contact form')->email(),
                TextInput::make('partner_notify_email')->label('Partner form')->email(),
            ]),
            Section::make('SEO')->columns(1)->schema([
                TextInput::make('seo_title'),
                Textarea::make('seo_description')->rows(2),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSiteSettings::route('/')];
    }
}
