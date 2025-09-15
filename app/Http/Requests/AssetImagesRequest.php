<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetImagesRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}