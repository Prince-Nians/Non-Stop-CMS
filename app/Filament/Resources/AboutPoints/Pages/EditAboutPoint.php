<?php

namespace App\Filament\Resources\AboutPoints\Pages;

use App\Filament\Resources\AboutPoints\AboutPointResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAboutPoint extends EditRecord
{
    protected static string $resource = AboutPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
