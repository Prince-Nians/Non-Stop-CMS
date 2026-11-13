<?php

namespace App\Filament\Resources\AboutPoints\Pages;

use App\Filament\Resources\AboutPoints\AboutPointResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAboutPoints extends ListRecords
{
    protected static string $resource = AboutPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
