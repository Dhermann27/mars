<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    public function building()
    {
        return $this->hasOne(Building::class);
    }

    public function program()
    {
        return $this->hasOne(Program::class);
    }
}
