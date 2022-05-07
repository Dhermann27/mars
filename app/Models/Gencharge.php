<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gencharge extends Model
{
    public $timestamps = false;

    public function camper()
    {
        return $this->hasOne(Camper::class);
    }

    public function chargetype()
    {
        return $this->hasOne(Chargetype::class);
    }
}
