<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Requests\AssetImagesRequest;
use App\Models\Asset;
use App\Models\AssetImage;
use App\Services\AssetSnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    protected AssetSnService $snService;

    public function __construct(AssetSnService $snService)
    {
        $this->snService = $snService;
    }

    /**
     * GET /api/assets
     * Filters (query): asset_name, asset_sn, department_id, asset_group_id, warranty_from, warranty_to, include_deleted, page, per_page
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $query = Asset::query()->with(['group', 'department', 'location']);

        if ($request->boolean('include_deleted')) {
            $query = $query->withTrashed();
        }

        if ($request->filled('asset_name')) {
            $query->where('asset_name', 'like', '%' . $request->asset_name . '%');
        }
        if ($request->filled('asset_sn')) {
            $query->where('asset_sn', 'like', '%' . $request->asset_sn . '%');
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('asset_group_id')) {
            $query->where('asset_group_id', $request->asset_group_id);
        }
        if ($request->filled('warranty_from') && $request->filled('warranty_to')) {
            $query->whereBetween('warranty_date', [$request->warranty_from, $request->warranty_to]);
        }

        $paginated = $query->paginate($perPage);

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'returned_count' => $paginated->count(),
                'total_count' => $paginated->total(),
            ],
        ]);
    }

    /**
     * GET /api/assets/{id}
     */
    public function show($id)
    {
        $asset = Asset::withTrashed()->with(['group', 'department', 'location', 'images', 'transfers'])->findOrFail($id);

        // convert images to URLs
        $asset->images->transform(function ($img) {
            $img->url = Storage::url($img->path);
            return $img;
        });

        return response()->json($asset);
    }

    /**
     * POST /api/assets
     */
    public function store(StoreAssetRequest $request)
    {
        $data = $request->validated();

        $asset = DB::transaction(function () use ($data) {
            // generate SN
            $sn = $this->snService->generate($data['department_id'], $data['asset_group_id']);
            $data['asset_sn'] = $sn;

            // create asset
            $asset = Asset::create([
                'asset_name' => $data['asset_name'],
                'asset_sn' => $data['asset_sn'],
                'asset_group_id' => $data['asset_group_id'],
                'department_id' => $data['department_id'],
                'location_id' => $data['location_id'],
                'accountable_party' => $data['accountable_party'] ?? null,
                'description' => $data['description'] ?? null,
                'warranty_date' => $data['warranty_date'] ?? null,
            ]);

            return $asset;
        });

        // handle images if any (outside transaction for simplicity)
        if ($request->has('images')) {
            $this->saveImages($asset, $request->file('images', []));
        }

        return response()->json($asset->load(['group', 'department', 'location', 'images']), 201);
    }

    /**
     * PUT /api/assets/{asset}
     */
    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $data = $request->validated();

        // prevent changing dept/group via update (use transfer)
        unset($data['department_id'], $data['asset_group_id'], $data['location_id']);

        $asset->update($data);

        return response()->json($asset->fresh()->load(['group','department','location','images']));
    }

    /**
     * DELETE /api/assets/{asset}
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(null, 204);
    }

    /**
     * POST /api/assets/{asset}/images
     */
    public function uploadImages(AssetImagesRequest $request, Asset $asset)
    {
        $files = $request->file('images', []);
        $images = $this->saveImages($asset, $files);

        return response()->json([
            'message' => 'Images uploaded',
            'images' => $images,
        ]);
    }

    /**
     * helper to save images array for an asset
     */
    protected function saveImages(Asset $asset, array $files)
    {
        $saved = [];
        foreach ($files as $file) {
            $path = $file->store("assets/{$asset->id}", 'public'); // storage/app/public/assets/{id}
            $img = AssetImage::create([
                'asset_id' => $asset->id,
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
            ]);
            $saved[] = [
                'id' => $img->id,
                'url' => Storage::url($path),
                'filename' => $img->filename,
            ];
        }
        return $saved;
    }
}