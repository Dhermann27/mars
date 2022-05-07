<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartdataLostcampers extends Model
{
    public function camper()
    {
        return $this->hasOne('App\Http\Camper', 'id', 'camper_id');
    }
}
