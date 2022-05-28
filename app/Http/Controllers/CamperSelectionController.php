<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\ChartdataNewcampers;
use App\Models\ChartdataOldcampers;
use App\Models\ChartdataVeryoldcampers;
use App\Models\Family;
use App\Models\Yearattending;
use App\Models\YearattendingStaff;
use App\Models\YearattendingWorkshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CamperSelectionController extends Controller
{
    public function store(Request $request, $id = null)
    {

        foreach ($request->all() as $key => $value) {
            $matches = array();
            if (preg_match('/(camper|newname)-(\d+)/', $key, $matches)) {
                if ($matches[1] == 'camper') {
                    if ($value == '1') {
                        Yearattending::updateOrCreate(['camper_id' => $matches[2], 'year_id' => $this->year->id]);
                    } else {
                        $ya = Yearattending::where('camper_id', $matches[2])->where('year_id', $this->year->id)->first();
                        if($ya) {
                            ChartdataNewcampers::where('yearattending_id', $ya->id)->delete();
                            ChartdataOldcampers::where('yearattending_id', $ya->id)->delete();
                            ChartdataVeryoldcampers::where('yearattending_id', $ya->id)->delete();
                            YearattendingWorkshop::where('yearattending_id', $ya->id)->delete();
                            YearattendingStaff::where('yearattending_id', $ya->id)->delete();
                            $ya->delete();
                        }
                    }
                } elseif (strlen($value) >= 2) {
                    $newnames = explode(' ', $value);
                    $newcamper = new Camper();
                    $newcamper->family_id = Auth::user()->camper->family_id;
                    if (count($newnames) == 2) {
                        $newcamper->firstname = $newnames[0];
                        $newcamper->lastname = $newnames[1];
                    } else {
                        $newcamper->firstname = $value;
                    }
                    $newcamper->save();
                    if($request->input('newcheck-' . $matches[2]) == '1') {
                        Yearattending::create(['camper_id' => $newcamper->id, 'year_id' => $this->year->id]);
                    }
                }
            }
        }
        GenerateCharges::dispatch($this->year->id);

        $request->session()->flash('success', 'Your information has been saved successfully.');
        return redirect()->route('camperselect.index', ['id' => $id]);

    }

    public function index(Request $request, $id = null)
    {
        $campers = array();
        if (Auth::user()->camper) {
            $campers = Camper::where('family_id', Auth::user()->camper->family_id)
                ->with(['yearsattending' => function ($query) {
                    $query->where('year_id', $this->year->id);
                }])->orderBy('birthdate')->get();
        } else {
            $family = new Family();
            $family->save();
            $campers[0] = new Camper();
            $campers[0]->family_id = $family->id;
            $campers[0]->email = Auth::user()->email;
            $campers[0]->save();
        }
        return view('register.camperselect', ['campers' => $campers, 'stepdata' => parent::getStepData()]);
    }
}
