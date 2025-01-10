<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class NewsChart extends ChartWidget
{
    protected static ?string $heading = 'Postingan Bulan Ini';

    protected function getData(): array
    {
        // Tahun saat ini
        $currentYear = now()->year;

        // Data postingan dibuat (per bulan)
        $postsCreated = DB::table('news')
            ->selectRaw('EXTRACT(MONTH FROM created_at) AS month, COUNT(*) AS total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Data postingan diverifikasi (per bulan)
        $postsVerified = DB::table('news')
            ->selectRaw('EXTRACT(MONTH FROM verified_at) AS month, COUNT(*) AS total')
            ->whereYear('verified_at', $currentYear)
            ->whereNotNull('verified_at')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Siapkan data lengkap (0 jika data tidak ada)
        $createdData = [];
        $verifiedData = [];

        for ($month = 1; $month <= 12; $month++) {
            $createdData[] = $postsCreated[$month] ?? 0; // Data postingan dibuat
            $verifiedData[] = $postsVerified[$month] ?? 0; // Data postingan diverifikasi
        }

        return [
            'datasets' => [
                [
                    'label' => 'Postingan dibuat',
                    'data' => $createdData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.4)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Postingan diverifikasi',
                    'data' => $verifiedData,
                    'backgroundColor' => 'rgba(153, 102, 255, 0.4)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'fill' => true,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Jenis grafik: line chart
    }
}
