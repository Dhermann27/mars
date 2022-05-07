<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    protected $dates = ['start_time', 'end_time'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function workshops()
    {
        return $this->hasMany('App\Models\Workshop');
    }

}
