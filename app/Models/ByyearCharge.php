<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ByyearCharge extends Model
{
    protected $table = "byyear_charges";

    public function camper()
    {
        return $this->hasOne(Camper::class, 'id', 'camper_id');
    }

    public function children()
    {
        return $this->hasMany(Charge::class, 'parent_id', 'id');
    }
}
