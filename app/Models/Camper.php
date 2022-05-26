<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camper extends Model
{
    use HasFactory;

    public function family()
    {
        return $this->hasOne('App\Models\Family', 'id', 'family_id');
    }

    public function pronoun()
    {
        return $this->hasOne('App\Models\Pronoun', 'id', 'pronoun_id');
    }

    public function foodoption()
    {
        return $this->hasOne('App\Models\Foodoption');
    }

    public function church()
    {
        return $this->hasOne('App\Models\Church', 'id', 'church_id');
    }

    public function yearsattending()
    {
        return $this->hasMany('App\Models\Yearattending');
    }

    public function charges()
    {
        return $this->hasMany('App\Models\Charge');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'email', 'email');
    }
}
