<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearattendingStaff extends Model
{
    use HasFactory;
    protected $fillable = ['yearattending_id', 'staffposition_id'];

    public $incrementing = false;
    protected $table = 'yearsattending__staff';

    public function yearsattending()
    {
        return $this->hasOne(Yearattending::class, 'id', 'yearattending_id');
    }

    public function staffposition()
    {
        return $this->hasOne(Staffposition::class, 'id', 'staffposition_id');
    }
}
