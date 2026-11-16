<?php

namespace App\Filament\Resources\SiteAssets;

use App\Filament\Resources\SiteAssets\Pages\EditSiteAsset;
use App\Filament\Resources\SiteAssets\Pages\ListSiteAssets;
use App\Models\SiteAsset;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** The fixed set of decorative images (hero cans, checker strips, shadows). Editors replace the picture only. */
class SiteAssetResource extends Resource
{
    protected static ?string $model = SiteAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static ?string $modelLabel = 'Decorative image';

    protected static ?string $pluralModelLabel = 'Decorative images';

    protected static ?string $recordTitleAttribute = 'label';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')->disabled()->dehydrated(false),
            TextInput::make('used_in')->label('Where it appears')->disabled()->dehydrated(false),
            FileUpload::make('image')->disk('public')->directory('site')->visibility('public')->required()
                ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/webp', 'image/jpeg'])
                ->imageEditor()->maxSize(5120)
                ->helperText('Cans: a transparent PNG cropped tightly to the can (it is stretched to the same box as the current one, so keep a similar tall shape). Checker strips: an SVG or PNG about 1500 px wide.'),
            TextInput::make('alt')->label('Description (alt text)'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('public')->height(48),
                TextColumn::make('label')->searchable(),
                TextColumn::make('used_in')->label('Where it appears'),
            ])
            ->recordActions([EditAction::make()])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteAssets::route('/'),
            'edit' => EditSiteAsset::route('/{record}/edit'),
        ];
    }
}
