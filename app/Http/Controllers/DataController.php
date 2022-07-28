<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DataController extends Controller
{
    public function campers(Request $request)
    {
        $this->validate($request, ['term' => 'required|between:3,50']);
        $campers = Camper::select('campers.id', 'campers.firstname', 'campers.lastname', 'campers.email', 'families.city', 'provinces.code')
            ->join('families', 'campers.family_id', 'families.id')
            ->join('provinces', 'families.province_id', 'provinces.id')
            ->where('campers.firstname', 'LIKE', '%' . $request->term . '%')
            ->orWhere('campers.lastname', 'LIKE', '%' . $request->term . '%')
            ->orWhere('campers.email', 'LIKE', '%' . $request->term . '%')->get();
        foreach ($campers as $camper) {
            $camper->term = $request->term;
        }
        return $campers;
    }

    public function churches(Request $request)
    {
        $this->validate($request, ['term' => 'required | between:3,50']);
        $churches = Church::select('id', 'name', 'city', 'province_id')->where('name', 'LIKE', '%' . $request->term . '%')
            ->orWhere('city', 'LIKE', '%' . $request->term . '%')->with('province')->get();
        foreach ($churches as $church) {
            $church->term = $request->term;
        }
        return $churches;
    }

//    public function loginsearch(Request $request)
//    {
//        $this->validate($request, ['term' => 'required | email']);
//        $camper = Camper::where('email', $request->term)->firstOrFail();
//        $campers = Camper::select('id', 'firstname', 'lastname')->where('family_id', $camper->family_id)->orderBy('birthdate')->get();
//        return $campers;
//    }
//
//    public function steps($id = null)
//    {
//        if ($id != null && $id == 0) return array();
//        $camper = $id ? Camper::findOrFail($id) : Auth::user()->camper;
//        $family = $camper->family->city != null;
//        $workshops = 0;
//        $room = 0;
//        $nametag = 0;
//        $medical = 0;
//        $live = $this->year->is_brochure ? false : $this->year->brochure_date;
//        $ya = Yearattending::where('camper_id', $camper->id)->where('year_id', $this->year->id)->first();
//        if ($ya) {
//            $workshops = YearattendingWorkshop::where('yearattending_id', $ya->id)->get()->count() > 0;
//            $room = $ya->room_id != null;
//            $nametag = $ya->nametag != "222215521";
//            $medical = Medicalresponse::where('yearattending_id', $ya->id)->count() > 0;
//            $ya = true;
//        } else {
//            $ya = false;
//        }
//        return [$family, $ya, Gate::allows('has-paid'), $workshops, $room, $nametag, $medical, $camper->firstname, $live];
//    }
}
