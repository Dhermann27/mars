<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = ['deposited_date'];

    public function camper()
    {
        return $this->hasOne('App\Http\Camper');
    }

    public function chargetype()
    {
        return $this->hasOne('App\Http\Chargetype');
    }
}
