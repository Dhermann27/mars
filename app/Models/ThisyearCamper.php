<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThisyearCamper extends Model
{
    protected $table = "thisyear_campers";

    public function church()
    {
        return $this->hasOne(Church::class, 'id', 'church_id');
    }

    public function family()
    {
        return $this->hasOne(Family::class, 'id', 'family_id');
    }

    public function foodoption()
    {
        return $this->hasOne(Foodoption::class, 'id', 'foodoption_id');
    }

    public function medicalresponse()
    {
        return $this->hasOne(Medicalresponse::class, 'yearattending_id', 'yearattending_id');
    }

    public function program()
    {
        return $this->hasOne(Program::class, 'id', 'program_id');
    }

    public function pronoun()
    {
        return $this->hasOne(Pronoun::class, 'id', 'pronoun_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function yearattending()
    {
        return $this->hasOne(Yearattending::class, 'id', 'yearattending_id');
    }

    public function yearsattending()
    {
        return $this->hasMany(Yearattending::class, 'camper_id', 'id')
            ->orderBy('year_id', 'desc');
    }

    public function getFormattedPhoneAttribute()
    {
        if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $this->phonenbr, $matches)) {
            $result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
            return $result;
        }
        return "";
    }

    public function parents()
    {
        return $this->hasMany(ThisyearCamper::class, 'family_id', 'family_id')
            ->where('age', '>', '17')->orderBy('birthdate');
    }

    public function getEachCalendarAttribute()
    {
        $cal = explode(';', $this->program->calendar);
        switch (count($cal)) {
            case 3:
                if ($this->age < 8) {
                    return $cal[0];
                } elseif ($this->age > 9) {
                    return $cal[2];
                } else {
                    return $cal[1];
                }
                break;

            case 2:
                return $this->age > 3 ? $cal[1] : $cal[0];
                break;

            default:
                return $cal[0];
        }
    }

    public function getNametagBackAttribute()
    {
        switch ($this->programid) {
            case 1001:
                return "Leader: ________________________________<br /><br />________________________________<br />________________________________<br />________________________________<br />________________________________<br />________________________________<br />________________________________";
                break;
            case 1002:
            case 1007:
                $parents = "";
                foreach ($this->family->campers()->where('age', '>', '17')->orderBy('birthdate')->get() as $parent) {
                    $parents .= "<u>" . $parent->firstname . " " . $parent->lastname . "</u><br />";
                    $parents .= "Room: " . $parent->buildingname . " " . $parent->room_number . "<br />";
                    if (count($parent->yearattending->workshops()->where('is_enrolled', '1')->get()) > 0) {
                        foreach ($parent->yearattending->workshops()->where('is_enrolled', '1')->get() as $workshop) {
                            if ($workshop->workshop->timeslotid == 1001 || $workshop->workshop->timeslotid == 1002) {
                                $parents .= $workshop->workshop->timeslot->name . " (" . $workshop->workshop->display_days . ") " . $workshop->workshop->room->room_number . "<br />";
                            }
                        }
                    }
                }
                return $parents;
                break;
            default:
                $workshops = "";
                if (count($this->yearattending->workshops()->where('is_enrolled', '1')->get()) > 0) {
                    foreach ($this->yearattending->workshops()->where('is_enrolled', '1')->get() as $workshop) {
                        $workshops .= $workshop->workshop->timeslot->name . " (" . $workshop->workshop->display_days . "): " . $workshop->workshop->name . " in " . $workshop->workshop->room->room_number . "<br />";
                    }
                }
                return $workshops;
                break;
        }
    }
}
