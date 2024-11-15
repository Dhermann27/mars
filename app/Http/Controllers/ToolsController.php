<?php

namespace App\Http\Controllers;

use App\Enums\Pctype;
use App\Enums\Usertype;
use App\Jobs\GenerateCharges;
use App\Models\CamperStaff;
use App\Models\Program;
use App\Models\Staffposition;
use App\Models\ThisyearCamper;
use App\Models\ThisyearStaff;
use App\Models\User;
use App\Models\YearattendingStaff;
use Illuminate\Http\Request;
use function collect;
use function view;

class ToolsController extends Controller
{

    public function cognoscenti()
    {
        $pctypes = collect([['id' => Pctype::Xc, 'name' => 'Executive Council'],
            ['id' => Pctype::Apc, 'name' => 'Adult Programming Committee'],
            ['id' => Pctype::Program, 'name' => 'Program Coordinators'],
            ['id' => Pctype::Staff, 'name' => 'Staff']]);
        return view('tools.cognoscenti', ['pctypes' => $pctypes,
            'positions' => ThisyearStaff::orderBy('staffpositionname')->orderBy('lastname')->orderBy('firstname')->get()
                ->groupBy('pctype')]);
    }

    public function positionStore(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $matches = array();
            if ($value == '1' && preg_match('/delete-(\d+)-(\d+)/', $key, $matches)) {
                $ya = ThisyearCamper::find($matches[1]);
                if ($ya) {
                    $assignment = YearattendingStaff::where('yearattending_id', $ya->yearattending_id)
                        ->where('staffposition_id', $matches[2]);
                    $user = User::where('email', $ya->email)->first();
                    if($user) {
                        $user->usertype = Usertype::Camper;
                        $user->save();
                    }
                } else {
                    $assignment = CamperStaff::where('camper_id', $matches[1])->where('staffposition_id', $matches[2]);
                }
                $assignment->delete();
            }
        }
        if ($request->input('camper_id') != '' && $request->input('staffposition_id') != '') {
            $ya = ThisyearCamper::find($request->input('camper_id'));
            if (!empty($ya)) {
                $assignment = new YearattendingStaff();
                $assignment->yearattending_id = $ya->yearattending_id;
                $assignment->staffposition_id = $request->input('staffposition_id');
                $assignment->is_eaf_paid = 1;
                $assignment->save();
            } else {
                $assignment = new CamperStaff();
                $assignment->camper_id = $request->input('camper_id');
                $assignment->staffposition_id = $request->input('staffposition_id');
                $assignment->save();
            }
        }
        GenerateCharges::dispatch($this->year->id);

        $request->session()->flash('success', 'Assigned. Suckers! No backsies.');

        return redirect()->action([ToolsController::class, 'positionIndex']);
    }

    public function positionIndex()
    {
        return view('tools.positions', ['programs' => Program::orderBy('order')->get(),
            'positions' => Staffposition::where('start_year', '<=', $this->year->year)
                ->where('end_year', '>=', $this->year->year)->orderBy('name')->get(),
            'staff' => ThisyearStaff::orderBy('staffpositionname')->orderBy('lastname')->orderBy('firstname')->get()
                ->groupBy('program_id')]);
    }
}
