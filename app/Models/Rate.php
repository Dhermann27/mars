<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    public function building()
    {
        return $this->hasOne('App\Models\Building');
    }

    public function program()
    {
        return $this->hasOne('App\Models\Program');
    }
}
