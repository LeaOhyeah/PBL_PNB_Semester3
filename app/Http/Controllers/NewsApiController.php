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
    // done
    /**
     * @OA\Get(
     *     path="/api/news/home",
     *     summary="Get the latest news and category segments",
     *     tags={"News"},
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
     *             @OA\Property(property="next_url", type="string", example="http://example.com/api/news/home?slug=prestasi&limitPaginate=25&page=2"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="latest_news", type="array",
     *                     @OA\Items(ref="#/components/schemas/NewsItem")
     *                 ),
     *                 @OA\Property(property="list", type="array",
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

        if ($request->has('slug') || $request->has('page')) {
            if (!$request->has('slug') || !$request->has('page')) {
                return response()->json([
                    "version" => env('APP_VERSION'),
                    "status" => "error",
                    "code" => 400,
                    "message" => "Invalid input parameter",
                    "errors" => [
                        "The page must have slug."
                    ]
                ], 400);
            }
        }

        if ($request->has('page')) {
            // Fetch latest news with pagination
            $latestNewsQuery = News::with(['category', 'user', 'tags'])
                ->where('verified_at', '!=', null)
                ->orderBy('created_at', 'desc');

            $newsList = News::with(['category', 'user', 'tags'])
                ->where('verified_at', '!=', null)
                ->whereRelation('category', 'slug', $slug) // Gunakan whereRelation untuk sintaks lebih ringkas
                ->orderBy('created_at', 'desc')
                ->paginate($limitPaginate, ['*'], 'page', $page);

            // Format response
            $response = [
                "version" => env('APP_VERSION'),
                "status" => "success",
                "code" => 200,
                "message" => "",
                "page" => $newsList->currentPage(),
                "total" => $newsList->lastPage(),
                "next_url" => $newsList->currentPage() < $newsList->lastPage()
                    ? env('APP_URL') . "/api/news/home?slug=" . $slug . "&limitPaginate=" . $limitPaginate . "&page=" . ($newsList->currentPage() + 1)
                    : "", // Set string kosong jika halaman saat ini adalah yang terakhir
                "data" => [
                    "list" => $newsList->map(fn($news) => $this->formatNewsData($news))
                ]
            ];
        } else {
            $categories = Category::with('news')->orderBy('created_at', 'desc')->limit(4)->get()->map(function ($category) use ($limit) {
                $category->setRelation('news', $category->news->where('verified_at', '!=', null)->take($limit));
                return $category;
            });

            // Fetch latest news without pagination
            $latestNewsQuery = News::with(['category', 'user', 'tags',])
                ->where('verified_at', '!=', null)
                ->orderBy('created_at', 'desc');

            $latestNews = $latestNewsQuery->limit($limit)->get();

            // Format response
            $response = [
                "version" => env('APP_VERSION'),
                "status" => "success",
                "code" => 200,
                "message" => "",
                "page" => 1,
                "total" => 1,
                "data" => [
                    "latest_news" => $latestNews->map(fn($news) => $this->formatNewsData($news)),
                    "segment" => $categories->map(function ($category) {
                        return [
                            "id" => $category->id,
                            "name" => $category->name,
                            "slug" => $category->slug,
                            "news" => $category->news->map(fn($news) => $this->formatNewsData($news))
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
            "tags" => $news->tags->map(fn($tag) => [
                "id" => $tag->id,
                "name" => $tag->name,
            ]),
        ];
    }


    // done
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
                "list" => $categories->map(fn($category) => [
                    "id" => $category->id,
                    "name" => $category->name,
                    "slug" => $category->slug,
                ])
            ]
        ];

        return response()->json($response, 200);
    }


    /**
     * @OA\Get(
     *     path="/api/news",
     *     summary="Search news",
     *     description="Fetch a list of news based on search criteria with pagination.",
     *     operationId="searchNews",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of per page (default is 25).",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, example=25)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number for paginated results (default is 1).",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Keyword to search by title, description, or tags.",
     *         required=false,
     *         @OA\Schema(type="string", example="Breaking News")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News fetched successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="News fetched successfully"),
     *             @OA\Property(property="page", type="integer", example=1),
     *             @OA\Property(property="total", type="integer", example=10),
     *             @OA\Property(property="next_url", type="string", example="http://example.com/api/news/search?search=keyword&limit=25&page=2"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="list",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Breaking News Today"),
     *                         @OA\Property(property="description", type="string", example="This is the description of the news."),
     *                         @OA\Property(
     *                             property="category",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="Sports")
     *                         ),
     *                         @OA\Property(
     *                             property="tags",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=5),
     *                                 @OA\Property(property="name", type="string", example="Football")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="user",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=3),
     *                             @OA\Property(property="name", type="string", example="John Doe")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input parameters.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=400),
     *             @OA\Property(property="message", type="string", example="Invalid input parameter"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(type="string", example="The limit, limit, and page parameters must be positive integers.")
     *             )
     *         )
     *     )
     * )
     */
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
                "list" => $newsList->map(fn($news) => $this->formatNewsData($news))
            ]
        ];

        return response()->json($response, 200);
    }


    /**
     * @OA\Get(
     *     path="/api/news/detail/{id}",
     *     summary="Retrieve a single news item by its ID",
     *     description="Fetch a news item along with related news based on its title",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the news item to fetch",
     *         @OA\Schema(type="string", example="1451e622-95bd-412e-a82f-d8cc41ad35f3")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved the news item",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="string", example="1.0"),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="News fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="news",
     *                     type="object",
     *                     description="Details of the requested news item",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Breaking News: Major Event"),
     *                     @OA\Property(property="description", type="string", example="A major event has taken place, drawing global attention."),
     *                     @OA\Property(property="category", type="object", 
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="World News"),
     *                         @OA\Property(property="slug", type="string", example="world-news")
     *                     ),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe")
     *                     ),
     *                     @OA\Property(property="tags", type="array", 
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=3),
     *                             @OA\Property(property="name", type="string", example="Breaking")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="related",
     *                     type="array",
     *                     description="List of related news items",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="title", type="string", example="Related News: Updates on the Major Event"),
     *                         @OA\Property(property="description", type="string", example="Further updates on the major event are now available."),
     *                         @OA\Property(property="category", type="object", 
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="World News"),
     *                             @OA\Property(property="slug", type="string", example="world-news")
     *                         ),
     *                         @OA\Property(property="user", type="object",
     *                             @OA\Property(property="id", type="integer", example=4),
     *                             @OA\Property(property="name", type="string", example="Jane Smith")
     *                         ),
     *                         @OA\Property(property="tags", type="array", 
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=3),
     *                                 @OA\Property(property="name", type="string", example="Breaking")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News item not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="string", example="1.0"),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="News not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="string", example="1.0"),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=500),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function findById($id)
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
                "related" => $relatedNews->map(fn($related) => $this->formatNewsData($related))
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



    /**
     * @OA\Get(
     *     path="/api/news/category/{slug}",
     *     summary="Get news by category",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the category",
     *         @OA\Schema(type="string", example="technology")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Limit the number of news items",
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
     *             @OA\Property(property="message", type="string", example="News fetched successfully"),
     *             @OA\Property(property="total", type="integer", example=10),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="list", type="object",
     *                     @OA\Property(property="latest_news", type="array",
     *                         @OA\Items(ref="#/components/schemas/NewsItem")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="number", example=3.1),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
    public function getByCategory(Request $request, $slug)
    {
        // Validate category existence by slug
        $category = Category::where('slug', $slug)->firstOrFail();

        // Get limit from request (default is no limit)
        $limit = $request->input('limit');
        if ($limit !== null && (!is_numeric($limit) || $limit < 0)) {
            return response()->json([
                "version" => env('APP_VERSION'),
                "status" => "error",
                "code" => 400,
                "message" => "Invalid input parameter",
                "errors" => ["The limit parameter must be a positive integer."]
            ], 400);
        }

        // Fetch news by category
        $newsQuery = News::with(['category', 'user', 'tags'])
            ->where('category_id', $category->id)
            ->where('verified_at', '!=', null)
            ->orderBy('created_at', 'desc');
        $news = $limit ? $newsQuery->limit($limit)->get() : $newsQuery->get();

        // Format response
        $response = [
            "version" => env('APP_VERSION'),
            "status" => "success",
            "code" => 200,
            "message" => "News fetched successfully",
            "total" => $news->count(),
            "data" => [
                "list" => [
                    "latest_news" => $news->map(fn($item) => $this->formatNewsData($item))
                ]
            ]
        ];

        return response()->json($response, 200);
    }


    /**
     * @OA\Get(
     *     path="/api/news/category/{slug}/page",
     *     summary="Get paginated news by category",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the category",
     *         @OA\Schema(type="string", example="event")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Number of news items per page (default: 10)",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination (default: 1)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="string", example="1.0.1"),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="News paginate fetched successfully"),
     *             @OA\Property(property="total", type="integer", example=6),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="string", example="5936a72b-10f8-4e75-9713-0a8b5e1910b4"),
     *                         @OA\Property(property="user_id", type="integer", example=2),
     *                         @OA\Property(property="category_id", type="integer", example=3),
     *                         @OA\Property(property="title", type="string", example="Ogoh ogoh denpasar 2025 keren dan super detail 🔥🔥🔥"),
     *                         @OA\Property(property="content_url", type="string", example="LCevkk0DEV8"),
     *                         @OA\Property(property="verified_at", type="string", format="date-time", example="2024-12-27 12:02:32"),
     *                         @OA\Property(property="description", type="string", example="updated now"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-27T03:02:32.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-27T04:02:22.000000Z"),
     *                         @OA\Property(property="category", type="object",
     *                             @OA\Property(property="id", type="integer", example=3),
     *                             @OA\Property(property="name", type="string", example="Event"),
     *                             @OA\Property(property="slug", type="string", example="event")
     *                         ),
     *                         @OA\Property(property="user", type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="Admin Adit")
     *                         ),
     *                         @OA\Property(property="tags", type="array", @OA\Items(type="object"))
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/news/category/event/page?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/api/news/category/event/page?page=3"),
     *                 @OA\Property(property="links", type="array", 
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example=null),
     *                         @OA\Property(property="label", type="string", example="pagination.previous"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="next_page_url", type="string", example="http://127.0.0.1:8000/api/news/category/event/page?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/news/category/event/page"),
     *                 @OA\Property(property="per_page", type="integer", example=2),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="to", type="integer", example=2),
     *                 @OA\Property(property="total", type="integer", example=6)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="version", type="string", example="1.0.1"),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
    public function getByCategoryPaginate(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $limit = $request->query('limit', 10); // Default limit to 10 if not provided
        $page = $request->query('page', 1); // Default page to 1 if not provided

        $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
            ->where('category_id', $category->id)
            ->where('verified_at', '!=', null)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        $response = [
            "version" => env('APP_VERSION'),
            "status" => "success",
            "code" => 200,
            "message" => "News paginate fetched successfully",
            "total" => $news->total(),
            "data" =>
            $news
        ];

        return response()->json($response, 200);
    }
}
