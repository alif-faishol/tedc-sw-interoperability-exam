<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['name', 'description', 'ticket_price', 'start_datetime', 'end_datetime', 'tenant_user_id'];

    public function tenant() {
        return $this->belongsTo(User::class, 'tenant_user_id');
    }

    public function unitGroups() {
        return $this->belongsToMany(UnitGroup::class, 'events_unit_groups');
    }
}

?>
