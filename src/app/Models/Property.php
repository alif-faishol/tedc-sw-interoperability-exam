<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['name', 'description', 'owner_user_id'];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}

?>
