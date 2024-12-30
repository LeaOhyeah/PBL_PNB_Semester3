<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\News;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

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
                ->icon('heroicon-o-check-badge')
                ->label(app()->getLocale() === 'id' ? 'Terverifikasi' : 'Verified')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verified_at', '!=', null))
                ->badge(News::query()->where('verified_at', '!=', null)->count()),
            'unverified' => Tab::make()
                ->icon('heroicon-o-no-symbol')
                ->label(app()->getLocale() === 'id' ? 'Belum di Verifikasi' : 'Unverified')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verified_at', null))
                ->badge(News::query()->where('verified_at', null)->count()),
            'needs_reverification' => Tab::make()
                ->icon('heroicon-o-question-mark-circle')
                ->label(app()->getLocale() === 'id' ? 'Butuh Verifikasi Ulang' : 'Needs Reverification')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('verified_at')
                    ->whereColumn('updated_at', '>', 'verified_at'))
                ->badge(News::whereNotNull('verified_at')
                ->whereColumn('updated_at', '>', 'verified_at')->count()),
        ];
    }
}
