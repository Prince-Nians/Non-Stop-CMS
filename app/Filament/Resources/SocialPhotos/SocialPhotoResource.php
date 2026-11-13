<?php

namespace App\Filament\Resources\SocialPhotos;

use App\Filament\Resources\SocialPhotos\Pages\CreateSocialPhoto;
use App\Filament\Resources\SocialPhotos\Pages\EditSocialPhoto;
use App\Filament\Resources\SocialPhotos\Pages\ListSocialPhotos;
use App\Models\SocialPhoto;
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

class SocialPhotoResource extends Resource
{
    protected static ?string $model = SocialPhoto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Homepage';

    protected static ?string $modelLabel = 'Social photo';

    protected static ?string $pluralModelLabel = 'Social photos';

    protected static ?string $recordTitleAttribute = 'alt';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                FileUpload::make('image')->image()->disk('public')->directory('site')->visibility('public')->imageEditor()->required(),
                TextInput::make('alt')->label('Alt text'),
                TextInput::make('url')->label('Link (optional)'),
                TextInput::make('sort_order')->numeric()->default(0)->helperText('Lower numbers appear first.'),
                Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('public'),
                TextColumn::make('alt'),
                TextColumn::make('sort_order')->sortable(),
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
            'index' => ListSocialPhotos::route('/'),
            'create' => CreateSocialPhoto::route('/create'),
            'edit' => EditSocialPhoto::route('/{record}/edit'),
        ];
    }
}
