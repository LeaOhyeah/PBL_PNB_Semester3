<?php

use App\Http\Controllers\BerandaController;
use Illuminate\Support\Facades\Route;

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
Route::get('/', [BerandaController::class, 'test']);
Route::get('/{id}', [BerandaController::class, 'testDetail'])->name('single');

Route::get('/search', [BerandaController::class, 'search'])->name('search');
// Route::get('/search?q=data', [BerandaController::class, 'search'])->name('search');
