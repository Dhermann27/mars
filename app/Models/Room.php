<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function building()
    {
        return $this->hasOne('App\Http\Building', 'id', 'building_id');
    }

    public function occupants()
    {
        return $this->hasMany('App\Http\ThisyearCamper', 'room_id', 'id');
    }

    public function yearsattending()
    {
        return $this->hasMany('App\Http\Yearsattending');
    }
}
