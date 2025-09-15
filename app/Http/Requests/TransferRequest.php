<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'to_department_id' => 'required|exists:departments,id',
            'to_location_id' => 'required|exists:locations,id',
            'accountable_party' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
        ];
    }
}