<?php

namespace App\Http\Controllers;

use App\Enums\Buildingtype;
use App\Models\Building;
use App\Models\CampCost;
use App\Models\Program;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function campcost()
    {
        $rates = CampCost::whereIn('building_id', [Buildingtype::Trout, Buildingtype::Tent, Buildingtype::LakewoodCabin])->get()->groupBy('building_id');
        return view('campcost', ['lodge' => $rates[Buildingtype::Trout], 'tent' => $rates[Buildingtype::Tent], 'lakewood' => $rates[Buildingtype::LakewoodCabin]]);
    }

    public function housing()
    {
        return view('housing', ['buildings' => Building::whereNotNull('blurb')->get(), 'background' => 'housing.jpg']);
    }

    public function programs()
    {
        return view('programs', ['programs' => Program::whereNotNull('blurb')->orderBy('order')->get(), 'background' => 'programs.jpg']);
    }

    public function scholarship()
    {
        return view('scholarship', ['background' => 'scholarship.jpg']);
    }

    public function themespeaker()
    {
        return view('themespeaker', ['background' => 'biographies.jpg']);
    }
}
