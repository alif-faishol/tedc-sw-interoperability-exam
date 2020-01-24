<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitGroup extends Model
{
    protected $fillable = ['name', 'description', 'hourly_price', 'available_to_public', 'property_id'];

    public function units() {
        return $this->hasMany(Unit::class, 'unit_group_id');
    }

    public function property() {
        return $this->belongsTo(Property::class, 'property_id');
    }
}

?>
