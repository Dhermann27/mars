<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\Family;
use App\Models\Province;
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

        $family = Family::findOrFail($id > 0 && Gate::allows('is-super') ?
            Camper::findOrFail($id)->family_id : Auth::user()->camper->family_id);

        $family->address1 = $request->input('address1');
        $family->address2 = $request->input('address2');
        $family->city = $request->input('city');
        $family->province_id = $request->input('province_id');
        $family->zipcd = $request->input('zipcd');
        $family->country = $request->input('country');
        if (Gate::allows('is-super')) {
            $family->is_address_current = $request->input('is_address_current');
        } else if ($request->input('address1') != 'NEED ADDRESS') {
            $family->is_address_current = 1;
        }
        $family->is_ecomm = $request->input('is_ecomm');
        $family->is_scholar = $request->input('is_scholar');
        $family->save();

        $request->session()->flash('success', 'Your information has been saved successfully.');
        return redirect()->route('household.index', ['id' => $id]);

    }

    public function index(Request $request, $id = null)
    {
        $family_id = $this->getFamilyId();
        $family = Family::find($family_id);
        return view('household', ['stepdata' => $this->getStepData(), 'family' => $family,
            'provinces' => Province::orderBy('name')->get()]);
    }

}
