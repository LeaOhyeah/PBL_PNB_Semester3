<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;


class NewsChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }


    public function getHeading(): string | Htmlable | null
    {
        // Pastikan nilai heading dinamis berdasarkan bahasa lokal
        return static::$heading ?? (app()->getLocale() === 'id' ? 'Analitik Tahun Ini' : 'Analytics This Year');
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'height' => 300,
        ];
    }



    protected function getData(): array
    {
        // Ambil pengguna yang sedang login
        $user = auth()->user();

        // Cek apakah pengguna adalah admin atau super_admin
        $isAdmin = $user->hasRole(['admin', 'super_admin']);

        // Tentukan model yang digunakan berdasarkan peran pengguna
        $model = $isAdmin ? \App\Models\News::query() : \App\Models\MyNews::query();

        // Tahun saat ini
        $currentYear = now()->year;

        // Pilihan bahasa
        $language = app()->getLocale();

        // Ambil data postingan dibuat (per bulan)
        $postsCreated = $model
            ->selectRaw('EXTRACT(MONTH FROM created_at) AS month, COUNT(*) AS total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Ambil data postingan diverifikasi (per bulan)
        $postsVerified = $model
            ->selectRaw('EXTRACT(MONTH FROM verified_at) AS month, COUNT(*) AS total')
            ->whereYear('verified_at', $currentYear)
            ->whereNotNull('verified_at')
            ->groupBy('month', 'verified_at') // Tambahkan verified_at di sini
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();


        // Siapkan data lengkap (0 jika data tidak ada)
        $createdData = [];
        $verifiedData = [];

        for ($month = 1; $month <= 12; $month++) {
            $createdData[] = $postsCreated[$month] ?? 0;
            $verifiedData[] = $postsVerified[$month] ?? 0;
        }

        // Tentukan label berdasarkan bahasa
        $createdLabel = $language === 'id' ? 'Postingan dibuat' : 'Posts created';
        $verifiedLabel = $language === 'id' ? 'Postingan diverifikasi' : 'Posts verified';
        $monthsLabels = $language === 'id'
            ? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
            : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        return [
            'datasets' => [
                [
                    'label' => $createdLabel,
                    'data' => $createdData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.4)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'fill' => false,
                ],
                [
                    'label' => $verifiedLabel,
                    'data' => $verifiedData,
                    'backgroundColor' => 'rgba(153, 102, 255, 0.4)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'fill' => false,
                ],
            ],
            'labels' => $monthsLabels,
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
