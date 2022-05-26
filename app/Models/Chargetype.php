<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chargetype extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function charge()
    {
        return $this->belongsTo('App\Models\Charge');
    }

    public function byyearcharges() {
        return $this->hasMany('App\Models\ByyearCharge');
    }

    public function thisyearcharges() {
        return $this->hasMany('App\Models\ThisyearCharge');
    }

}
