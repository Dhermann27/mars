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
        return $this->hasOne(Family::class);
    }

    public function pronoun()
    {
        return $this->hasOne(Pronoun::class);
    }

    public function foodoption()
    {
        return $this->hasOne(Foodoption::class);
    }

    public function church()
    {
        return $this->hasOne(Church::class, 'id', 'church_id');
    }

    public function yearsattending()
    {
        return $this->hasMany(Yearattending::class);
    }
}
