<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize()
    {
        return true; // cho phép tất cả request, có thể chỉnh lại theo policy
    }

    public function rules()
    {
        // lấy id asset hiện tại (để bỏ qua unique check của chính nó khi update)
        $assetId = $this->route('asset') ? $this->route('asset')->id : null;

        return [
            // Asset name phải unique trong cùng location
            'asset_name' => [
                'sometimes', 'string', 'max:255',
                Rule::unique('assets', 'asset_name')
                    ->where(function ($query) {
                        return $query->where('location_id', $this->input(
                            'location_id',
                            $this->route('asset') ? $this->route('asset')->location_id : null
                        ));
                    })->ignore($assetId)
            ],

            // Asset SN unique toàn bảng
            'asset_sn' => [
                'sometimes', 'string', 'max:100',
                Rule::unique('assets', 'asset_sn')->ignore($assetId)
            ],

            // Không cho phép đổi department_id hoặc asset_group_id ở đây (dùng API transfer)
            'accountable_party' => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'warranty_date'     => 'nullable|date',
        ];
    }
}