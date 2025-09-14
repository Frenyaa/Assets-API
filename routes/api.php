<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TransferController;

Route::prefix('api')->group(function () {
    // If your app already has /api prefix in api.php, just use routes below (no need prefix)
});

// Core asset routes (api.php already loaded with /api prefix)
Route::get('assets', [AssetController::class, 'index']);
Route::get('assets/{id}', [AssetController::class, 'show']);
Route::post('assets', [AssetController::class, 'store']);
Route::put('assets/{asset}', [AssetController::class, 'update']);
Route::patch('assets/{asset}', [AssetController::class, 'update']);
Route::delete('assets/{asset}', [AssetController::class, 'destroy']);

// images & transfer
Route::post('assets/{asset}/images', [AssetController::class, 'uploadImages']);
Route::post('assets/{asset}/transfer', [TransferController::class, 'transfer']);
Route::get('assets/{asset}/transfers', [TransferController::class, 'history']);