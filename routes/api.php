<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;


Route::get('/test', function() {
    return response()->json(['message' => 'API đang hoạt động!']);
});

// Core asset routes (api.php already loaded with /api prefix)
Route::get('assets', [AssetController::class, 'index']);
Route::get('assets/{id}', [AssetController::class, 'show']);
Route::post('assets', [AssetController::class, 'store']);
Route::put('/assets/{asset}', [AssetController::class, 'update']);
Route::delete('assets/{asset}', [AssetController::class, 'destroy']);
// Additional routes for asset images
Route::post('assets/{asset}/images', [AssetController::class, 'uploadImages']);
