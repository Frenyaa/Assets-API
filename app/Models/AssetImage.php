<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetImage extends Model
{
    protected $fillable = ['asset_id', 'path', 'filename'];

    public function asset() {
        return $this->belongsTo(Asset::class);
    }

    // helper to get full URL
    public function url() {
        return asset('storage/' . $this->path);
    }
}