<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedEventTicket extends Model
{
    protected $fillable = [
        'unit_id',
        'event_id',
        'user_id'
    ];

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}

?>
