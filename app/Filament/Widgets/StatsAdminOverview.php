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
        $labels = [
            'thisMonth' => $language === 'id' ? 'Total Bulan Ini' : 'Total This Month',
            'lastMonth' => $language === 'id' ? 'Bulan Lalu' : 'Last Month',
            'monthlyGrowth' => $language === 'id' ? 'Kenaikan Bulanan' : 'Monthly Growth',
            'thisWeek' => $language === 'id' ? 'Total Minggu Ini' : 'Total This Week',
            'lastWeek' => $language === 'id' ? 'Minggu Lalu' : 'Last Week',
            'weeklyGrowth' => $language === 'id' ? 'Kenaikan Mingguan' : 'Weekly Growth',
        ];

        $descriptions = [
            'thisMonth' => $language === 'id' ? 'Jumlah berita diterbitkan bulan ini' : 'Number of news published this month',
            'lastMonth' => $language === 'id' ? 'Jumlah berita diterbitkan bulan lalu' : 'Number of news published last month',
            'monthlyGrowth' => $language === 'id' ? 'Persentase kenaikan dari bulan lalu' : 'Percentage growth from last month',
            'thisWeek' => $language === 'id' ? 'Jumlah berita diterbitkan minggu ini' : 'Number of news published this week',
            'lastWeek' => $language === 'id' ? 'Jumlah berita diterbitkan minggu lalu' : 'Number of news published last week',
            'weeklyGrowth' => $language === 'id' ? 'Persentase kenaikan dari minggu lalu' : 'Percentage growth from last week',
        ];

        return [
            Stat::make($labels['thisMonth'], $newsThisMonth)
                ->description($descriptions['thisMonth'])
                ->color('success'),
            Stat::make($labels['lastMonth'], $newsLastMonth)
                ->description($descriptions['lastMonth'])
                ->color('secondary'),
            Stat::make($labels['monthlyGrowth'], $monthlyGrowthPercentage !== null ? number_format($monthlyGrowthPercentage, 2) . '%' : 'Tidak tersedia')
                ->description($descriptions['monthlyGrowth'])
                ->color($monthlyGrowthPercentage > 0 ? 'success' : 'danger'),
            Stat::make($labels['thisWeek'], $newsThisWeek)
                ->description($descriptions['thisWeek'])
                ->color('success'),
            Stat::make($labels['lastWeek'], $newsLastWeek)
                ->description($descriptions['lastWeek'])
                ->color('secondary'),
            Stat::make($labels['weeklyGrowth'], $weeklyGrowthPercentage !== null ? number_format($weeklyGrowthPercentage, 2) . '%' : 'Tidak tersedia')
                ->description($descriptions['weeklyGrowth'])
                ->color($weeklyGrowthPercentage > 0 ? 'success' : 'danger'),
        ];
    }
}
