<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Models\Asset;
use App\Models\Transfer;
use App\Services\AssetSnService;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    protected AssetSnService $snService;

    public function __construct(AssetSnService $snService)
    {
        $this->snService = $snService;
    }

    /**
     * POST /api/assets/{asset}/transfer
     * Chuyển tài sản sang phòng ban / vị trí khác
     */
    public function transfer(TransferRequest $request, $assetId)
    {
        $asset = Asset::findOrFail($assetId);
        $toDept = (int) $request->input('to_department_id');
        $toLoc  = (int) $request->input('to_location_id');

        // Kiểm tra nếu vẫn cùng phòng ban & vị trí
        if ($asset->department_id == $toDept && $asset->location_id == $toLoc) {
            return response()->json([
                'message' => 'Asset is already in the specified department/location'
            ], 422);
        }

        $oldSN   = $asset->asset_sn;
        $oldDept = $asset->department_id;
        $oldLoc  = $asset->location_id;

        return DB::transaction(function () use ($asset, $toDept, $toLoc, $oldSN, $oldDept, $oldLoc, $request) {
            // Tìm SN cũ từng dùng trong phòng ban đó
            $reuseSn = $this->snService->findPreviousSnForDept($asset->id, $toDept);

            $newSn = $reuseSn ?: $this->snService->generate($toDept, $asset->asset_group_id);

            // Lưu log chuyển giao
            $transfer = Transfer::create([
                'asset_id'           => $asset->id,
                'from_department_id' => $oldDept,
                'to_department_id'   => $toDept,
                'from_location_id'   => $oldLoc,
                'to_location_id'     => $toLoc,
                'old_sn'             => $oldSN,
                'new_sn'             => $newSn,
                'note'               => $request->input('note'),
            ]);

            // Cập nhật asset
            $asset->update([
                'department_id'     => $toDept,
                'location_id'       => $toLoc,
                'asset_sn'          => $newSn,
                'accountable_party' => $request->input('accountable_party', $asset->accountable_party),
                'description'       => $request->input('description', $asset->description),
            ]);

            return response()->json([
                'message'  => 'Asset transferred successfully',
                'asset'    => $asset->fresh()->load(['department', 'location', 'group', 'images']),
                'transfer' => $transfer,
            ], 200);
        });
    }
}