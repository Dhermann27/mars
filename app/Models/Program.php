<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    public function thisyearcampers()
    {
        return $this->hasMany(ThisyearCamper::class, 'program_id', 'id')
            ->orderBy('lastname')->orderBy('firstname');
    }

    public function staffpositions()
    {
        return $this->hasMany(Staffposition::class, 'program_id', 'id');
    }

    public function yearsattending()
    {
        return $this->hasMany(Yearattending::class);
    }
}
