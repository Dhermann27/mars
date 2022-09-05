<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ByyearFamily extends Model
{
    protected $table = "byyear_families";

    public function campers()
    {
        return $this->hasMany(Camper::class, 'family_id', 'id');
    }

    public function byyearcampers()
    {
        return $this->hasMany(ByyearCamper::class, 'family_id', 'id');
    }

    public function getFormattedYearsAttribute()
    {
        $years = explode(",", $this->years);
        $yearstring = "";
        if (in_array('2019', $years) && in_array('2021', $years)) array_push($years, '2020');
        sort($years);
        $i = 0;
        while ($i < count($years)) {
            if ($i != 0)
                $yearstring .= ", ";
            $rangestart = $i;
            $yearstring .= $years[$i++];
            while ($i < count($years) && $years[$i] == $years[$i - 1] + 1)
                $i++;
            if ($i > $rangestart + 1)
                $yearstring .= "&ndash;" . $years[$i - 1];
        }
        return $yearstring;
    }


}
