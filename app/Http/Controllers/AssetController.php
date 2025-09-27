<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;

class AssetController extends Controller
{
    /**
     * PUT /api/assets/{asset}
     * Update asset (không cho đổi department, group, location ở đây)
     */
    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $data = $request->validated();

        unset($data['department_id'], $data['asset_group_id'], $data['location_id']);

        // Cập nhật asset
        $asset->update($data);

        return response()->json([
            'message' => 'Asset updated successfully',
            'data'    => $asset->fresh(),
        ]);
    }
}