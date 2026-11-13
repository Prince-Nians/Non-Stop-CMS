<?php

namespace App\Filament\Resources\NavLinks;

use App\Filament\Resources\NavLinks\Pages\CreateNavLink;
use App\Filament\Resources\NavLinks\Pages\EditNavLink;
use App\Filament\Resources\NavLinks\Pages\ListNavLinks;
use App\Models\NavLink;
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

class NavLinkResource extends Resource
{
    protected static ?string $model = NavLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static ?string $modelLabel = 'Menu link';

    protected static ?string $pluralModelLabel = 'Menu links';

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                Select::make('location')->options(['header' => 'Header menu', 'footer' => 'Footer quick links', 'legal' => 'Footer legal links'])->required(),
                TextInput::make('label')->required(),
                TextInput::make('url')->required()->helperText('Use #about style anchors or a full https:// URL.'),
                TextInput::make('sort_order')->numeric()->default(0)->helperText('Lower numbers appear first.'),
                Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('location')->badge()->sortable(),
                TextColumn::make('label')->searchable(),
                TextColumn::make('url'),
                TextColumn::make('sort_order')->sortable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([
                SelectFilter::make('location')->options(['header' => 'Header', 'footer' => 'Footer', 'legal' => 'Legal'])
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('location');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNavLinks::route('/'),
            'create' => CreateNavLink::route('/create'),
            'edit' => EditNavLink::route('/{record}/edit'),
        ];
    }
}
