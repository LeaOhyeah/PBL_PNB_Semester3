<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class TampunganIde extends Controller
{
    public function index()
    {
        $keywords = json_decode(Cookie::get('search_keywords', '[]'), true);
        $interactions = json_decode(Cookie::get('news_interactions', '[]'), true);
        $categories = json_decode(Cookie::get('category_interactions', '[]'), true);

        $data['keywords'] = $keywords;

        // // Kombinasikan data pencarian, interaksi berita, dan kategori
        // $data['fyp'] = News::where(function ($query) use ($keywords, $interactions, $categories) {
        //     foreach ($keywords as $keyword) {
        //         $query->orWhere('content', 'like', "%{$keyword}%");
        //     }

        //     foreach ($interactions as $newsId) {
        //         $query->orWhere('id', $newsId);
        //     }

        //     foreach ($categories as $categoryId => $count) {
        //         // Berikan bobot lebih tinggi pada kategori yang sering dibuka
        //         $query->orWhere('category_id', $categoryId);
        //     }
        // })->orderByRaw('FIELD(id, ' . implode(',', $interactions) . ') DESC')  // Berikan prioritas pada berita yang pernah dilihat
        //     ->limit(10)->get();

        // Berikan rekomendasi berita, tapi jangan tampilkan yang sudah dibaca
        $data['fyp'] = News::where(function ($query) use ($keywords, $categories) {
            foreach ($keywords as $keyword) {
                $query->orWhere('content', 'like', "%{$keyword}%");
            }

            foreach ($categories as $categoryId => $count) {
                // Tambahkan filter berdasarkan kategori yang sering dilihat
                $query->orWhere('category_id', $categoryId);
            }
        })->whereNotIn('id', $interactions)  // Jangan tampilkan berita yang sudah dibaca
            ->limit(10)->get();

        $data['news'] = News::latest()->get();

        return view('beranda', $data);
    }

    public function show($id)
    {
        // Cari berita berdasarkan ID
        $news = News::findOrFail($id);

        // Simpan interaksi pengguna
        $this->storeNewsInteraction($id);

        // Kembalikan tampilan detail berita
        return view('news.show', ['news' => $news]);
    }

    function storeNewsInteraction($newsId)
    {
        // Ambil data interaksi dari cookie atau buat array kosong jika belum ada
        $interactions = json_decode(Cookie::get('news_interactions', '[]'), true);
        $minutes = 60 * 24 * 365 * 20;

        // Tambahkan ID berita yang baru dibaca di depan
        array_unshift($interactions, $newsId);

        // Batasi jumlah berita yang disimpan (misalnya, maksimal 20)
        $interactions = array_slice($interactions, 0, 20);

        // Simpan kembali ke cookie (berlaku selama 20 tahun)
        Cookie::queue('news_interactions', json_encode($interactions), $minutes);
    }

    function storeCategoryInteraction($categoryId)
    {
        // Ambil data interaksi kategori dari cookie atau buat array kosong jika belum ada
        $categories = json_decode(Cookie::get('category_interactions', '[]'), true);
        $minutes = 60 * 24 * 365 * 20;

        // Tambahkan kategori baru di depan atau tambahkan skor untuk kategori yang sudah ada
        if (!isset($categories[$categoryId])) {
            $categories[$categoryId] = 1;
        } else {
            $categories[$categoryId] += 1;
        }

        // Simpan kembali ke cookie (berlaku selama 20 tahun)
        Cookie::queue('category_interactions', json_encode($categories), $minutes);
    }

    // Di controller saat user membuka berita:
    public function show2($id)
    {
        $news = News::findOrFail($id);

        // Simpan interaksi kategori
        $this->storeCategoryInteraction($news->category_id);

        return view('news.show', ['news' => $news]);
    }

    public function index2()
    {
        // Ambil keyword pencarian dari cookie
        $keywords = json_decode(Cookie::get('search_keywords', '[]'), true);

        // Ambil ID berita yang sudah dibaca (riwayat interaksi)
        $interactions = json_decode(Cookie::get('news_interactions', '[]'), true);

        // Ambil kategori yang sering dibaca
        $categories = json_decode(Cookie::get('category_interactions', '[]'), true);

        $data['keywords'] = $keywords;

        // Jika ada riwayat pencarian atau interaksi kategori, lakukan query personalisasi
        if (!empty($keywords) || !empty($categories)) {
            $data['fyp'] = News::where(function ($query) use ($keywords, $categories) {
                // Filter berdasarkan kata kunci pencarian
                foreach ($keywords as $keyword) {
                    $query->orWhere('content', 'like', "%{$keyword}%");
                }

                // Filter berdasarkan kategori yang sering dibaca
                foreach ($categories as $categoryId => $count) {
                    $query->orWhere('category_id', $categoryId);
                }
            })
                ->whereNotIn('id', $interactions)  // Jangan tampilkan berita yang sudah dibaca
                ->limit(10)
                ->get();
        } else {
            // Jika tidak ada data personalisasi, tampilkan berita terbaru
            $data['fyp'] = News::latest()
                ->whereNotIn('id', $interactions)  // Jangan tampilkan berita yang sudah dibaca
                ->limit(10)
                ->get();
        }

        // Tampilkan berita terbaru untuk bagian utama (tanpa filter FYP)
        $data['news'] = News::latest()->get();

        return view('beranda', $data);
    }

    
    public function search(Request $request)
    {
        $limit = $request->input('limit') ?? 25;
        $page = $request->input('page') ?? 1;
        $search = $request->input('search');

        // Validasi input
        if ((!is_numeric($limit) || $limit < 1) || (!is_numeric($page) || $page < 1)) {
            return response()->json([
                "version" => env('APP_VERSION'),
                "status" => "error",
                "code" => 400,
                "message" => "Invalid input parameter",
                "errors" => [
                    "The limit, limit, and page parameters must be positive integers."
                ]
            ], 400);
        }

        // Query berita
        $newsQuery = News::with(['category', 'user', 'tags'])
            ->where('verified_at', '!=', null)
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan parameter pencarian
        if ($search) {
            $newsQuery->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%")
                    ->orWhereHas('tags', function ($tagQuery) use ($search) {
                        $tagQuery->where('name', 'LIKE', "%$search%");
                    });
            });
        }

        // Paginasi atau batasan jumlah berita
        $newsList = $newsQuery->paginate($limit, ['*'], 'page', $page);

        // Format respons
        $response = [
            "version" => env('APP_VERSION'),
            "status" => "success",
            "code" => 200,
            "message" => "News fetched successfully",
            "page" => $newsList->currentPage(),
            "total" => $newsList->lastPage(),
            "next_url" => $newsList->currentPage() < $newsList->lastPage()
                ? env('APP_URL') . "/api/news/search?search=" . urlencode($search) . "&limit=" . $limit . "&page=" . ($newsList->currentPage() + 1)
                : "",
            "data" => [
                "list" => $newsList->values()->map(fn($news) => $this->formatNewsData($news))
            ]
        ];

        return response()->json($response, 200);
    }

    
}
