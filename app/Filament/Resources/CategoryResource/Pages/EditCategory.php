<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /** Colonne (relative à public/) -> champ FileUpload (relatif à public/uploads). Les images legacy ne sont pas chargées dans le champ. */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image'] = Category::imageToUpload($data['image'] ?? null);

        return $data;
    }

    /** Sans nouvel upload, une image legacy (« images/… ») ou une URL est conservée. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $new = Category::imageFromUpload($data['image'] ?? null);
        $current = Category::normalizeImagePath($this->record->image);
        $isUpload = $current !== null && str_starts_with($current, 'uploads/');

        $data['image'] = $new ?? ($isUpload ? null : $current);

        return $data;
    }
}
