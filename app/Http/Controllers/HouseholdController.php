<?php

namespace App\Http\Controllers;

use App\Http\Camper;
use App\Enums\Foodoptionname;
use App\Http\Family;
use App\Http\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class HouseholdController extends Controller
{
    public function store(Request $request, $id = null)
    {
        $messages = ['province_id.exists' => 'Please choose a state code or "ZZ" for international.'];

        $this->validate($request, [
            'address1' => 'required|max:255',
            'address2' => 'max:255',
            'city' => 'required|max:255',
            'province_id' => 'required|exists:provinces,id',
            'zipcd' => 'required|alpha_dash|max:255',
            'is_ecomm' => 'required|in:0,1',
            'is_scholar' => 'required|in:0,1'
        ], $messages);

        $family = new Family();
        if ($id > 0 && Gate::allows('is-super')) {
            $family = Family::findOrFail(Camper::findOrFail($id)->family_id);
        } else if ($id > 0 || !Gate::allows('is-super')) {
            $family = Family::findOrFail(Auth::user()->camper->family_id);
        }
        $family->address1 = $request->input('address1');
        $family->address2 = $request->input('address2');
        $family->city = $request->input('city');
        $family->province_id = $request->input('province_id');
        $family->zipcd = $request->input('zipcd');
        $family->country = $request->input('country');
        if ($id != null && Gate::allows('is-super')) {
            $family->is_address_current = $request->input('is_address_current');
        }
        $family->is_ecomm = $request->input('is_ecomm');
        $family->is_scholar = $request->input('is_scholar');
        $family->save();

        $request->session()->flash('success', 'Your information has been saved successfully.');

        if ($id == 0 && Gate::allows('is-super')) {
            $camper = new Camper();
            $camper->family_id = $family->id;
            $camper->firstname = "New Camper";
            $camper->foodoption_id = Foodoptionname::None;
            $camper->save();

            $id = $camper->id;
        }

        return redirect()->route('household.index', ['id' => $id]);

    }

    public function index(Request $request, $id = null)
    {
        if ($id != null && $id == 0) {
            $family = new Family();
            if(Gate::allows('is-super')) {
                $camper = new Camper();
                $camper->id = 0;
                $request->session()->flash('camper', $camper);
            }
        } else {
            if ($id != null && Gate::allows('is-council')) {
                $camper = Camper::findOrFail($id);
                $request->session()->flash('camper', $camper);
                $family = Family::findOrFail($camper->family_id);
            } else {
                $family = Auth::user()->camper->family;
            }
        }
        return view('household', ['formobject' => $family, 'provinces' => Province::orderBy('name')->get()]);
    }

}
