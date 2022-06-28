<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Yearattending extends Model
{
    use HasFactory;

    protected $table = 'yearsattending';
    protected $fillable = ['camper_id', 'year_id', 'room_id'];

    public function camper()
    {
        return $this->hasOne('App\Models\Camper', 'id', 'camper_id');
    }

    public function thisyearcamper()
    {
        return $this->hasOne('App\Models\ThisyearCamper');
    }

    public function program()
    {
        return $this->hasOne('App\Models\Program', 'id', 'program_id');
    }

    public function room()
    {
        return $this->hasOne('App\Models\Room', 'id', 'room_id');
    }

    public function year()
    {
        return $this->hasOne('App\Models\Year', 'id', 'year_id');
    }

    public function staffpositions()
    {
        return $this->hasManyThrough('App\Models\Staffposition', 'App\Models\YearattendingStaff',
            'yearattending_id', 'id', 'id', 'staffposition_id');
    }

    public function workshops()
    {
        return $this->hasManyThrough('App\Models\Workshop', 'App\Models\YearattendingWorkshop',
            'yearattending_id', 'id', 'id', 'workshop_id');
    }

    /*
     * Default: 222215521
     * Pos0 Display pronouns (2)
     * Pos1 Name Display: First Last (2) First then Last (1) First only (4)
     * Pos2 Namesize (1-5) .5x+.3em
     * Pos3 Line 1 Church (1)
     * Pos4 Line 2 Hometown (2)
     * Pos5 Line 3 PC Position (3)
     * Pos6 Line 4 Newcamper (4) Nothing (5)
     * Pos7 Font apply to name (1)
     * Pos8 Font TODO: Expose in nametag_expo
     */
    public function getPronounValueAttribute()
    {
        return $this->camper->pronoun->name;
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
                return "Indie Flower";
                break;
            case "3":
                return "Fredericka the Great";
                break;
            case "4":
                return "Mystery Quest";
                break;
            case "5":
                return "Great Vibes";
                break;
            case "6":
                return "Bangers";
                break;
            case "7":
                return "Comic Sans MS";
                break;
            default:
                return "Jost";
        }
    }

    public function getFontAttribute()
    {
        return substr($this->nametag, 8, 1);
    }

    public function getFirsttimeAttribute() {
        return Yearattending::where('camper_id', $this->camper_id)->count() == 1;
    }
}
