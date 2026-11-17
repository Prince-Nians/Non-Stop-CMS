<?php

namespace App\Filament\Resources\PageSeos;

use App\Filament\Resources\PageSeos\Pages\ManageContactSeo;
use UnitEnum;

class ContactSeoResource extends PageSeoResource
{
    protected static string $page = 'contact';

    protected static string|UnitEnum|null $navigationGroup = 'Contact Us';

    protected static ?string $slug = 'contact-seo';

    public static function getPages(): array
    {
        return ['index' => ManageContactSeo::route('/')];
    }
}
