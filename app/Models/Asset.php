<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_name',
        'asset_sn',
        'asset_group_id',
        'department_id',
        'location_id',
        'accountable_party',
        'description',
        'warranty_date',
    ];

    protected $dates = ['warranty_date', 'deleted_at'];

    public function group() {
        return $this->belongsTo(AssetGroup::class, 'asset_group_id');
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function transfers() {
        return $this->hasMany(Transfer::class);
    }

    public function images() {
        return $this->hasMany(AssetImage::class);
    }
}