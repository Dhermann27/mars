<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Yearattending extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'yearsattending';

    protected $fillable = ['camper_id', 'year_id'];

    public function camper()
    {
        return $this->hasOne('App\Http\Camper', 'id', 'camper_id');
    }

    public function thisyearcamper()
    {
        return $this->hasOne('App\Http\ThisyearCamper');
    }

    public function program()
    {
        return $this->hasOne('App\Http\Program', 'id', 'program_id');
    }

    public function room()
    {
        return $this->hasOne('App\Http\Room', 'id', 'room_id');
    }

    public function year()
    {
        return $this->hasOne('App\Http\Year', 'id', 'year_id');
    }

    public function staffpositions()
    {
        return $this->hasManyThrough('App\Http\Staffposition', 'App\YearattendingStaff',
            'yearattending_id', 'id', 'id', 'staffposition_id');
    }

    public function volunteerpositions()
    {
        return $this->belongsToMany('App\Http\Volunteerposition')->using('App\Http\YearattendingVolunteer');
    }

    public function workshops()
    {
        return $this->hasManyThrough('App\Http\Workshop', 'App\Http\YearattendingWorkshop',
            'yearattending_id', 'id', 'id', 'workshop_id');
    }

    public function getPronounValueAttribute()
    {
        return $this->getPronounAttribute() == '2' ? $this->camper->pronoun->name : "";
    }

    public function getPronounAttribute()
    {
        return substr($this->nametag, 0, 1);
    }

    public function getNameValueAttribute()
    {
        switch ($this->getNameAttribute()) {
            case "1":
            case "4":
                return $this->camper->firstname;
                break;
            default:
                return $this->camper->firstname . " " . $this->camper->lastname;

        }
    }

    public function getNameAttribute()
    {
        return substr($this->nametag, 1, 1);
    }

    public function getSurnameValueAttribute()
    {
        switch ($this->getNameAttribute()) {
            case "1":
                return $this->camper->lastname;
                break;
            default:
                return "";
        }
    }

    public function getLine1ValueAttribute()
    {
        return $this->getLine($this->getLine1Attribute());
    }

    private function getLine($i)
    {
        switch ($i) {
            case "1":
                return $this->camper->church ? $this->camper->church->name : '';
                break;
            case "2":
                return $this->camper->family->city . ", " . $this->camper->family->province->code;
                break;
            case "3":
                return $this->staffpositions->first()->name;
                break;
            case "4":
                return "First-time Camper";
                break;
            default:
                return "";
        }
    }

    public function getLine1Attribute()
    {
        return substr($this->nametag, 3, 1);
    }

    public function getNamesizeAttribute()
    {
        return substr($this->nametag, 2, 1);
    }

    public function getLine2ValueAttribute()
    {
        return $this->getLine($this->getLine2Attribute());
    }

    public function getLine2Attribute()
    {
        return substr($this->nametag, 4, 1);
    }

    public function getLine3ValueAttribute()
    {
        return $this->getLine($this->getLine3Attribute());
    }

    public function getLine3Attribute()
    {
        return substr($this->nametag, 5, 1);
    }

    public function getLine4ValueAttribute()
    {
        return $this->getLine($this->getLine4Attribute());
    }

    public function getLine4Attribute()
    {
        return substr($this->nametag, 6, 1);
    }

    public function getFontapplyAttribute()
    {
        return substr($this->nametag, 7, 1);
    }

    public function getFontValueAttribute()
    {
        switch ($this->getFontAttribute()) {
            case "2":
                return "indie";
                break;
            case "3":
                return "ftg";
                break;
            case "4":
                return "quest";
                break;
            case "5":
                return "vibes";
                break;
            case "6":
                return "bangers";
                break;
            case "7":
                return "comic";
                break;
            default:
                return "opensans";
        }
    }

    public function getFontAttribute()
    {
        return substr($this->nametag, 8, 1);
    }
}
