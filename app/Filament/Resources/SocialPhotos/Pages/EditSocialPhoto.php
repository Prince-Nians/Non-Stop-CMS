<?php

namespace App\Filament\Resources\SocialPhotos\Pages;

use App\Filament\Resources\SocialPhotos\SocialPhotoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSocialPhoto extends EditRecord
{
    protected static string $resource = SocialPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
