<?php

namespace App\Filament\Resources\AboutPoints;

use App\Filament\Resources\AboutPoints\Pages\CreateAboutPoint;
use App\Filament\Resources\AboutPoints\Pages\EditAboutPoint;
use App\Filament\Resources\AboutPoints\Pages\ListAboutPoints;
use App\Models\AboutPoint;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;;

class AboutPointResource extends Resource
{
    protected static ?string $model = AboutPoint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'Homepage';

    protected static ?string $modelLabel = 'About point';

    protected static ?string $pluralModelLabel = 'About points';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                TextInput::make('title')->required(),
                Textarea::make('body')->rows(3),
                TextInput::make('sort_order')->numeric()->default(0)->helperText('Lower numbers appear first.'),
                Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->sortable(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('body')->limit(50),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([

            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAboutPoints::route('/'),
            'create' => CreateAboutPoint::route('/create'),
            'edit' => EditAboutPoint::route('/{record}/edit'),
        ];
    }
}
