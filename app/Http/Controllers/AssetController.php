<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * POST /api/assets
     * Body: asset_name, asset_group_id, department_id, location_id, description, warranty_date
     */
    public function store(Request $request)
    {
        // validate input
        $validated = $request->validate([
            'asset_name'     => 'required|string|max:255',
            'asset_sn'       => 'required|string|max:100|unique:assets,asset_sn',
            'asset_group_id' => 'required|integer',
            'department_id'  => 'required|integer',
            'location_id'    => 'nullable|integer',
            'accountable_party' => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'warranty_date'  => 'nullable|date',
        ]);

        // create asset
        $asset = Asset::create($validated);

        // return response
        return response()->json([
            'message' => 'Asset created successfully',
            'data'    => $asset,
        ], 201);
    }
}