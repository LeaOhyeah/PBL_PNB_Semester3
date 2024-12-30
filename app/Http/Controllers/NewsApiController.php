<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class NewsApiController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/news",
     *      operationId="getNewsList",
     *      tags={"News"},
     *      summary="Get list of news with limit option",
     *      description="Returns a list of news with their category, user, and tags. Optionally limit the number of news.",
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit the number of news. If not provided, returns all news.",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/News"
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *          response=400,
     *          description="Bad request"
     *       )
     * )
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit');

        if ($limit) {
            $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
                ->orderBy('created_at', 'desc')
                ->limit($limit) // Apply the limit
                ->get();
        } else {
            // Return all data if no limit is provided
            $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json($news);
    }


    /**
     * @OA\Get(
     *      path="/api/news/page",
     *      operationId="getNewsListPaginate",
     *      tags={"News"},
     *      summary="Get list of news with pagination",
     *      description="Returns a list of news with pagination and limit options.",
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit the number of news",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number for pagination",
     *          required=false,
     *          @OA\Schema(type="integer", default=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/News"
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *          response=400,
     *          description="Bad request"
     *       )
     * )
     */
    public function indexPaginate(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);

        $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json($news);
    }


    /**
     * @OA\Get(
     *      path="/api/news/{id}",
     *      operationId="getNewsById",
     *      tags={"News"},
     *      summary="Get news by ID",
     *      description="Returns a specific news article by ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="News ID"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Schema(ref="#/components/schemas/News")
     *       ),
     *       @OA\Response(
     *          response=404,
     *          description="News not found"
     *       )
     * )
     */
    public function findById($id)
    {
        $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])->find($id);
        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }
        return response()->json($news);
    }


    /**
     * @OA\Get(
     *      path="/api/news/category/{slug}",
     *      operationId="getNewsByCategory",
     *      tags={"News"},
     *      summary="Get news by category with limit option",
     *      description="Returns news filtered by category slug. Optionally limit the number of news.",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          required=true,
     *          description="Category slug"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit the number of news. If not provided, returns all news from the category.",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/News"
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *          response=404,
     *          description="Category not found"
     *       )
     * )
     */
    public function getByCategory(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $limit = $request->query('limit');

        if ($limit) {
            $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
                ->where('category_id', $category->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit) // Apply the limit
                ->get();
        } else {
            // Return all data if no limit is provided
            $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
                ->where('category_id', $category->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json($news);
    }


    /**
     * @OA\Get(
     *      path="/api/news/category/{slug}/page",
     *      operationId="getNewsByCategoryPaginate",
     *      tags={"News"},
     *      summary="Get news by category with pagination",
     *      description="Returns news filtered by category slug with pagination.",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          required=true,
     *          description="Category slug"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit the number of news",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number for pagination",
     *          required=false,
     *          @OA\Schema(type="integer", default=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/News"
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *          response=404,
     *          description="Category not found"
     *       )
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
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json($news);
    }


    /**
     * @OA\Get(
     *      path="/api/news/tag/{tagName}",
     *      operationId="getNewsByTag",
     *      tags={"News"},
     *      summary="Get news by tag with limit option",
     *      description="Returns news filtered by tag name. Optionally limit the number of news.",
     *      @OA\Parameter(
     *          name="tagName",
     *          in="path",
     *          required=true,
     *          description="Tag name"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit the number of news. If not provided, returns all news for the tag.",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/News"
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *          response=404,
     *          description="Tag not found"
     *       )
     * )
     */
    public function getByTag(Request $request, $tagName)
    {
        $tag = Tag::where('name', $tagName)->first();
        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $limit = $request->query('limit'); // No default limit, will show all if not provided

        if ($limit) {
            $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
                ->whereHas('tags', function ($query) use ($tag) {
                    $query->where('tags.name', $tag->name);
                })
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } else {
            $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
                ->whereHas('tags', function ($query) use ($tag) {
                    $query->where('tags.name', $tag->name);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json($news);
    }

    /**
     * @OA\Get(
     *      path="/api/news/tag/{tagName}/page",
     *      operationId="getNewsByTagPaginate",
     *      tags={"News"},
     *      summary="Get news by tag with pagination",
     *      description="Returns news filtered by tag name with pagination.",
     *      @OA\Parameter(
     *          name="tagName",
     *          in="path",
     *          required=true,
     *          description="Tag name"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit the number of news per page",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number for pagination",
     *          required=false,
     *          @OA\Schema(type="integer", default=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/News"
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *          response=404,
     *          description="Tag not found"
     *       )
     * )
     */
    public function getByTagPaginate(Request $request, $tagName)
    {
        $tag = Tag::where('name', $tagName)->first();
        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $limit = $request->query('limit', 10); // Default limit to 10
        $page = $request->query('page', 1); // Default page to 1

        $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.name', $tag->name);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json($news);
    }

    /**
     * @OA\Get(
     *      path="/api/news/author/{id}",
     *      operationId="getNewsByAuthor",
     *      tags={"News"},
     *      summary="Get news by author with limit option",
     *      description="Returns news filtered by user ID. Optionally limit the number of news.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="User ID"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit the number of news. If not provided, returns all news by the author.",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/News"
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *          response=404,
     *          description="User not found"
     *       )
     * )
     */
    public function getByAuthor(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $limit = $request->query('limit'); // No default limit, will show all if not provided

        if ($limit) {
            $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } else {
            $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json($news);
    }

    /**
     * @OA\Get(
     *      path="/api/news/author/{id}/page",
     *      operationId="getNewsByAuthorPaginate",
     *      tags={"News"},
     *      summary="Get news by author with pagination",
     *      description="Returns news filtered by user ID with pagination.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="User ID"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit the number of news per page",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number for pagination",
     *          required=false,
     *          @OA\Schema(type="integer", default=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  ref="#/components/schemas/News"
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *          response=404,
     *          description="User not found"
     *       )
     * )
     */
    public function getByAuthorPaginate(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $limit = $request->query('limit', 10); // Default limit to 10
        $page = $request->query('page', 1); // Default page to 1

        $news = News::with(['category:id,name,slug', 'user:id,name', 'tags:id,name'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json($news);
    }
}
