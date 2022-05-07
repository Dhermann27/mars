<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearattendingStaff extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'yearsattending__staff';

    public function yearsattending()
    {
        return $this->hasOne('App\Http\Yearattending', 'id', 'yearattending_id');
    }

    public function staffposition()
    {
        return $this->hasOne('App\Http\Staffposition', 'id', 'staffposition_id');
    }
}
