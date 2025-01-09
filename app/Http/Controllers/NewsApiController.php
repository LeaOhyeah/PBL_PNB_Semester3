<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Pgvector\Laravel\Distance;

class NewsApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/news/home",
     *     summary="Mendapatkan latest_news dan segment jika menggunakan /home, terdapat parameter seperti slug, title, author dan tag yang mengembalikan pagination, terdapat parameter id yang mengembalikan news (detail berita) dan related_news",
     *     tags={"News"},
     *     @OA\Parameter(
     *     name="id",
     *     in="query",
     *     required=false,
     *     description="ID of the news item to fetch (optional)",
     *     @OA\Schema(type="string", example="8cc48c8c-949b-478c-a7d7-3e8b33e4e204")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Limit the number of latest news items (default: 5)",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="limitPaginate",
     *         in="query",
     *         required=false,
     *         description="Limit the number of paginated news items per page (default: 25)",
     *         @OA\Schema(type="integer", example=25)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for paginated results (default: 1)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="slug",
     *         in="query",
     *         required=false,
     *         description="Category slug to filter news by category",
     *         @OA\Schema(type="string", example="prestasi")
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=false,
     *         description="Title keyword to filter news by title",
     *         @OA\Schema(type="string", example="breaking news")
     *     ),
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         required=false,
     *         description="Author name to filter news by author",
     *         @OA\Schema(type="string", example="John Doe")
     *     ),
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         required=false,
     *         description="Tag name to filter news by tag",
     *         @OA\Schema(type="string", example="technology")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="number", example=3.1),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="page", type="integer", example=1),
     *             @OA\Property(property="total", type="integer", example=10),
     *             @OA\Property(property="next_url", type="string", example="http://example.com/api/news/home?slug=prestasi&title=example+title&limitPaginate=10&page=2"),
     *             @OA\Property(property="prev_url", type="string", example="http://example.com/api/news/home?slug=prestasi&title=example+title&limitPaginate=10&page=1"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="latest_news", type="array",
     *                     @OA\Items(ref="#/components/schemas/NewsItem")
     *                 ),
     *                 @OA\Property(property="segment", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=123),
     *                         @OA\Property(property="name", type="string", example="Prestasi"),
     *                         @OA\Property(property="slug", type="string", example="prestasi"),
     *                         @OA\Property(property="news", type="array",
     *                             @OA\Items(ref="#/components/schemas/NewsItem")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="number", example=3.1),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=400),
     *             @OA\Property(property="message", type="string", example="Invalid input parameter"),
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(type="string", example="The limit and page parameters must be positive integers.")
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="NewsItem",
     *     type="object",
     *     @OA\Property(property="id", type="string", example="kasdbf734ijwkhdb"),
     *     @OA\Property(property="title", type="string", example="Sample News Title"),
     *     @OA\Property(property="content_url", type="string", example="image_back_end.png"),
     *     @OA\Property(property="short_desc", type="string", example="Short description here"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-25T09:50:49.000Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-28T11:27:50.422Z"),
     *     @OA\Property(property="segment", type="string", example="prestasi"),
     *     @OA\Property(property="category", type="object",
     *         @OA\Property(property="id", type="integer", example=123),
     *         @OA\Property(property="name", type="string", example="Prestasi"),
     *         @OA\Property(property="slug", type="string", example="prestasi")
     *     ),
     *     @OA\Property(property="writer", type="object",
     *         @OA\Property(property="id", type="integer", example=123),
     *         @OA\Property(property="name", type="string", example="Author Name")
     *     ),
     *     @OA\Property(property="tags", type="array",
     *         @OA\Items(type="object",
     *             @OA\Property(property="id", type="integer", example=123),
     *             @OA\Property(property="name", type="string", example="coding")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?? 5;
        $limitPaginate = $request->input('limitPaginate') ?? 25;
        $page = $request->input('page') ?? 1;
        $slug = $request->input('slug');
        $title = $request->input('title');
        $author = $request->input('author');
        $tag = $request->input('tag');
        $id = $request->input('id');

        // find by id
        if ($id) {
            return $this->findById($id);
        }

        // Validasi input
        if ((!is_numeric($limit) || $limit < 0) || (!is_numeric($page) || $page < 1) || (!is_numeric($limitPaginate) || $limitPaginate < 1)) {
            return response()->json([
                "version" => env('APP_VERSION'),
                "status" => "error",
                "code" => 400,
                "message" => "Invalid input parameter",
                "errors" => [
                    "The limit and page parameters must be positive integers."
                ]
            ], 400);
        }

        // Filter by param
        if ($slug || $title || $author || $tag) {
            // Query dasar
            $newsQuery = News::with(['category', 'user', 'tags'])
                ->where('verified_at', '!=', null)
                ->orderBy('created_at', 'desc');

            // Filter slug
            if ($slug) {
                $newsQuery->whereRelation('category', 'slug', $slug);
            }

            // Filter title
            if ($title) {
                $newsQuery->where('title', 'LIKE', "%$title%");
            }

            // Filter author
            if ($author) {
                $newsQuery->whereRelation('user', 'email', 'LIKE', "%$author%");
            }

            // Filter tag
            if ($tag) {
                $newsQuery->whereHas('tags', function ($query) use ($tag) {
                    $query->where('name', 'LIKE', "%$tag%");
                });
            }

            // Paginate query
            $newsList = $newsQuery->paginate($limitPaginate, ['*'], 'page', $page);

            // Get all param
            $queryParams = [
                'slug' => $slug,
                'title' => $title,
                'author' => $author,
                'tag' => $tag,
                'limitPaginate' => $limitPaginate,
                'page' => $newsList->currentPage() + 1, // Tambahkan 1 ke halaman saat ini untuk next_url
            ];

            // Delete null param
            $queryParams = array_filter($queryParams, function ($value) {
                return !is_null($value) && $value !== '';
            });

            // Build next and prev url (if exist)
            $nextUrl = $newsList->currentPage() < $newsList->lastPage()
                ? env('APP_URL') . "/api/news/home?" . http_build_query($queryParams)
                : "";

            $prevUrl = $newsList->currentPage() > 1
                ? env('APP_URL') . "/api/news/home?" . http_build_query(array_merge($queryParams, ['page' => $newsList->currentPage() - 1]))
                : "";

            // Format respons
            $response = [
                "version" => env('APP_VERSION'),
                "status" => "success",
                "code" => 200,
                "message" => "",
                "page" => $newsList->currentPage(),
                "total" => $newsList->lastPage(),
                "prev_url" => $prevUrl,
                "next_url" => $nextUrl,
                "data" => [
                    "list" => $newsList->values()->map(fn($news) => $this->formatNewsData($news))
                ]
            ];
        } else {
            // home default (latest and segment)
            $categories = Category::with('news')->orderBy('created_at', 'desc')->limit(4)->get()->map(function ($category) use ($limit) {
                $category->setRelation('news', $category->news->where('verified_at', '!=', null)->take($limit));
                return $category;
            });

            $latestNews = News::with(['category', 'user', 'tags'])
                ->where('verified_at', '!=', null)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            // Format response
            $response = [
                "version" => env('APP_VERSION'),
                "status" => "success",
                "code" => 200,
                "message" => "",
                "page" => 1,
                "total" => 1,
                "data" => [
                    "latest_news" => $latestNews->values()->map(fn($news) => $this->formatNewsData($news)),
                    "segment" => $categories->values()->map(function ($category) {
                        return [
                            "id" => $category->id,
                            "name" => $category->name,
                            "slug" => $category->slug,
                            "news" => $category->news->values()->map(fn($news) => $this->formatNewsData($news))
                        ];
                    })
                ]
            ];
        }

        return response()->json($response, 200);
    }


    /**
     * Format individual news data for response.
     */
    private function formatNewsData($news)
    {
        return [
            "id" => $news->id,
            "title" => $news->title,
            "content_url" => $news->content_url,
            "short_desc" => $news->short_desc ?? "Lorem ipsum dolor sit amet consectetur adipisicing elit. Adipisci suscipit autem, facilis delectus nostrum maiores natus. Deleniti temporibus molestias accusamus distinctio maxime inventore impedit exercitationem?.",
            "created_at" => $news->created_at->toIso8601String(),
            "updated_at" => $news->updated_at->toIso8601String(),
            "verified_at" => $news->updated_at->toIso8601String(),
            "segment" => $news->category->slug ?? null,
            "category" => [
                "id" => $news->category->id ?? null,
                "name" => $news->category->name ?? null,
                "slug" => $news->category->slug ?? null,
            ],
            "writer" => [
                "id" => $news->user->id ?? null,
                "name" => $news->user->name ?? null,
            ],
            "tags" => $news->tags->values()->map(fn($tag) => [
                "id" => $tag->id,
                "name" => $tag->name,
            ]),
        ];
    }


    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get list of categories",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Limit the number of categories",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="number", example=3.1),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Categories fetched successfully"),
     *             @OA\Property(property="total", type="integer", example=5),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="list", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Technology"),
     *                         @OA\Property(property="slug", type="string", example="technology")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="number", example=3.1),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=400),
     *             @OA\Property(property="message", type="string", example="Invalid input parameter"),
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(type="string", example="The limit parameter must be a positive integer.")
     *             )
     *         )
     *     )
     * )
     */
    public function indexCategories(Request $request)
    {
        $limit = $request->input('limit');

        // Validasi parameter limit
        if ($limit !== null && (!is_numeric($limit) || $limit < 0)) {
            return response()->json([
                "version" => env('APP_VERSION'),
                "status" => "error",
                "code" => 400,
                "message" => "Invalid input parameter",
                "errors" => ["The limit parameter must be a positive integer."]
            ], 400);
        }

        // Query kategori
        $categoriesQuery = Category::query()->orderBy('name', 'asc');
        $categories = $limit ? $categoriesQuery->limit($limit)->get() : $categoriesQuery->get();

        // Format respons
        $response = [
            "version" => env('APP_VERSION'),
            "status" => "success",
            "code" => 200,
            "message" => "Categories fetched successfully",
            "total" => $categories->count(),
            "data" => [
                "list" => $categories->values()->map(fn($category) => [
                    "id" => $category->id,
                    "name" => $category->name,
                    "slug" => $category->slug,
                ])
            ]
        ];

        return response()->json($response, 200);
    }


    private function findById($id)
    {
        $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])->find($id);

        if (!$news) {
            return response()->json([
                "version" => env('APP_VERSION'),
                "status" => "error",
                "code" => 404,
                "message" => "News not found"
            ], 404);
        }

        $newsWords = preg_split('/\s+/', strtolower($news->title)); // Split title into words

        // Fetch related news
        $relatedNews = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
            ->where('id', '!=', $id)
            ->where('verified_at', '!=', null)
            ->get()
            ->filter(function ($relatedItem) use ($newsWords) {
                $relatedWords = preg_split('/\s+/', strtolower($relatedItem->title));
                return count(array_intersect($newsWords, $relatedWords)) > 0;
            })
            ->sortByDesc(function ($relatedItem) use ($newsWords) {
                $relatedWords = preg_split('/\s+/', strtolower($relatedItem->title));
                return count(array_intersect($newsWords, $relatedWords));
            })
            ->take(5)
            ->values(); // Reset the index

        // If less than 5 related news are found, fetch fallback news
        if ($relatedNews->count() < 5) {
            $fallbackNews = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
                ->where('id', '!=', $id)
                ->where('verified_at', '!=', null)
                ->whereNotIn('id', $relatedNews->pluck('id')) // Exclude already related news
                ->orderBy('created_at', 'desc')
                ->take(5 - $relatedNews->count()) // Take only as many as needed to reach 5
                ->get();

            $relatedNews = $relatedNews->merge($fallbackNews)->values(); // Merge and reset index
        }

        // Format response
        $response = [
            "version" => env('APP_VERSION'),
            "status" => "success",
            "code" => 200,
            "message" => "News fetched successfully",
            "data" => [
                "news" => $this->formatNewsData($news),
                "related" => $relatedNews->values()->map(fn($related) => $this->formatNewsData($related))
            ]
        ];

        return response()->json($response, 200);
    }


    // pgsql
    // public function findById($id)
    // {
    //     $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
    //         ->select('id', 'title', 'category_id', 'user_id', 'embedding')
    //         ->find($id);

    //     if (!$news) {
    //         return response()->json([
    //             "version" => env('APP_VERSION'),
    //             "status" => "error",
    //             "code" => 404,
    //             "message" => "News not found"
    //         ], 404);
    //     }

    //     $relatedNews = $news->nearestNeighbors('embedding', Distance::L2)->take(10)->get();

    //     // Format response
    //     $response = [
    //         "version" => env('APP_VERSION'),
    //         "status" => "success",
    //         "code" => 200,
    //         "message" => "News fetched successfully",
    //         "data" => [
    //             "news" => $news,
    //             "related" => $relatedNews
    //         ]
    //     ];

    //     return response()->json($response, 200);
    // }

}
