<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomselectionExpo extends Model
{
    protected $table = "roomselection_expo";
    protected $fillable = ['created_at'];

    public function room()
    {
        return $this->hasOne(Room::class, 'id', 'room_id');
    }
}
