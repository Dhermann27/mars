<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CamperStaff extends Model
{
    use HasFactory;
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'camper__staff';

    public function camper()
    {
        return $this->hasOne('App\Models\Camper');
    }

    public function staffposition()
    {
        return $this->hasOne('App\Models\Staffposition');
    }
}
