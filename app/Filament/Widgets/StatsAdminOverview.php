<?php

namespace App\Filament\Widgets;

use App\Models\MyNews;
use App\Models\News;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Ambil pengguna yang sedang login
        $user = auth()->user();

        // Cek apakah pengguna adalah admin atau super_admin
        $isAdmin = $user->hasRole(['admin', 'super_admin']);

        // $dataModel = $isAdmin ? ;

        // Ambil data bulan dan minggu sekarang
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $currentWeekStart = now()->startOfWeek();
        $currentWeekEnd = now()->endOfWeek();

        // Data bulan lalu
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        // Data minggu lalu
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();

        if ($isAdmin) {
            // Query data bulan ini
            $newsThisMonth = News::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();

            // Query data bulan lalu
            $newsLastMonth = News::whereYear('created_at', $lastYear)
                ->whereMonth('created_at', $lastMonth)
                ->count();

            // Query data minggu ini
            $newsThisWeek = News::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
                ->count();

            // Query data minggu lalu
            $newsLastWeek = News::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                ->count();

            // Hitung kenaikan/persen bulanan
            $monthlyGrowthPercentage = $newsLastMonth > 0
                ? (($newsThisMonth - $newsLastMonth) / $newsLastMonth) * 100
                : null;

            // Hitung kenaikan/persen mingguan
            $weeklyGrowthPercentage = $newsLastWeek > 0
                ? (($newsThisWeek - $newsLastWeek) / $newsLastWeek) * 100
                : null;

            // Total semua berita
            $totalNews = News::count();
        } else {
            // Query data bulan ini
            $newsThisMonth = MyNews::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();

            // Query data bulan lalu
            $newsLastMonth = MyNews::whereYear('created_at', $lastYear)
                ->whereMonth('created_at', $lastMonth)
                ->count();

            // Query data minggu ini
            $newsThisWeek = MyNews::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
                ->count();

            // Query data minggu lalu
            $newsLastWeek = MyNews::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                ->count();

            // Hitung kenaikan/persen bulanan
            $monthlyGrowthPercentage = $newsLastMonth > 0
                ? (($newsThisMonth - $newsLastMonth) / $newsLastMonth) * 100
                : null;

            // Hitung kenaikan/persen mingguan
            $weeklyGrowthPercentage = $newsLastWeek > 0
                ? (($newsThisWeek - $newsLastWeek) / $newsLastWeek) * 100
                : null;

            // Total semua berita
            $totalNews = MyNews::count();
        }

        $language = app()->getLocale();

         // Tentukan label dan deskripsi berdasarkan bahasa
         $thisMonthLabel = $language === 'id' ? 'Jumlah berita diterbitkan bulan ini' : 'Number of news published this month';
         $lastMonthLabel = $language === 'id' ? 'Jumlah berita diterbitkan bulan lalu' : 'Number of news published last month';
         $monthlyGrowthLabel = $language === 'id' ? 'Persentase kenaikan dari bulan lalu' : 'Percentage growth from last month';
         $thisWeekLabel = $language === 'id' ? 'Jumlah berita diterbitkan minggu ini' : 'Number of news published this week';
         $lastWeekLabel = $language === 'id' ? 'Jumlah berita diterbitkan minggu lalu' : 'Number of news published last week';
         $weeklyGrowthLabel = $language === 'id' ? 'Persentase kenaikan dari minggu lalu' : 'Percentage growth from last week';

        return [

            Stat::make('Total Bulan Ini', $newsThisMonth)
                ->description($thisMonthLabel)
                ->color('success'),

            Stat::make('Bulan Lalu', $newsLastMonth)
                ->description($lastMonthLabel)
                ->color('secondary'),

            Stat::make('Kenaikan Bulanan', $monthlyGrowthPercentage !== null ? number_format($monthlyGrowthPercentage, 2) . '%' : 'Tidak tersedia')
                ->description($monthlyGrowthLabel)
                ->color($monthlyGrowthPercentage > 0 ? 'success' : 'danger'),

            Stat::make('Total Minggu Ini', $newsThisWeek)
                ->description($thisWeekLabel)
                ->color('success'),

            Stat::make('Minggu Lalu', $newsLastWeek)
                ->description($lastWeekLabel)
                ->color('secondary'),

            Stat::make('Kenaikan Mingguan', $weeklyGrowthPercentage !== null ? number_format($weeklyGrowthPercentage, 2) . '%' : 'Tidak tersedia')
                ->description($weeklyGrowthLabel)
                ->color($weeklyGrowthPercentage > 0 ? 'success' : 'danger'),

        ];
    }
}
