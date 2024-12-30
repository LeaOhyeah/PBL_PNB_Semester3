<?php

namespace App\Filament\Resources\MyNewsResource\Pages;

use App\Filament\Resources\MyNewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMyNews extends CreateRecord
{
    protected static string $resource = MyNewsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id(); // Mengisi user_id dengan ID pengguna yang sedang login
        // dd($data);
        return $data;
    }
}
