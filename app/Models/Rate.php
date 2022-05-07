<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    public function building()
    {
        return $this->hasOne('App\Http\Building');
    }

    public function program()
    {
        return $this->hasOne('App\Http\Program');
    }
}
