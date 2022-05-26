<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staffposition extends Model
{
    use HasFactory;

    public function compensationlevel()
    {
        return $this->hasOne('App\Models\Compensationlevel', 'id', 'compensationlevel_id');
    }

    public function program()
    {
        return $this->hasOne('App\Models\Program', 'id', 'progrram_id');
    }

    public function assigned()
    {
        return $this->hasMany('App\Models\YearattendingStaff');
    }
}
