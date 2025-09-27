<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetImagesRequest;
use App\Models\Asset;
use App\Models\AssetImage;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * POST /api/assets/{asset}/images
     */
    public function uploadImages(AssetImagesRequest $request, Asset $asset)
    {
        $files  = $request->file('images', []);
        $images = $this->saveImages($asset, $files);

        return response()->json([
            'message' => 'Images uploaded successfully',
            'images'  => $images,
        ]);
    }

    protected function saveImages(Asset $asset, array $files)
    {
        $saved = [];

        foreach ($files as $file) {
            $path = $file->store("assets/{$asset->id}", 'public');

            $img = AssetImage::create([
                'asset_id' => $asset->id,
                'path'     => $path,
                'filename' => $file->getClientOriginalName(),
            ]);

            $saved[] = [
                'id'       => $img->id,
                'url'      => Storage::url($path),
                'filename' => $img->filename,
            ];
        }

        return $saved;
    }
}