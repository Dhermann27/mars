<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartdataVeryoldcampers extends Model
{
    public function yearattending()
    {
        return $this->hasOne(Yearattending::class, 'id', 'yearattending_id');
    }
}
