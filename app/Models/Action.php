<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    public function user()
    {
        return $this->hasOne('App\Models\User');
    }
}
