<?php

namespace App\Http\Controllers;

use App\Enums\Usertype;
use App\Models\Camper;
use App\Models\Compensationlevel;
use App\Models\Program;
use App\Models\Staffposition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
//    public function distlistExport(Request $request)
//    {
//        return (new ByyearCampersExport($request))
//            ->download('MUUSA_Distlist_' . Carbon::now()->toDateString() . '.xlsx');
//    }
//
//    public function distlistIndex($request = null)
//    {
//        return view('admin.distlist', ['programs' => Program::orderBy('order')->get(),
//            'request' => $request ? $request : new Request()]);
//    }

    public function roleStore(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $matches = array();
            if (preg_match('/delete-(\d+)/', $key, $matches)) {
                if ($value == 'on') {
                    $user = User::findOrFail($matches[1]);
                    $user->usertype = 0;
                    $user->save();
                }
            }
        }

        if ($request->input('camper_id') != '') {
            $camper = Camper::findOrFail($request->input('camper_id'));
            if ($camper->user()) {
                $user = User::where('email', $camper->email)->firstOrFail();
                $user->usertype = $request->input('usertype');
                $user->save();
            } else {
                $request->session()->flash('error',
                    $camper->firstname . " " . $camper->lastname . " has not yet registered on muusa.org.<br />");
            }
        }

        $request->session()->flash('success', 'Real artists ship.');

        return redirect()->action([AdminController::class, 'roleIndex']);
    }

    public function roleIndex()
    {
        $users = User::where('usertype', '>', Usertype::Camper)->with('camper')->get()->groupBy('usertype');
        $roles = collect([['id' => Usertype::Pc, 'name' => 'Planning Council'], ['id' => Usertype::Admin, 'name' => 'Super Admin']]);
        return view('admin.roles', ['roles' => $roles->all(), 'users' => $users]);
    }

    public function positionStore(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $matches = array();
            if (preg_match('/(delete|program_id|name|compensationlevel_id)-(\d+)/', $key, $matches)) {
                $position = Staffposition::findOrFail($matches[2]);
                if ($matches[1] == 'delete') {
                    if ($value == '1') {
                        DB::table('yearsattending__staff')
                            ->join('yearsattending', 'yearsattending.id', '=', 'yearsattending__staff.yearattending_id')
                            ->where('yearsattending.year_id', $this->year->id)
                            ->where('yearsattending__staff.staffposition_id', $matches[2])->delete();
                        $position->end_year = $this->year->year - 1;
                        $position->save();
                    }
                } else {
                    $position->{$matches[1]} = $value;
                    $position->save();
                }
            }
        }

        if ($request->input('name') != '') {
            $position = new Staffposition();
            $position->program_id = $request->input('program_id');
            $position->name = $request->input('name');
            $position->compensationlevel_id = $request->input('compensationlevel_id');
            $position->pctype = $request->input('pctype');
            $position->start_year = $this->year->year;
            $position->save();
        }

        $request->session()->flash('success', 'You created those positions like a <i>pro</i>.');

        return redirect()->action([AdminController::class, 'positionIndex']);
    }

    public function positionIndex()
    {
        return view('admin.positions', ['programs' => Program::with('staffpositions.compensationlevel')->orderBy('order')->get(),
            'levels' => Compensationlevel::where('start_year', '<=', $this->year->year)
                ->where('end_year', '>=', $this->year->year)->orderBy('name')->get()]);
    }

}
