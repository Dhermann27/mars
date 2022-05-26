<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compensationlevel extends Model
{
    use HasFactory;

    public function staffposition()
    {
        return $this->belongsTo('App\Models\Staffposition');
    }

}
