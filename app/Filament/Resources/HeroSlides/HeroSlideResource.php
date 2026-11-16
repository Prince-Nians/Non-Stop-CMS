<?php

namespace App\Filament\Resources\HeroSlides;

use App\Filament\Resources\HeroSlides\Pages\CreateHeroSlide;
use App\Filament\Resources\HeroSlides\Pages\EditHeroSlide;
use App\Filament\Resources\HeroSlides\Pages\ListHeroSlides;
use App\Models\HeroSlide;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Homepage';

    protected static ?string $modelLabel = 'Banner slide';

    protected static ?string $pluralModelLabel = 'Banner slides';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->helperText('Internal name, also used for the slider dot label.'),
            Select::make('kind')
                ->options(['banner' => 'Banner image', 'stadium' => 'Stadium artwork (built in)'])
                ->default('banner')->required()
                ->disabled(fn (?HeroSlide $record) => filled($record))->dehydrated()
                ->helperText('The stadium artwork is built into the site; only its position and visibility can be changed.'),
            FileUpload::make('image')->image()->disk('public')->directory('site')->visibility('public')->imageEditor()
                ->visible(fn ($get) => $get('kind') !== 'stadium')
                ->required(fn ($get) => $get('kind') !== 'stadium')
                ->helperText('Wide banner, 1440 × 589 px or larger (2880 × 1178 is ideal for sharp screens). Keep the can and headline near the centre: on phones only the middle is shown.'),
            FileUpload::make('mobile_image')->image()->disk('public')->directory('site')->visibility('public')->imageEditor()
                ->visible(fn ($get) => $get('kind') !== 'stadium')
                ->helperText('Optional square or portrait version used on phones.'),
            TextInput::make('alt')->label('Image description (alt text)')
                ->visible(fn ($get) => $get('kind') !== 'stadium'),
            TextInput::make('url')->label('Link (optional)')->helperText('Where the banner goes when clicked.')
                ->visible(fn ($get) => $get('kind') !== 'stadium'),
            TextInput::make('sort_order')->numeric()->default(0)->helperText('Lower numbers appear first. The first slide is shown on load.'),
            Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('public')->label('Banner')->width(160)->height(65),
                TextColumn::make('name')->searchable(),
                TextColumn::make('kind')->badge(),
                TextColumn::make('sort_order')->sortable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHeroSlides::route('/'),
            'create' => CreateHeroSlide::route('/create'),
            'edit' => EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
