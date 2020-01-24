<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'description',
        'property_id',
        'unit_group_id',
        'event_only',
        'hourly_price',
        'daily_price',
        'monthly_price',
        'yearly_price',
        'available_to_public'
    ];

    public function property() {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function unit_group() {
        return $this->belongsTo(UnitGroup::class, 'unit_group_id');
    }
}

?>
