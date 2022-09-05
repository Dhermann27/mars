<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThisyearFamily extends Model
{
    protected $table = 'thisyear_families';

    public function campers()
    {
        return $this->hasMany(Camper::class, 'family_id', 'id');
    }

    public function family()
    {
        return $this->hasOne(Family::class, 'id', 'id');
    }

    public function thisyearcampers()
    {
        return $this->hasMany(ThisyearCamper::class, 'family_id', 'id');
    }
}
