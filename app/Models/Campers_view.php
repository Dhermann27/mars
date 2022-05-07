<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campers_view extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'campers_view';

    public function family()
    {
        return $this->hasOne('App\Http\Family');
    }

    public function pronoun()
    {
        return $this->hasOne('App\Http\Pronoun');
    }

    public function foodoption()
    {
        return $this->hasOne('App\Http\Foodoption');
    }

    public function church()
    {
        return $this->hasOne('App\Http\Church', 'id', 'church_id');
    }

    public function yearsattending()
    {
        return $this->hasMany('App\Http\Yearattending');
    }
}
