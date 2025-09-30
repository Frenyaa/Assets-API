<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * GET /api/assets
     * Filters: asset_name, asset_sn, department_id, asset_group_id, warranty_from, warranty_to, include_deleted, page, per_page
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);

        $query = Asset::query()->with(['group', 'department', 'location']);

        // include deleted (soft delete)
        if ($request->boolean('include_deleted')) {
            $query->withTrashed();
        }

        // filters
        if ($request->filled('asset_name')) {
            $query->where('asset_name', 'like', "%{$request->asset_name}%");
        }

        if ($request->filled('asset_sn')) {
            $query->where('asset_sn', 'like', "%{$request->asset_sn}%");
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('asset_group_id')) {
            $query->where('asset_group_id', $request->asset_group_id);
        }

        if ($request->filled('warranty_from') && $request->filled('warranty_to')) {
            $query->whereBetween('warranty_date', [
                $request->warranty_from,
                $request->warranty_to,
            ]);
        }

        // pagination
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page'   => $paginated->currentPage(),
                'per_page'       => $paginated->perPage(),
                'returned_count' => $paginated->count(),
                'total_count'    => $paginated->total(),
            ],
        ]);
    }
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