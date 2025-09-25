<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

Route::prefix('api')->group(function () {
    // If your app already has /api prefix in api.php, just use routes below (no need prefix)
});

// Core asset routes (api.php already loaded with /api prefix)
Route::get('assets', [AssetController::class, 'index']);
Route::get('assets/{id}', [AssetController::class, 'show']);