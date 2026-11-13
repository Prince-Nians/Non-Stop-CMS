<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\EditRecord;

class ManageSiteSettings extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    public function mount(int|string|null $record = null): void
    {
        $this->record = SiteSetting::firstOrCreate(['id' => 1]);
        $this->authorizeAccess();
        $this->fillForm();
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
