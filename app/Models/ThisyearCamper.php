<?php

namespace App\Models;

use App\Enums\Programname;
use App\Enums\Pronounname;
use App\Enums\Timeslotname;
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

    public function staffpositions()
    {
        return $this->hasManyThrough(Staffposition::class, YearattendingStaff::class,
            'yearattending_id', 'id', 'id', 'staffposition_id');
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
        return $this->hasManyThrough(Yearattending::class, ParentsChildExpo::class,
            'child_yearattending_id', 'id', 'yearattending_id', 'parent_yearattending_id');
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
        switch ($this->program_id) {
            case Programname::Meyer:
                return "Leader: _________________________<br />________________________________<br />________________________________<br />________________________________<br />________________________________<br />________________________________<br />________________________________";
            case Programname::Cratty:
            case Programname::Lumens:
                $parents = "";
                $pyas = $this->parents->sortBy('camper.birthdate');
                if (count($pyas) == 2) {
                    if (($pyas[0]->camper->pronoun_id == Pronounname::HeHim && $pyas[1]->camper->pronoun_id == Pronounname::SheHer)
                        || ($pyas[1]->camper->pronoun_id == Pronounname::HeHim && $pyas[0]->camper->pronoun_id == Pronounname::SheHer)) {
                        $icon = '<i class="fas fa-family"></i>';
                    } elseif ($pyas[0]->camper->pronoun_id == Pronounname::SheHer && $pyas[1]->camper->pronoun_id == Pronounname::SheHer) {
                        $icon = '<i class="fas fa-family-dress"></i>';
                    } else {
                        $icon = '<i class="fas fa-family-pants"></i>';
                    }
                } elseif (count($pyas) == 1) {
                    $icon = '<span class="fa-layers">
                                <i class="fas fa-person" data-fa-transform="grow-10 left-7 down-1" style="color: darkgray"></i>
                                <i class="fas fa-child" data-fa-transform="down-6"></i>
                            </span>';
                    if ($pyas[0]->camper->pronoun_id == Pronounname::SheHer) {
                        $icon = preg_replace('/fa-person/', 'fa-person-dress', $icon);
                    }
                } elseif (count($pyas) == 0) {
                    return "SPONSOR NEEDED";
                } else {
                    $icon = '<i class="fas fa-people-group"></i>';
                }
                foreach ($pyas as $pya) {
                    $parents .= $icon . " " . $pya->camper->firstname . " " . $pya->camper->lastname;
                    if (isset($pya->room)) {
                        $parents .= " (Room: " . $pya->room->building->buildingname . " " . $pya->room->room_number . ")<br />";
                    } else {
                        $parents .= " (NO ROOM ASSIGNED)<br />";
                    }
                    $yws = YearattendingWorkshop::where('yearattending_id', $pya->id)
                        ->where('is_enrolled', '1')->with('workshop.timeslot')->get();
                    if (count($yws) > 0) {
                        foreach ($yws as $yw) {
                            if ($yw->workshop->timeslot_id == Timeslotname::Morning || $yw->workshop->timeslot_id == Timeslotname::Early_Afternoon) {
                                $parents .= $yw->workshop->timeslot->name . " (" . $yw->workshop->display_days . "): " . $yw->workshop->name . " in " . $yw->workshop->room->room_number . "<br />";
                            }
                        }
                    }
                }
                return $parents;
                break;
            default:
                $workshops = "";
                $yws = YearattendingWorkshop::where('yearattending_id', $this->yearattending_id)
                    ->where('is_enrolled', '1')->with('workshop.timeslot')->get();
                if (count($yws) > 0) {
                    foreach ($yws as $yw) {
                        $workshops .= $yw->workshop->timeslot->name . " (" . $yw->workshop->display_days . "): " . $yw->workshop->name . " in " . $yw->workshop->room->room_number . "<br />";
                    }
                }
                return $workshops;
                break;
        }
    }
}
