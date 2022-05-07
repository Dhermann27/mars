<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Enums\Timeslotname;
use App\Models\ThisyearCamper;
use App\Models\Timeslot;
use App\Models\YearattendingWorkshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                    $yw = YearattendingWorkshop::where(['yearattending_id' => $camper->yearattending_id, 'workshop_id' => $choice])->first();
                    if (!$yw) {
                        $yw = new YearattendingWorkshop();
                        $yw->yearattending_id = $camper->yearattending_id;
                        $yw->workshop_id = $choice;
                    }
                    $yw->save();

                    $choices->forget($choice);
                }
            }

            if (count($choices) > 0) {
                foreach ($choices as $choice) {
                    DB::statement('DELETE FROM yearsattending__workshop WHERE yearattending_id=' .
                        $choice->yearattending_id . ' AND workshop_id=' . $choice->workshop_id);
                }
            }
        }

        DB::statement('CALL workshops();');

        $request->session()->flash('success', 'Your workshop selections have been updated.');

        return redirect()->action('WorkshopController@index', ['id' => $id]);
    }

    public function index(Request $request, $id = null)
    {
        if ($id && Gate::allows('is-council')) {
            $camper = Camper::findOrFail($id);
            $request->session()->flash('camper', $camper);
        }
        $campers = $this->getCampers($id ? $camper->family_id : Auth::user()->camper->family_id);
        if (count($campers) == 0) {
            $request->session()->flash('warning', 'There are no campers registered for this year.');
            return redirect()->action('CamperController@index', ['id' => $id ? $id : null]);
        }
        return view('workshopchoice', ['timeslots' => Timeslot::with('workshops')->get(),
            'campers' => $campers]);

    }

    public function display()
    {
        return view('workshops', ['timeslots' => Timeslot::all()->where('id', '!=', Timeslotname::Excursions)]);
    }

    public function excursions()
    {
        return view('excursions', ['timeslot' => Timeslot::findOrFail(Timeslotname::Excursions)]);

    }

    private function getCampers($id)
    {
        return ThisyearCamper::where('family_id', $id)->with('yearattending.workshops')->orderBy('birthdate')->get();
    }

}
