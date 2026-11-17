<?php

namespace App\Filament\Resources\ContactSections;

use App\Filament\Resources\ContactSections\Pages\EditContactSection;
use App\Filament\Resources\ContactSections\Pages\ListContactSections;
use App\Filament\Resources\PageSections\PageSectionResource;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** The "Contact Us" tab: every editable text/image block of the Contact Us page (the website's /contact). */
class ContactSectionResource extends PageSectionResource
{
    /** Plain names for the fixed section keys, so editors never see technical identifiers. */
    public const NAMES = [
        'contact_hero' => 'Top banner (photo + heading)',
        'contact_info' => 'Contact information (left side)',
        'contact_form' => 'Contact form (labels & messages)',
        'faq' => 'FAQ heading',
        'marquee' => 'Scrolling strip',
    ];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Contact Us';

    protected static ?string $navigationLabel = 'Page content';

    protected static ?string $slug = 'contact-page-content';

    protected static ?int $navigationSort = 1;

    protected static string $page = 'contact';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Block')->formatStateUsing(fn (string $state) => self::NAMES[$state] ?? $state)->sortable(),
                TextColumn::make('title')->searchable(),
                ImageColumn::make('image')->disk('public'),
                IconColumn::make('is_active')->boolean()->label('Shown'),
            ])
            ->recordActions([EditAction::make()])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactSections::route('/'),
            'edit' => EditContactSection::route('/{record}/edit'),
        ];
    }
}
