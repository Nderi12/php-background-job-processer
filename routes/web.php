<?php

use App\Http\Controllers\BackgroundJobController;
use App\Http\Controllers\GithubController;
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

Route::get('/', [BackgroundJobController::class, 'index']);

Route::post('github', [GithubController::class, 'sync'])->name('github');
Route::post('clear', [GithubController::class, 'clean'])->name('clear');
