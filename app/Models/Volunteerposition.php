<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteerposition extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function yearsattending()
    {
        return $this->belongsToMany('App\Http\Yearattending')->using('App\Http\YearattendingVolunteer');
    }
}
