<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicalresponse extends Model
{
    public function yearattending()
    {
        return $this->hasOne('App\Http\Yearattending');
    }
}
