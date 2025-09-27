<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
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
}