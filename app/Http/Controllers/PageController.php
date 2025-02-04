<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;


class PageController extends Controller
{
    public function index(Request $request)
    {
        // bagian berta terbaru
        // Hero terbaru (berita terbaru 1)
        $heroLatest = News::verified()
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Tiga berita terbaru tambahan
        $latestNews = News::verified()
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->where('id', '!=', $heroLatest->id)
            ->take(3)
            ->get();

        // Kumpulkan semua ID yang sudah digunakan
        $usedNewsIds = $latestNews->pluck('id')->toArray();
        $usedNewsIds[] = $heroLatest->id;

        // Lima kategori dan masing-masing satu berita selain yang sudah tampil
        $hero_categories = Category::with('news.user')->orderBy('created_at', 'desc')->limit(5)->get()->map(function ($category) use ($usedNewsIds) {
            $filteredNews = $category->news->where('verified_at', '!=', null)
                ->whereNotIn('id', $usedNewsIds)
                ->take(1);
            $category->setRelation('news', $filteredNews);
            return $category;
        });



        // bagian berita kategori dipilih
        // Ambil kategori yang dipilih dari cookie
        $userCategoryId = $request->cookie('userCategorySelected');

        if ($userCategoryId == 'skip' || $userCategoryId == null) {
            $userCategoryId = Category::first()->id;
        }

        // Ambil 5 berita berdasarkan kategori yang dipilih
        $newsFromCategory = News::with('category', 'user')
            ->where('category_id', $userCategoryId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $by_categories = Category::with('news.user')->orderBy('created_at', 'desc')->get()->map(function ($category) {
            $filteredNews = $category->news->where('verified_at', '!=', null)
                ->take(8);
            $category->setRelation('news', $filteredNews);
            return $category;
        });

        // dd($by_categories);
        $categories = Category::all();
        $tags = Tag::all();

        $data = [
            'hero_latest' => $heroLatest,
            'latest_news' => $latestNews,
            'hero_categories' => $hero_categories,
            'newsFromCategory' => $newsFromCategory,
            'by_categories' => $by_categories,
            'categories' => $categories,
            'tags' => $tags,
        ];

        // dd($data['categories']);
        return view('01_new_era.index', $data);
    }



    public function news($id)
    {
        return News::find($id);
    }



    public function filter(Request $request)
    {
        $query = News::with(['user', 'category'])->verified()->latest();
        $title = null;
        $infoText = null;

        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where('category_id', $category->id);
            $title = 'Berita dari Kategori ' . $category->name;

            $infoText = fn($news) =>
            '<a href="' . route('page.filter', ['user' => $news->user->id]) . '">
                <span class="color1">' . $news->user->name . '</span>
            </a>';
        }

        if ($request->has('tag')) {
            $tag = Tag::where('name', $request->tag)->firstOrFail();
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            });
            $title = 'Berita dengan Tag ' . $tag->name;

            $infoText = fn($news) =>
            '<a href="' . route('page.filter', ['user' => $news->user->id]) . '">
                <span class="color1">' . $news->user->name . '</span>
            </a>';
        }

        if ($request->has('user')) {
            $user = User::where('id', $request->user)->firstOrFail();
            $query->where('user_id', $user->id);
            $title = 'Berita oleh ' . $user->name;

            $infoText = fn($news) =>
            '<a href="' . route('page.filter', ['category' => $news->category->slug]) . '">
                <span class="color1">' . $news->category->name . '</span>
            </a>';
        }

        if ($request->has('q')) {
            $query->where('title', 'like', '%' . $request->q . '%')
                ->orWhere('description', 'like', '%' . $request->q . '%');
            $title = 'Hasil Pencarian untuk "' . $request->q . '"';

            $infoText = fn($news) =>
            '<a href="' . route('page.filter', ['user' => $news->user->id]) . '">
                <span class="color1">' . $news->user->name . '</span>
            </a>';
        }

        if (!$title) {
            abort(404);
        }

        $news = $query->paginate(16);

        return view('01_new_era.page', compact('news', 'title', 'infoText'));
    }
}
