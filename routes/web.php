<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rss\RssFeedController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('feed', [RssFeedController::class, 'feed']);
Route::get('feed/{category}', [RssFeedController::class, 'feedByCategory']);
// Route::get('feed/{category}/{child}', [RssFeedController::class, 'feedByChildCategory']);

Route::get('/', function () {
    return view('welcome');
});
