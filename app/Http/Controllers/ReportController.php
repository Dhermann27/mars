<?php

namespace App\Http\Controllers;

use App\Enums\Programname;
use App\Http\ByyearCharge;
use App\Http\Charge;
use App\Http\Chargetype;
use App\Http\ChartdataDays;
use App\Http\Outstanding;
use App\Http\Program;
use App\Http\ThisyearCharge;
use App\Http\Timeslot;
use App\Http\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function preg_match;
use function view;

class ReportController extends Controller
{
    public function campers()
    {
        $columns = ['familyname' => 'Family Name',
            'address1' => 'Address Line #1',
            'address2' => 'Address Line #2',
            'city' => 'City',
            'provincecode' => 'Province',
            'zipcd' => 'Postal Code',
            'country' => 'Country',
            'pronounname' => 'Pronouns',
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'email' => 'Email',
            'birthday' => 'Birthday',
            'age' => 'Age',
            'programname' => 'Program',
            'days' => 'Days Attending',
            'buildingname' => 'Building',
            'room_number' => 'Room',
            'controls' => 'Admin Controls'];
        $visible = [0, 1, 2, 3, 4, 5, 6, 7, 11, 14];
        $years = Year::where('year', '>', $this->year->year - 7)->where('year', '<=', $this->year->year)
            ->orderBy('year')->with('byyearcampers')->get();

        return view('reports.datatables', ['title' => 'Registered Campers', 'columns' => $columns,
            'visible' => $visible, 'groupColumn' => 0, 'tabs' => $years, 'tabfield' => 'year',
            'datafield' => "byyearcampers"]);
    }

    public function chart()
    {
        return view('reports.chart', ['years' => Year::where('year', '>', $this->year->year - 7)
            ->where('year', '<=', $this->year->year)->with(['chartdataNewcampers.yearattending.camper',
                'chartdataOldcampers.yearattending.camper', 'chartdataVeryoldcampers.yearattending.camper',
                'chartdataLostcampers.camper', 'yearsattending'])->orderBy('year')->get(),
            'chartdataDays' => ChartdataDays::all()->groupBy('onlyday')]);
    }


    public function depositsMark(Request $request, $id)
    {
        $found = false;
        foreach ($request->all() as $key => $value) {
            $matches = array();
            if (preg_match('/delete-(\d+)/', $key, $matches)) {
                $charge = Charge::findOrFail($matches[1]);
                $charge->deposited_date = Carbon::now()->toDateString();
                $charge->save();
                $found = true;
            }
        }
        if (!$found) {
            Charge::where('chargetype_id', $id)->where('deposited_date', null)
                ->update(['deposited_date' => Carbon::now()->toDateString()]);
        }
        $request->session()->flash('success', 'John made me put this message here. Send help.');
        return redirect()->action('ReportController@deposits');
    }

    public function deposits()
    {
        $chargetypes = Chargetype::where('is_deposited', '1')
            ->with(['byyearcharges.camper', 'byyearcharges.children'])->get();
        return view('reports.deposits', ['chargetypes' => $chargetypes]);
    }

    public function outstandingMark(Request $request, $id)
    {
        $charge = new Charge();
        $charge->camper_id = $id;
        $charge->amount = $request->input('amount');
        $charge->memo = $request->input('memo');
        $charge->chargetype_id = $request->input('chargetype_id');
        $charge->timestamp = Carbon::now()->toDateString();
        $charge->year_id = $request->input('year_id');
        $charge->save();

        $request->session()->flash('success', 'This payment was actually ignored, but the green message still seems congratulatory.');
        return redirect()->action('ReportController@outstanding');
    }

    public function outstanding()
    {
        $chargetypes = Chargetype::where('is_shown', '1')->orderBy('name')->get();
        $lastyear = Year::where('year', $this->year->year-1)->first();
        return view('reports.outstanding', ['chargetypes' => $chargetypes, 'charges' => Outstanding::all(),
            'lastyear' => $lastyear]);
    }

    public function programs()
    {
        $columns = ['familyname' => 'Family Name',
            'address1' => 'Address Line #1',
            'address2' => 'Address Line #2',
            'city' => 'City',
            'provincecode' => 'Province',
            'zipcd' => 'Postal Code',
            'country' => 'Country',
            'pronounname' => 'Pronouns',
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'email' => 'Email',
            'birthday' => 'Birthday',
            'age' => 'Age',
            'days' => 'Days Attending',
            'buildingname' => 'Building',
            'room_number' => 'Room',
            'controls' => 'Admin Controls'];
        $visible = [0, 1, 2, 3, 4, 5, 6, 10, 11, 13];
        $programs = Program::where('id', '!=', Programname::Adult)->orderBy('order')->get();

        return view('reports.datatables', ['title' => 'Programs', 'columns' => $columns,
            'visible' => $visible, 'tabs' => $programs, 'tabfield' => 'name',
            'datafield' => "thisyearcampers"]);
    }

    public function rooms()
    {
        $years = Year::where('year', '>', $this->year->year - 7)->where('year', '<=', $this->year->year)
            ->orderBy('year')->with('byyearcampers')->get();
        return view('reports.rooms', ['years' => $years]);
    }

    public function workshops()
    {
        DB::raw('CALL workshops()');
        return view('reports.workshops', ['timeslots' => Timeslot::with('workshops.choices.yearattending.camper')->get()]);

    }
}
