<?php

namespace App\Filament\Resources\ContactSections\Pages;

use App\Filament\Resources\ContactSections\ContactSectionResource;
use App\Filament\Resources\PageSections\Pages\ListPageSections;

class ListContactSections extends ListPageSections
{
    protected static string $resource = ContactSectionResource::class;
}
