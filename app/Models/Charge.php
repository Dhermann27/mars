<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = ['camper_id', 'chargetype_id', 'memo', 'amount', 'year_id', 'timestamp', 'deposited_date',
        'parent_id'];

    public function camper()
    {
        return $this->hasOne(Camper::class);
    }

    public function chargetype()
    {
        return $this->hasOne(Chargetype::class);
    }
}
