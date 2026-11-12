<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
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

// سيرفر التراخيص والسحابة (سيرفر المالك فقط، HQ_ENABLED=true) — مشفر وبتوكن
Route::prefix('hq')->middleware('hq')->group(function () {
    Route::post('/request', [\App\Http\Controllers\HqApiController::class, 'request'])->middleware('throttle:20,1');
    Route::post('/status', [\App\Http\Controllers\HqApiController::class, 'status'])->middleware('throttle:30,1');
    Route::post('/heartbeat', [\App\Http\Controllers\HqApiController::class, 'heartbeat'])->middleware('throttle:30,1');
});
Route::post('/cloud/sync', [\App\Http\Controllers\CloudSyncController::class, 'sync'])->middleware(['hq', 'cloud.token', 'throttle:240,1']);

// جاهزية المتجر الإلكتروني: نفس الهيكل تحت store و ecommerce بـ api_token
foreach (['store', 'ecommerce'] as $ns) {
    Route::prefix("v1/$ns")->middleware('api.token')->group(function () {
        Route::get('/catalog', [\App\Http\Controllers\StoreApiController::class, 'catalog']);
        Route::get('/items/{key}', [\App\Http\Controllers\StoreApiController::class, 'item']);
        Route::get('/categories', [\App\Http\Controllers\StoreApiController::class, 'categories']);
        Route::get('/stock', [\App\Http\Controllers\StoreApiController::class, 'stock']);
        Route::post('/orders', [\App\Http\Controllers\StoreApiController::class, 'order']);
        Route::post('/webhook/orders', [\App\Http\Controllers\StoreApiController::class, 'order']);
    });
}
