<?php

namespace App\Filament\Resources\PageSeos\Pages;

use App\Models\PageSeo;
use Filament\Resources\Pages\EditRecord;

/** Edits the single SEO row of one page (no list, no create). */
abstract class ManagePageSeo extends EditRecord
{
    public function mount(int|string|null $record = null): void
    {
        $this->record = PageSeo::firstOrCreate(['page' => static::getResource()::pageKey()]);
        $this->authorizeAccess();
        $this->fillForm();
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
