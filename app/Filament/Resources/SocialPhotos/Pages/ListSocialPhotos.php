<?php

namespace App\Filament\Resources\SocialPhotos\Pages;

use App\Filament\Resources\SocialPhotos\SocialPhotoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSocialPhotos extends ListRecords
{
    protected static string $resource = SocialPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
