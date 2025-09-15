<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $assetId = $this->route('asset') ? $this->route('asset')->id : null;
        return [
            'asset_name' => [
                'sometimes', 'string', 'max:255',
                Rule::unique('assets', 'asset_name')
                    ->where(function ($query) {
                        return $query->where('location_id', $this->input('location_id', $this->route('asset') ? $this->route('asset')->location_id : null));
                    })->ignore($assetId)
            ],
            // Disallow changing department_id/asset_group_id via update (use transfer)
            'accountable_party' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'warranty_date' => 'nullable|date',
        ];
    }
}