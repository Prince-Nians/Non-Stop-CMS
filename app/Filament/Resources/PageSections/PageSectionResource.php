<?php

namespace App\Filament\Resources\PageSections;

use App\Filament\Resources\PageSections\Pages\CreatePageSection;
use App\Filament\Resources\PageSections\Pages\EditPageSection;
use App\Filament\Resources\PageSections\Pages\ListPageSections;
use App\Models\PageSection;
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

class PageSectionResource extends Resource
{
    protected static ?string $model = PageSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Homepage';

    protected static ?string $modelLabel = 'Section';

    protected static ?string $pluralModelLabel = 'Sections';

    protected static ?string $recordTitleAttribute = 'title';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                TextInput::make('key')->disabled()->dehydrated(false)->helperText('Fixed identifier used by the website.'),
                TextInput::make('title'),
                TextInput::make('subtitle'),
                Textarea::make('description')->rows(5)->columnSpanFull(),
                TextInput::make('button_label'),
                TextInput::make('button_url'),
                FileUpload::make('image')->image()->disk('public')->directory('site')->visibility('public')->imageEditor(),
                FileUpload::make('image_secondary')->image()->disk('public')->directory('site')->visibility('public')->imageEditor(),
                KeyValue::make('extra')->label('Extra text fields')->columnSpanFull(),
                Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->badge()->sortable(),
                TextColumn::make('title')->searchable(),
                ImageColumn::make('image')->disk('public'),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([

            ])
            ->recordActions([EditAction::make()])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageSections::route('/'),
            'edit' => EditPageSection::route('/{record}/edit'),
        ];
    }
}
