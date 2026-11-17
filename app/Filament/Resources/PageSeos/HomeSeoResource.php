<?php

namespace App\Filament\Resources\PageSeos;

use App\Filament\Resources\PageSeos\Pages\ManageHomeSeo;
use UnitEnum;

class HomeSeoResource extends PageSeoResource
{
    protected static string $page = 'home';

    protected static string|UnitEnum|null $navigationGroup = 'Homepage';

    protected static ?string $slug = 'homepage-seo';

    public static function getPages(): array
    {
        return ['index' => ManageHomeSeo::route('/')];
    }
}
