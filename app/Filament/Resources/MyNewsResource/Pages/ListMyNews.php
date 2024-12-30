<?php

namespace App\Filament\Resources\MyNewsResource\Pages;

use App\Filament\Resources\MyNewsResource;
use App\Models\MyNews;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListMyNews extends ListRecords
{
    protected static string $resource = MyNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->label(app()->getLocale() === 'id' ? 'Semua' : 'All'),
            'verified' => Tab::make()
                ->label(app()->getLocale() === 'id' ? 'Terverifikasi' : 'Verified')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verified_at', '!=', null))
                ->badge(MyNews::query()->where('verified_at', '!=', null)->count()),
            'unverified' => Tab::make()
                ->label(app()->getLocale() === 'id' ? 'Belum di Verifikasi' : 'Unverified')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verified_at', null))
                ->badge(MyNews::query()->where('verified_at', null)->count()),
        ];
    }
}
