<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TransferController;

Route::prefix('api')->group(function () {
    // If your app already has /api prefix in api.php, just use routes below (no need prefix)
});

// Core asset routes (api.php already loaded with /api prefix)
Route::post('assets', [AssetController::class, 'store']);
