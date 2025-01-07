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

    // Menampilkan berita berdasarkan ID
    Route::get('/detail/{id}', [NewsApiController::class, 'findById']);

    // Menampilkan berita berdasarkan tag name
    // Route::get('/tag/{tagName}', [NewsApiController::class, 'getByTag']);

    // Menampilkan berita berdasarkan user_id (dalam hal ini menggunakan ID user)
    // Route::get('/author/{id}', [NewsApiController::class, 'getByAuthor']);

});

Route::get('/categories', [NewsApiController::class, 'indexCategories']);
