<?php

use App\Http\Controllers\Advertisement\AdsPositionController;
use App\Http\Controllers\Advertisement\AdsProviderController;
use App\Http\Controllers\Advertisement\AdvertisementController;
use App\Http\Controllers\Common\MenuController;
use App\Http\Controllers\Common\ActivityController;
use App\Http\Controllers\Common\AssetController;
use App\Http\Controllers\Common\AssetCategoryController;
use App\Http\Controllers\LiveMediaController;
use App\Http\Controllers\Post\CategoryController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Post\TagController;
use App\Http\Controllers\Post\TypeController;
use App\Http\Controllers\Post\OpedPostController;
use App\Http\Controllers\Post\LatestPostController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ReporterController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserLogController;
use App\Http\Controllers\Subscribe\NewsletterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// auth routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('token', [AuthController::class, 'token']);

    // reset password
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    // verify email
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
    Route::get('email/resend/{email}', [AuthController::class, 'resend'])->name('verification.resend');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-by-token', [AuthController::class, 'logoutByToken']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    // user routes
    Route::post('update-user-avatar/{user}', [UserController::class, 'updateUserAvatar']);
    Route::patch('update-user-info/{user}', [UserController::class, 'updateUserInfo']);
    Route::patch('update-user-password/{user}', [UserController::class, 'updateUserPassword']);
    Route::get('publishers', [UserController::class, 'getAllUsers']);
    Route::resource('user', UserController::class);
    // user log routes
    Route::delete('user-logs', [UserLogController::class, 'destroyAll']);
    Route::apiResource('user-logs', UserLogController::class);
    // role routes
    Route::patch('add-permission/{role}', [RoleController::class, 'addPermissions']);
    Route::get('permission', [RoleController::class, 'getPermissions']);
    Route::get('permission/{role}', [RoleController::class, 'getPermissionsByRole']);
    Route::get('role-all', [RoleController::class, 'getAllRoles']);
    Route::apiResource('role', RoleController::class);
    // activity logs
    Route::get('activity-log', [ActivityController::class, 'getActivityLogs']);
    Route::delete('activity-log/{activity}', [ActivityController::class, 'destroy']);
    Route::delete('activity-log', [ActivityController::class, 'destroyAll']);
    // menu routes
    Route::post('menu-reorder', [MenuController::class, 'reorderMenu']);
    Route::get('menus', [MenuController::class, 'getMenus']);
    Route::resource('menu', MenuController::class);
    // type routes
    Route::get('type-all', [TypeController::class, 'getAll']);
    Route::apiResource('type', TypeController::class);
    // OpedPost routes
    Route::apiResource('opedpost', OpedPostController::class);
    // post assets
    Route::post('post-assets/{post}', [PostController::class, 'saveAssets']);
    // post statistics routes
    Route::get('post/statistic', [PostController::class, 'getStatistic']);
    // advertisement routes
    Route::prefix('ads')->group(function () {
        Route::get('providers', [AdsProviderController::class, 'getProviders']);
        Route::get('pages', [AdsPositionController::class, 'getPages']);
        Route::get('sections/{page}', [AdsPositionController::class, 'getSections']);
        Route::get('positions/{page}/{section}', [AdsPositionController::class, 'getPositions']);
        Route::apiResource('advertisement', AdvertisementController::class);
    });
     // Controllers Within The "App\Http\Controllers\Common" Namespace
     Route::prefix('asset')->name('asset.')->group(function () {
        // categories routes
        Route::post('category-rebuild', [AssetCategoryController::class, 'rebuildTree']);
        // delete trashed all category
        Route::delete('category-force', [AssetCategoryController::class, 'forceDelete']);
        // delete trashed single category
        Route::delete('category-force/{id}', [AssetCategoryController::class, 'forceSingleDelete']);
        // get all
        Route::get('category-all', [AssetCategoryController::class, 'getAll']);
        // get all as tree
        Route::get('category-tree', [AssetCategoryController::class, 'getAllAsTree']);
        // get all child
        Route::get('category-child', [AssetCategoryController::class, 'getAllChild']);
        Route::apiResource('category', AssetCategoryController::class);
    });

    // media assets
    Route::get('asset-download/{asset}', [AssetController::class, 'downloadAsset']);
    Route::apiResource('asset', AssetController::class);

    // For Newslatter
    Route::prefix('newsletter')->group(function () {
        Route::get('/subscribers', [NewsletterController::class, 'getSubscribers']);
    });

    // locales
    Route::prefix('{locale}')->group(function () {
        // category routes
        Route::get('category-slug/{title}', [CategoryController::class, 'checkSlug']);
        Route::get('categories', [CategoryController::class, 'getAll']);
        Route::get('categories-tree', [CategoryController::class, 'getAllAsTree']);
        Route::post('category-reorder', [CategoryController::class, 'reorderCategories']);
        Route::apiResource('category', CategoryController::class);
        // tag routes
        Route::get('tag-slug/{title}', [TagController::class, 'checkSlug']);
        Route::get('tags', [TagController::class, 'getAll']);
        Route::apiResource('tag', TagController::class);
        // reporter routes
        Route::get('reporter-username/{name}', [ReporterController::class, 'checkUsername']);
        Route::get('reporters', [ReporterController::class, 'getAll']);
        Route::apiResource('reporter', ReporterController::class);
        // post routes
        Route::get('post-slug/{title}', [PostController::class, 'checkSlug']);
        Route::apiResource('post', PostController::class);
        // slider routes
        Route::put('slider-reorder', [SliderController::class, 'reorderSlider']);
        Route::apiResource('slider', SliderController::class);
        // latest post routes
        Route::apiResource('latest-post', LatestPostController::class);
        // live media routes
        Route::apiResource('live-media', LiveMediaController::class);        
    });
});