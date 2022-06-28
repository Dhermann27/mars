<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentsChildExpo extends Model
{
    protected $table = "parents__child_expo";

    public function childyearattending()
    {
        return $this->hasOne(Yearattending::class, 'id', 'child_yearattending_id');
    }

    public function parentyearattending()
    {
        return $this->hasMany(Yearattending::class, 'id', 'parent_yearattending_id');
    }
}
