<?php

namespace App\Http\Controllers;

use App\Models\Asset;

class AssetController extends Controller
{
    /**
     * DELETE /api/assets/{asset}
     * Soft delete tài sản
     */
    public function destroy(Asset $asset)
    {
        $asset->delete(); // soft delete
        return response()->json(null, 204);
    }
}