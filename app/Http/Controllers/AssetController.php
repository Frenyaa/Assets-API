<?php

namespace App\Http\Controllers;

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
}