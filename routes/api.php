<?php

use App\Http\Controllers\NewsApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('news')->group(function () {
    // search
    Route::get('/', [NewsApiController::class, 'search']);
    
    // Menampilkan semua berita
    Route::get('/home', [NewsApiController::class, 'index']);

    // Menampilkan semua berita dengan pagination dan limit
    Route::get('/page', [NewsApiController::class, 'indexPaginate']);

    // Menampilkan berita berdasarkan ID
    Route::get('/{id}', [NewsApiController::class, 'findById']);

    // Menampilkan berita berdasarkan category slug
    Route::get('/category/{slug}', [NewsApiController::class, 'getByCategory']);

    // Menampilkan berita berdasarkan category slug dengan pagination dan limit
    Route::get('/category/{slug}/page', [NewsApiController::class, 'getByCategoryPaginate']);

    // Menampilkan berita berdasarkan tag name
    // Route::get('/tag/{tagName}', [NewsApiController::class, 'getByTag']);

    // Menampilkan berita berdasarkan tag name dengan pagination dan limit
    // Route::get('/tag/{tagName}/page', [NewsApiController::class, 'getByTagPaginate']);

    // Menampilkan berita berdasarkan user_id (dalam hal ini menggunakan ID user)
    // Route::get('/author/{id}', [NewsApiController::class, 'getByAuthor']);

    // Menampilkan berita berdasarkan user_id (dalam hal ini menggunakan ID user) dengan pagination dan limit
    // Route::get('/author/{id}/page', [NewsApiController::class, 'getByAuthorPaginate']);
});

Route::get('/categories', [NewsApiController::class, 'indexCategories']);
