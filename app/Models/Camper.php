<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camper extends Model
{
    use HasFactory;

    public function family()
    {
        return $this->hasOne(Family::class, 'id', 'family_id');
    }

    public function pronoun()
    {
        return $this->hasOne(Pronoun::class, 'id', 'pronoun_id');
    }

    public function foodoption()
    {
        return $this->hasOne(Foodoption::class);
    }

    public function church()
    {
        return $this->hasOne(Church::class, 'id', 'church_id');
    }

    public function getChurchNameAttribute() {
        if(isset($this->church)) {
            return $this->church->name . ' (' . $this->church->city . ', ' . $this->church->province->code . ')';
        } else {
            return '';
        }
    }

    public function yearsattending()
    {
        return $this->hasMany(Yearattending::class);
    }

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}
