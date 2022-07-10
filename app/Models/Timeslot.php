<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        return $this->hasMany(Workshop::class);
    }

    public function thisyearWorkshops()
    {
        return $this->hasMany(Workshop::class)->where('year_id', DB::raw('getcurrentyear()'));
    }

}
