<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'asset_name' => [
                'required', 'string', 'max:255',
                Rule::unique('assets', 'asset_name')->where(function ($query) {
                    return $query->where('location_id', $this->input('location_id'));
                }),
            ],
            'asset_sn'         => 'nullable|string|max:100|unique:assets,asset_sn',
            'asset_group_id'   => 'required|exists:asset_groups,id',
            'department_id'    => 'required|exists:departments,id',
            'location_id'      => 'required|exists:locations,id',
            'accountable_party'=> 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'warranty_date'    => 'nullable|date',
            'images'           => 'sometimes|array',
            'images.*'         => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}