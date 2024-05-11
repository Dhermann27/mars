<?php

namespace App\Http\Controllers;

use App\Enums\Timeslotname;
use App\Jobs\GenerateCharges;
use App\Jobs\UpdateWorkshops;
use App\Models\Camper;
use App\Models\ThisyearCamper;
use App\Models\Timeslot;
use App\Models\Workshop;
use App\Models\Year;
use App\Models\YearattendingWorkshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WorkshopController extends Controller
{
    public function store(Request $request, $id = null)
    {
        if ($id && Gate::allows('is-super')) {
            $camper = Camper::findOrFail($id);
        }
        $campers = $this->getCampers($id ? $camper->family_id : Auth::user()->camper->family_id);

        foreach ($campers as $camper) {
            $this->validate($request, ['workshops-' . $camper->id => 'nullable|regex:/^\d{0,5}+(,\d{0,5})*$/']);
            $choices = YearattendingWorkshop::where('yearattending_id', $camper->yearattending_id)
                ->get()->keyBy('workshop_id');

            if ($request->input('workshops-' . $camper->id) != null) {
                foreach (explode(',', $request->input('workshops-' . $camper->id)) as $choice) {
                    $yw = YearattendingWorkshop::updateOrCreate(['yearattending_id' => $camper->yearattending_id,
                        'workshop_id' => $choice]);
                    $choices->forget($choice);
                }
            }

            if (count($choices) > 0) {
                foreach ($choices as $choice) {
                    YearattendingWorkshop::where('yearattending_id', $camper->yearattending_id)
                        ->where('workshop_id', $choice->workshop_id)->delete();
                }
            }
        }

        UpdateWorkshops::dispatch($this->year->id);
        GenerateCharges::dispatch($this->year->id);
        $request->session()->flash('success', 'Your workshop selections have been updated.');
        return redirect()->action([WorkshopController::class, 'index'], ['id' => $id]);
    }

    public function index(Request $request, $id = null)
    {
        if ($id && Gate::allows('is-council')) {
            $camper = Camper::findOrFail($id);
            $request->session()->flash('camper', $camper);
        } else {
            $family_id = $this->getFamilyId();
        }
        $steps = $this->getStepData($id);
        $campers = $this->getCampers($id ? $camper->family_id : $family_id);
        if ($steps["amountDueNow"] > 0) {
            $request->session()->flash('error', 'You cannot register for workshops until your deposit has been paid.');
        } else {
            if (count($campers) == 0) {
                $request->session()->flash('warning', 'There are no campers registered for this year.');
            }
        }
        $timeslots = Timeslot::withWhereHas(['workshops' => function ($query) {
            $query->where('year_id', $this->year->id);
        }])->get();
        return view('workshopchoice', ['timeslots' => $timeslots, 'campers' => $campers,
            'stepdata' => $steps]);

    }

    public function display()
    {
        if ($this->year->is_brochure) {
            $workshops = Workshop::where('year_id', $this->year->id)->get()->groupBy('timeslot_id');
        } else {
            $lastyear = Year::where('year', '<', $this->year->year)->orderBy('year', 'desc')->firstOrFail();
            $workshops = Workshop::where('year_id', $lastyear->id)->get()->groupBy('timeslot_id');
        }
        return view('workshops', ['timeslots' => Timeslot::where('id', '!=', Timeslotname::Excursions)->get(),
            'workshops' => $workshops]);
    }

    public function excursions()
    {
        if ($this->year->is_brochure) {
            $workshops = Workshop::where('year_id', $this->year->id)->where('timeslot_id', Timeslotname::Excursions)
                ->get();
        } else {
            $lastyear = Year::where('year', '<', $this->year->year)->orderBy('year', 'desc')->firstOrFail();
            $workshops = Workshop::where('year_id', $lastyear->id)->where('timeslot_id', Timeslotname::Excursions)
                ->get();
        }
        return view('excursions', ['timeslot' => Timeslot::findOrFail(Timeslotname::Excursions),
            'workshops' => $workshops]);

    }

    private function getCampers($id)
    {
        return ThisyearCamper::where('family_id', $id)->whereNotNull('program_id')->with('yearattending.workshops')->orderBy('birthdate')->get();
    }

}
