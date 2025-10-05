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
     * GET /api/assets/{id}/transfers
     */
    public function history($assetId)
    {
        $transfers = Transfer::where('asset_id', $assetId)
            ->with(['fromDepartment','toDepartment','fromLocation','toLocation'])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($transfers->isEmpty()) {
            return response()->json(['message' => 'No transfer history found.'], 200);
        }

        return response()->json([
            'history' => $transfers->map(function ($t) {
                return [
                    'transfer_date'   => $t->created_at,
                    'from_department' => $t->fromDepartment ? $t->fromDepartment->name : null,
                    'old_asset_sn'    => $t->old_sn,
                    'to_department'   => $t->toDepartment ? $t->toDepartment->name : null,
                    'new_asset_sn'    => $t->new_sn,
                    'note'            => $t->note,
                ];
            }),
        ]);
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