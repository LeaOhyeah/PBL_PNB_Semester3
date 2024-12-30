<?php

namespace App\Filament\Resources\MyNewsResource\Pages;

use App\Filament\Resources\MyNewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyNews extends EditRecord
{
    protected static string $resource = MyNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
