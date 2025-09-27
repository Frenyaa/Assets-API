<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

Route::get('/test', function() {
    return response()->json(['message' => 'API đang hoạt động!']);
});

// Core asset routes (api.php already loaded with /api prefix)
Route::post('assets', [AssetController::class, 'store']);
