<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TransferController;


Route::get('/test', function() {
    return response()->json(['message' => 'API đang hoạt động!']);
});

// Core asset routes (api.php already loaded with /api prefix)
Route::put('/assets/{asset}', [AssetController::class, 'update']);