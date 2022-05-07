<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThisyearCharge extends Model
{
    protected $table = "thisyear_charges";

    public function camper()
    {
        return $this->hasOne(Camper::class, 'id', 'camper_id');
    }

    public function children()
    {
        return $this->hasMany(Charge::class, 'parent_id', 'id');
    }
}
