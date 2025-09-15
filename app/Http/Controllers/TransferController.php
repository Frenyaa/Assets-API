<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Models\Asset;
use App\Models\Transfer;
use App\Services\AssetSnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    protected AssetSnService $snService;

    public function __construct(AssetSnService $snService)
    {
        $this->snService = $snService;
    }

    /**
     * POST /api/assets/{id}/transfer
     */
    public function transfer(TransferRequest $request, $assetId)
    {
        $asset = Asset::findOrFail($assetId);
        $toDept = (int) $request->input('to_department_id');
        $toLoc = (int) $request->input('to_location_id');

        // Validate not same department
        if ($asset->department_id == $toDept && $asset->location_id == $toLoc) {
            return response()->json(['message' => 'Asset is already in the specified department/location'], 422);
        }

        $oldSN = $asset->asset_sn;
        $oldDept = $asset->department_id;
        $oldLoc = $asset->location_id;

        return DB::transaction(function () use ($asset, $toDept, $toLoc, $oldSN, $oldDept, $oldLoc, $request) {
            // Try to reuse previous SN if asset had been in that dept before
            $reuseSn = $this->snService->findPreviousSnForDept($asset->id, $toDept);

            if ($reuseSn) {
                $newSn = $reuseSn;
            } else {
                // generate new sn for (toDept, asset_group_id)
                $newSn = $this->snService->generate($toDept, $asset->asset_group_id);
            }

            // create transfer log
            $transfer = Transfer::create([
                'asset_id' => $asset->id,
                'from_department_id' => $oldDept,
                'to_department_id' => $toDept,
                'from_location_id' => $oldLoc,
                'to_location_id' => $toLoc,
                'old_sn' => $oldSN,
                'new_sn' => $newSn,
                'note' => $request->input('note'),
            ]);

            // update asset
            $asset->department_id = $toDept;
            $asset->location_id = $toLoc;
            $asset->asset_sn = $newSn;
            if ($request->filled('accountable_party')) {
                $asset->accountable_party = $request->input('accountable_party');
            }
            if ($request->filled('description')) {
                $asset->description = $request->input('description');
            }
            $asset->save();

            return response()->json([
                'message' => 'Asset transferred successfully',
                'asset' => $asset->fresh()->load(['department','location','group','images']),
                'transfer' => $transfer,
            ], 200);
        });
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

        // check last 12 months
        $fromDate = now()->subMonths(12);
        $recent = Transfer::where('asset_id', $assetId)
            ->where('created_at', '>=', $fromDate)
            ->exists();

        if (!$recent && $transfers->isEmpty()) {
            return response()->json(['message' => 'No transfers in last 12 months.'], 200);
        }

        return response()->json([
            'history' => $transfers->map(function ($t) {
                return [
                    'transfer_date' => $t->created_at,
                    'from_department' => $t->fromDepartment ? $t->fromDepartment->name : null,
                    'old_asset_sn' => $t->old_sn,
                    'to_department' => $t->toDepartment ? $t->toDepartment->name : null,
                    'new_asset_sn' => $t->new_sn,
                    'note' => $t->note,
                ];
            })
        ]);
    }
}