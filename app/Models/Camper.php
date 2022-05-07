<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camper extends Model
{
    use HasFactory;

    public function family()
    {
        return $this->hasOne('App\Http\Family', 'id', 'family_id');
    }

    public function pronoun()
    {
        return $this->hasOne('App\Http\Pronoun', 'id', 'pronoun_id');
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

    public function charges()
    {
        return $this->hasMany('App\Http\Charge');
    }

    public function user()
    {
        return $this->hasOne('App\Http\User', 'email', 'email');
    }
}
