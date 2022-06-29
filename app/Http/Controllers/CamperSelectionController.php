<?php

namespace App\Http\Controllers;

use App\Jobs\ExposeParentsChild;
use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\CamperStaff;
use App\Models\ChartdataNewcampers;
use App\Models\ChartdataOldcampers;
use App\Models\ChartdataVeryoldcampers;
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
                        $ya = Yearattending::updateOrCreate(['camper_id' => $matches[2], 'year_id' => $this->year->id]);
                        $staffs = CamperStaff::where('camper_id', $matches[2])->get();
                        if (count($staffs) > 0) {
                            foreach ($staffs as $staff) {
                                YearattendingStaff::updateOrCreate(['yearattending_id' => $ya->id,
                                    'staffposition_id' => $staff->staffposition_id]);
                            }
                            CamperStaff::where('camper_id', $matches[2])->delete();
                        }

                    } else {
                        $ya = Yearattending::where('camper_id', $matches[2])->where('year_id', $this->year->id)->first();
                        if ($ya) {
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
                    $newcamper = $matches[2] >= 1000 ? Camper::find($matches[2]) : new Camper();
                    $newcamper->family_id = Auth::user()->camper->family_id;
                    if (count($newnames) == 2) {
                        $newcamper->firstname = $newnames[0];
                        $newcamper->lastname = $newnames[1];
                    } else {
                        $newcamper->firstname = $value;
                    }
                    if(config('app.name') == 'MUUSADusk') $newcamper->roommate = __FUNCTION__;
                    $newcamper->save();
                    if ($request->input('newcheck-' . $matches[2]) == '1') {
                        Yearattending::create(['camper_id' => $newcamper->id, 'year_id' => $this->year->id]);
                    }
                }
            }
        }
        GenerateCharges::dispatch($this->year->id);
        ExposeParentsChild::dispatch($this->year->id);

        $request->session()->flash('success', 'Your information has been saved successfully.');
        return redirect()->route('camperselect.index', ['id' => $id]);

    }

    public function index(Request $request, $id = null)
    {
        $family_id = $this->getFamilyId();
        $campers = Camper::where('family_id', $family_id)
            ->with(['yearsattending' => function ($query) {
                $query->where('year_id', $this->year->id);
            }])->orderBy('birthdate')->get();
        return view('register.camperselect', ['campers' => $campers, 'stepdata' => $this->getStepData()]);
    }

}
