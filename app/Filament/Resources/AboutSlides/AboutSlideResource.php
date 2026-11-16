<?php

namespace App\Filament\Resources\AboutSlides;

use App\Filament\Resources\AboutSlides\Pages\CreateAboutSlide;
use App\Filament\Resources\AboutSlides\Pages\EditAboutSlide;
use App\Filament\Resources\AboutSlides\Pages\ListAboutSlides;
use App\Models\AboutSlide;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AboutSlideResource extends Resource
{
    protected static ?string $model = AboutSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static string|UnitEnum|null $navigationGroup = 'Homepage';

    protected static ?string $modelLabel = 'About slide';

    protected static ?string $pluralModelLabel = 'About slides';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->helperText('Shown on the slider dot, e.g. "Jungle".'),
            FileUpload::make('can_image')->image()->disk('public')->directory('site')->imageEditor()
                ->helperText('Transparent PNG of the can. The design shows it slightly tilted.'),
            ColorPicker::make('ribbon_color_a')->label('Ribbon colour (light)')->required(),
            ColorPicker::make('ribbon_color_b')->label('Ribbon colour (dark)')->required(),
            ColorPicker::make('title_color')->label('"About Us" heading colour')->required(),
            ColorPicker::make('number_color')->label('Point numbers colour (01–04)')->required(),
            ColorPicker::make('point_title_color')->label('Point titles colour')->required(),
            ColorPicker::make('button_color')->label('"Become a Partner" button colour')->required(),
            TextInput::make('sort_order')->numeric()->default(0)->helperText('Lower numbers appear first. The first slide is shown on load.'),
            Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('can_image')->disk('public')->label('Can'),
                TextColumn::make('name')->searchable(),
                ColorColumn::make('ribbon_color_a')->label('Ribbon'),
                ColorColumn::make('ribbon_color_b')->label('Ribbon 2'),
                ColorColumn::make('button_color')->label('Button'),
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
            'index' => ListAboutSlides::route('/'),
            'create' => CreateAboutSlide::route('/create'),
            'edit' => EditAboutSlide::route('/{record}/edit'),
        ];
    }
}
