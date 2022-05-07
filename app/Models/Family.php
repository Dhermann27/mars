<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'families';

    public function province()
    {
        return $this->hasOne('App\Http\Province', 'id', 'province_id');
    }

    public function campers()
    {
        return $this->hasMany('App\Http\Camper');
    }
}
