<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateWorkshops;
use App\Models\ByyearFamily;
use App\Models\Charge;
use App\Models\Chargetype;
use App\Models\ThisyearFamily;
use App\Models\Timeslot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function view;

class ReportController extends Controller
{
    public function campers()
    {
        $years = ThisyearFamily::orderBy('familyname')->with('thisyearcampers')->get();

        return view('reports.campers', ['years' => $years]);
    }

//    public function chart()
//    {
//        return view('reports.chart', ['years' => Year::where('year', '>', $this->year->year - 7)
//            ->where('year', '<=', $this->year->year)->with(['chartdataNewcampers.yearattending.camper',
//                'chartdataOldcampers.yearattending.camper', 'chartdataVeryoldcampers.yearattending.camper',
//                'chartdataLostcampers.camper', 'yearsattending'])->orderBy('year')->get(),
//            'chartdataDays' => ChartdataDays::all()->groupBy('onlyday')]);
//    }
//
//
    public function depositsMark(Request $request, $id)
    {
        $found = false;
        if($request->has('mark')) {
            for ($i = 0; $i < count($request->input('mark')); $i++) {
                $charge = Charge::findOrFail($request->input('mark')[$i]);
                $charge->deposited_date = Carbon::now()->toDateString();
                $charge->save();
                $found = true;
            }
        }
        if (!$found) {
            Charge::where('chargetype_id', $id)->where('deposited_date', null)
                ->update(['deposited_date' => Carbon::now()->toDateString()]);
        }
        $request->session()->flash('success', 'John made me put this message here. I\'m in his basement. Send help. -Dan');
        return redirect()->action([ReportController::class, 'deposits']);
    }

    public function deposits()
    {
        $chargetypes = Chargetype::where('is_deposited', '1')
            ->with(['byyearcharges.camper', 'byyearcharges.children'])->get();
        return view('reports.deposits', ['chargetypes' => $chargetypes]);
    }

//
//    public function outstandingMark(Request $request, $id)
//    {
//        $charge = new Charge();
//        $charge->camper_id = $id;
//        $charge->amount = $request->input('amount');
//        $charge->memo = $request->input('memo');
//        $charge->chargetype_id = $request->input('chargetype_id');
//        $charge->timestamp = Carbon::now()->toDateString();
//        $charge->year_id = $request->input('year_id');
//        $charge->save();
//
//        $request->session()->flash('success', 'This payment was actually ignored, but the green message still seems congratulatory.');
//        return redirect()->action([ReportController::class, 'outstanding']);
//    }
//
//    public function outstanding()
//    {
//        $chargetypes = Chargetype::where('is_shown', '1')->orderBy('name')->get();
//        $lastyear = Year::where('year', $this->year->year-1)->first();
//        return view('reports.outstanding', ['chargetypes' => $chargetypes, 'charges' => Outstanding::all(),
//            'lastyear' => $lastyear]);
//    }
//
//    public function programs()
//    {
//        $columns = ['familyname' => 'Family Name',
//            'address1' => 'Address Line #1',
//            'address2' => 'Address Line #2',
//            'city' => 'City',
//            'provincecode' => 'Province',
//            'zipcd' => 'Postal Code',
//            'country' => 'Country',
//            'pronounname' => 'Pronouns',
//            'firstname' => 'First Name',
//            'lastname' => 'Last Name',
//            'email' => 'Email',
//            'birthday' => 'Birthday',
//            'age' => 'Age',
//            'days' => 'Days Attending',
//            'buildingname' => 'Building',
//            'room_number' => 'Room',
//            'controls' => 'Admin Controls'];
//        $visible = [0, 1, 2, 3, 4, 5, 6, 10, 11, 13];
//        $programs = Program::where('id', '!=', Programname::Adult)->orderBy('order')->get();
//
//        return view('reports.datatables', ['title' => 'Programs', 'columns' => $columns,
//            'visible' => $visible, 'tabs' => $programs, 'tabfield' => 'name',
//            'datafield' => "thisyearcampers"]);
//    }
//
//    public function rooms()
//    {
//        $years = Year::where('year', '>', $this->year->year - 7)->where('year', '<=', $this->year->year)
//            ->orderBy('year')->with('byyearcampers')->get();
//        return view('reports.rooms', ['years' => $years]);
//    }

    public function workshops()
    {
        UpdateWorkshops::dispatchSync($this->year->id);
        return view('reports.workshops', ['timeslots' => Timeslot::with('thisyearWorkshops.choices.yearattending.camper')->get()]);

    }
}
