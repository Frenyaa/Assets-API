<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

class AssetSnService
{
    /**
     * Generate next SN for given department and asset group.
     * Format: dd/gg/nnnn
     */
    public function generate(int $departmentId, int $assetGroupId): string
    {
        return DB::transaction(function () use ($departmentId, $assetGroupId) {
            $dd = str_pad($departmentId, 2, '0', STR_PAD_LEFT);
            $gg = str_pad($assetGroupId, 2, '0', STR_PAD_LEFT);
            $prefix = "{$dd}/{$gg}/";

            // Tìm asset_sn lớn nhất có prefix này
            $last = Asset::where('department_id', $departmentId)
                ->where('asset_group_id', $assetGroupId)
                ->where('asset_sn', 'like', $prefix . '%')
                ->lockForUpdate() // tránh race condition
                ->orderBy('asset_sn', 'desc')
                ->first();

            $nextNumber = 1;
            if ($last) {
                $lastNumber = (int) substr($last->asset_sn, -4);
                $nextNumber = $lastNumber + 1;
            }

            $nnnn = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            return "{$dd}/{$gg}/{$nnnn}";
        });
    }

    /**
     * Try to find a previous SN for this asset in the given department (to reuse).
     * Returns string SN or null.
     */
    public function findPreviousSnForDept(int $assetId, int $toDepartmentId): ?string
    {
        $transfer = Transfer::where('asset_id', $assetId)
            ->where('to_department_id', $toDepartmentId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $transfer ? $transfer->new_sn : null;
    }
}