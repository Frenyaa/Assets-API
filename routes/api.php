<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransferController;

Route::get('/test', function() {
    return response()->json(['message' => 'API đang hoạt động!']);
});

// images & transfer
Route::post('assets/{asset}/transfer', [TransferController::class, 'transfer']);