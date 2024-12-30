<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent;

class BerandaController extends Controller
{

    public function test()
    {

        $agent = new Agent();
        $perPage = $agent->isMobile() ? 10 : 18;

        $news = News::paginate($perPage);



        // dd($news);
        return view('welcome', compact('news'));
    }

    public function testDetail($id)
    {
        $data = [
            'id' => $id
        ];
        return view('single', $data);
    }

    public function index()
    {
        // Ambil keyword dari cookie
        $keywords = json_decode(Cookie::get('search_keywords', '[]'), true);

        $data['keywords'] = $keywords;

        // Jika ada keyword, cari berita berdasarkan keyword tersebut
        if (!empty($keywords)) {
            $data['fyp'] = News::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('content', 'like', "%{$keyword}%");
                }
            })->limit(10)->get();
        } else {
            // Jika tidak ada keyword, tampilkan berita terbaru
            $data['fyp'] = News::latest()->limit(10)->get();
        }

        $data['news'] =  News::latest()->get();

        return view('beranda', $data);
    }

    public function search(Request $request)
    {
        // Tangkap keyword dari form pencarian
        $keyword = $request->input('q');

        // Simpan keyword di cookie
        $this->storeSearchKeyword($keyword);

        // Lakukan pencarian di database
        $news = News::where('content', 'like', "%{$keyword}%")
            ->get();

        return view('pencarian  ', ['news' => $news, 'keyword' => $keyword]);
    }

    function storeSearchKeyword($keyword)
    {
        // Ambil data keyword dari cookie atau buat array kosong jika belum ada
        $keywords = json_decode(Cookie::get('search_keywords', '[]'), true);
        $minutes = 60 * 24 * 365 * 20;

        // Tambahkan keyword baru di depan
        array_unshift($keywords, $keyword);

        // Batasi jumlah keyword yang disimpan (misalnya, maksimal 10)
        $keywords = array_slice($keywords, 0, 10);

        // Simpan kembali ke cookie (berlaku selama 7 hari)
        Cookie::queue('search_keywords', json_encode($keywords), $minutes);
    }
}
