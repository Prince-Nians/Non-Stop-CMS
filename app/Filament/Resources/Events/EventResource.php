<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Models\Event;
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

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $modelLabel = 'Event';

    protected static ?string $pluralModelLabel = 'Events';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(function ($state, $set, $get) { if (blank($get('slug'))) { $set('slug', Str::slug((string) $state)); } }),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->helperText('Used in URLs. Auto-filled from the title.'),
                Textarea::make('excerpt')->rows(3)->columnSpanFull(),
                RichEditor::make('body')->columnSpanFull(),
                FileUpload::make('image')->image()->disk('public')->directory('site')->visibility('public')->imageEditor(),
                DateTimePicker::make('starts_at'),
                TextInput::make('location'),
                TextInput::make('url')->label('Registration / details link'),
                DateTimePicker::make('published_at'),
                Toggle::make('is_published')->default(true),
                TextInput::make('sort_order')->numeric()->default(0)->helperText('Lower numbers appear first.'),
                TextInput::make('seo_title')->label('SEO title'),
                Textarea::make('seo_description')->label('SEO description')->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('public'),
                TextColumn::make('title')->searchable()->limit(50),
                TextColumn::make('starts_at')->dateTime()->sortable(),
                IconColumn::make('is_published')->boolean()->label('Published'),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Published')
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
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
