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
     *     summary="Get latest news",
     *     tags={"News"},
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
     *                     
     *                     @OA\Property(property="latest_news", type="array",
     *                         @OA\Items(ref="#/components/schemas/NewsItem")
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
     *     @OA\Property(property="category", type="object",
     *         @OA\Property(property="id", type="integer", example=123),
     *         @OA\Property(property="name", type="string", example="Prestasi")
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

        $latestNewsQuery = News::with(['category', 'user', 'tags'])->where('verified_at', '!=', null)->orderBy('created_at', 'desc');
        $latesNews = $limit ? $latestNewsQuery->limit($limit)->get() : $latestNewsQuery->get();

        $response = [
            "version" => env('APP_VERSION'),
            "status" => "success",
            "code" => 200,
            "message" => "News fetched successfully",
            "total" => $latesNews->count(),
            "data" => [
                "list" => [
                    "latest_news" => $latesNews->map(fn($news) => $this->formatNewsData($news))
                ]
            ]
        ];

        return response()->json($response, 200);
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
        $categoriesQuery = Category::query()->where('verified_at', '!=', null)->orderBy('name', 'asc');
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
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Limit the number of news items",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword for news",
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
     *             @OA\Property(property="message", type="string", example="News fetched successfully"),
     *             @OA\Property(property="total", type="integer", example=10),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="list", type="array",
     *                         @OA\Items(ref="#/components/schemas/NewsItem")
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
    public function search(Request $request)
    {
        $limit = $request->input('limit');
        $search = $request->input('search');

        if ($limit !== null && (!is_numeric($limit) || $limit < 0)) {
            return response()->json([
                "version" => env('APP_VERSION'),
                "status" => "error",
                "code" => 400,
                "message" => "Invalid input parameter",
                "errors" => ["The limit parameter must be a positive integer."]
            ], 400);
        }

        $latestNewsQuery = News::with(['category', 'user', 'tags'])
            ->where('verified_at', '!=', null)
            ->orderBy('created_at', 'desc');

        if ($search) {
            $latestNewsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhereHas('tags', function ($tagQuery) use ($search) {
                        $tagQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        $latesNews = $limit ? $latestNewsQuery->limit($limit)->get() : $latestNewsQuery->get();

        $response = [
            "version" => env('APP_VERSION'),
            "status" => "success",
            "code" => 200,
            "message" => "News fetched successfully",
            "total" => $latesNews->count(),
            "data" => [
                "list" => $latesNews->map(fn($news) => $this->formatNewsData($news))
                
            ]
        ];

        return response()->json($response, 200);
    }


    // done
    /**
     * @OA\Get(
     *      path="/api/news/{id}",
     *      operationId="getNewsById",
     *      tags={"News"},
     *      summary="Get news by ID",
     *      description="Returns a specific news article by ID, including related news based on similar titles",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="News ID",
     *          @OA\Schema(type="string", example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="version", type="string", example="1.0.1"),
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="News fetched successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="news", ref="#/components/schemas/News"),
     *                  @OA\Property(property="related", type="array",
     *                      @OA\Items(ref="#/components/schemas/News")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="News not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="version", type="string", example="1.0.1"),
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="code", type="integer", example=404),
     *              @OA\Property(property="message", type="string", example="News not found")
     *          )
     *      )
     * )
     * 
     * @OA\Schema(
     *     schema="News",
     *     type="object",
     *     @OA\Property(property="id", type="string", example="5124982c-c7f5-4979-988f-4ef8fec17918"),
     *     @OA\Property(property="title", type="string", example="Ogoh ogoh denpasar 2025 keren dan super detail 🔥🔥🔥"),
     *     @OA\Property(property="content_url", type="string", example="LCevkk0DEV8"),
     *     @OA\Property(property="description", type="string", example="updated now"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-27T03:02:32.000000Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-27T04:02:22.000000Z"),
     *     @OA\Property(property="category", type="object",
     *         @OA\Property(property="id", type="integer", example=3),
     *         @OA\Property(property="name", type="string", example="Event"),
     *         @OA\Property(property="slug", type="string", example="event")
     *     ),
     *     @OA\Property(property="user", type="object",
     *         @OA\Property(property="id", type="integer", example=2),
     *         @OA\Property(property="name", type="string", example="Admin Adit")
     *     ),
     *     @OA\Property(property="tags", type="array",
     *         @OA\Items(type="object",
     *             @OA\Property(property="id", type="integer", example=123),
     *             @OA\Property(property="name", type="string", example="culture")
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

        $newsWords = preg_split('/\s+/', strtolower($news->title));  // Convert to lowercase and split by spaces

        // Fetch related news based on word matches in the title
        $relatedNews = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
            ->where('id', '!=', $id)  // Exclude the current news item
            ->where('verified_at', '!=', null)
            ->get()
            ->filter(function ($relatedItem) use ($newsWords) {
                // Split related news title into words
                $relatedWords = preg_split('/\s+/', strtolower($relatedItem->title));

                // Calculate intersection of words
                $intersection = array_intersect($newsWords, $relatedWords);

                // Return news if there is at least one matching word
                return count($intersection) > 0;
            })
            ->sortByDesc(function ($relatedItem) use ($newsWords) {
                // Calculate the score based on the number of matching words (higher score = more related)
                $relatedWords = preg_split('/\s+/', strtolower($relatedItem->title));
                $intersection = array_intersect($newsWords, $relatedWords);
                return count($intersection); // Sorting by the number of matching words
            })
            ->take(5); // Limit to 5 related news items

        // Format response
        $response = [
            "version" => env('APP_VERSION'),
            "status" => "success",
            "code" => 200,
            "message" => "News fetched successfully",
            "data" => [
                "news" => $news,
                "related" => $relatedNews
            ]
        ];

        return response()->json($response, 200);
    }


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


    // done
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

    // done
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

    /**
     * Helper function to format a news item.
     */
    private function formatNewsData($news)
    {
        return [
            "id" => $news->id,
            "title" => $news->title,
            "content_url" => $news->content_url,
            "short_desc" => $news->short_desc,
            "created_at" => $news->created_at->toIso8601String(),
            "updated_at" => $news->updated_at->toIso8601String(),
            "category" => [
                "id" => $news->category->id,
                "name" => $news->category->name,
            ],
            "writer" => [
                "id" => $news->user->id,
                "name" => $news->user->name,
            ],
            "tags" => $news->tags->map(fn($tag) => [
                "id" => $tag->id,
                "name" => $tag->name,
            ])
        ];
    }
}
