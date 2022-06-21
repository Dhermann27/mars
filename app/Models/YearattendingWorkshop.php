<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearattendingWorkshop extends Model
{
    use HasFactory;

    protected $fillable = ['yearattending_id', 'workshop_id'];
    public $incrementing = false;
    protected $table = 'yearsattending__workshop';

    public function yearattending()
    {
        return $this->hasOne('App\Models\Yearattending', 'id', 'yearattending_id');
    }

    public function workshop()
    {
        return $this->hasOne('App\Models\Workshop', 'id', 'workshop_id');
    }
}
