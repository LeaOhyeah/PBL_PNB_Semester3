<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', [BerandaController::class, 'index']);
// Route::get('/', [BerandaController::class, 'test']);
// Route::get('/{id}', [BerandaController::class, 'testDetail'])->name('single');
// Route::get('/search', [BerandaController::class, 'search'])->name('search');
// Route::get('/search?q=data', [BerandaController::class, 'search'])->name('search');

Route::post('/webhook', [TelegramController::class, 'webhook']);

Route::get('/', [PageController::class, 'index'])->name('news.index');
Route::get('/news/{id}', [PageController::class, 'news'])->name('news.show');
Route::get('/page/filter', [PageController::class, 'filter'])->name('page.filter');
Route::get('/page/latest', [PageController::class, 'latest'])->name('page.latest');
Route::get('/about', [PageController::class, 'about'])->name('page.about');
