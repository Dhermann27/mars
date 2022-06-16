<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomselectionExpo extends Model
{
    protected $table = "roomselection_expo";
    protected $fillable = ['created_at'];

    public function room()
    {
        return $this->hasOne('App\Models\Room', 'id', 'room_id');
    }
}
