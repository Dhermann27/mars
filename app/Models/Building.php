<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function getImageArrayAttribute()
    {
        return explode(';', $this->image);
    }
}
