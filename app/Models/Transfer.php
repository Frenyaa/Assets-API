<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'asset_id',
        'from_department_id',
        'to_department_id',
        'from_location_id',
        'to_location_id',
        'old_sn',
        'new_sn',
        'note',
    ];

    public function asset() {
        return $this->belongsTo(Asset::class);
    }

    public function fromDepartment() {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment() {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromLocation() {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation() {
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}