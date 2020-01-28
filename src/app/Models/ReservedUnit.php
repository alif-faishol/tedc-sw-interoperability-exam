<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedUnit extends Model
{
    protected $fillable = [
        'tenant_user_id',
        'unit_id',
        'start_datetime',
        'end_datetime'
    ];

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function tenant() {
        return $this->belongsTo(User::class, 'tenant_user_id');
    }
}

?>
