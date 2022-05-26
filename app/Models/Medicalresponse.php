<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicalresponse extends Model
{
    use HasFactory;

    public function yearattending()
    {
        return $this->hasOne('App\Models\Yearattending');
    }
}
