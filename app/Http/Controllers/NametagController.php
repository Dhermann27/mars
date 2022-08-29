<?php

namespace App\Http\Controllers;

use App\Jobs\ExposeNametagsByFamily;
use App\Jobs\ExposeNametagsByYear;
use App\Jobs\ExposeParentsChildByFamily;
use App\Jobs\ExposeParentsChildByYear;
use App\Models\Camper;
use App\Models\ThisyearCamper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use function redirect;

class NametagController extends Controller
{
    public function store(Request $request)
    {
        $campers = $this->getCampers();

        $this->validate($request, [
            'name-*' => 'required|between:1,4',
            'namesize-*' => 'required|between:1,5',
            'pronoun-*' => 'required|between:1,2',
            'line1-*' => 'required|between:1,5',
            'line2-*' => 'required|between:1,5',
            'line3-*' => 'required|between:1,5',
            'line4-*' => 'required|between:1,5',
            'font-*' => 'required|between:1,7',
            'fontapply-*' => 'required|between:1,2',
        ]);

        foreach ($campers as $camper) {
            $this->updateCamper($request, $camper);
        }

        ExposeNametagsByYear::dispatch($this->year->id);
        $request->session()->flash('success', 'You have successfully customized your nametag' . (count($campers) > 1 ? 's' : '') . '.');
        return redirect()->route('nametag.index', ['campers' => $campers]);

    }

    private function getCampers($id = null)
    {
        return ThisyearCamper::where('family_id', $id ? $id : Auth::user()->camper->family_id)
            ->with('pronoun', 'church', 'yearattending.staffpositions', 'nametag')->orderBy('birthdate')->get();
    }

    private function updateCamper($request, $camper)
    {
        $nametag = $request->input('pronoun-' . $camper->id);
        $nametag .= $request->input('name-' . $camper->id);
        $nametag .= $request->input('namesize-' . $camper->id);
        $nametag .= $request->input('line1-' . $camper->id);
        $nametag .= $request->input('line2-' . $camper->id);
        $nametag .= $request->input('line3-' . $camper->id);
        $nametag .= $request->input('line4-' . $camper->id);
        $nametag .= $request->input('fontapply-' . $camper->id);
        $nametag .= $request->input('font-' . $camper->id);
        $camper->yearattending->nametag = $nametag;
        $camper->yearattending->save();
    }


    public function index(Request $request, $id = null)
    {
        if ($id && Gate::allows('is-council')) {
            $camper = Camper::findOrFail($id);
            $request->session()->flash('camper', $camper);
            $family_id = $camper->family_id;
        } else {
            $family_id = $this->getFamilyId();
        }
        $steps = $this->getStepData($id);
        ExposeParentsChildByFamily::dispatchSync($family_id);
        ExposeNametagsByFamily::dispatchSync($family_id);
        $campers = $this->getCampers($family_id);
        if ($steps["amountDueNow"] > 0) {
            $request->session()->flash('error', 'You cannot customize your nametags until your deposit has been paid.');
        } else {
            if (count($campers) == 0) {
                $request->session()->flash('warning', 'There are no campers registered for this year.');
            }
        }
        return view('nametags', ['campers' => $campers, 'stepdata' => $steps]);
    }

    public function all()
    {
        ExposeParentsChildByYear::dispatchSync($this->year->id);
        ExposeNametagsByYear::dispatchSync($this->year->id);
        $cqb = ThisyearCamper::with('nametag');
        if (config('app.name') != 'MUUSADusk') {
            $cqb->orderBy('familyname')->orderBy('family_id')->orderBy('birthdate');
        }
        $campers = $cqb->get();
        $campers->where('age', '<', '18')->load(['parents.room.building', 'parents.camper']);
        return view('layouts.allnametags', ['campers' => $campers]);
    }

//    public function write(Request $request, $id)
//    {
//
//        $campers = \App\Thisyear_Camper::where('familyid', $id)->get();
//
//        foreach ($campers as $camper) {
//            $this->updateCamper($request, $camper);
//        }
//
//        $request->session()->flash('success', 'It worked, but did you ever consider that all of this is meaningless in the grand scheme of things?');
//
//        return redirect()->action([NametagController::class, 'read'], ['i' => 'f', 'id' => $id]);
//    }
//
//    public function read($i, $id)
//    {
//        $readonly = \Entrust::can('read') && !\Entrust::can('write');
//        $campers = \App\Thisyear_Camper::where('familyid', $this->getFamilyId($i, $id))->orderBy('birthdate')->get();
//
//        return view('nametags', ['campers' => $campers, 'readonly' => $readonly, 'steps' => $this->getSteps()]);
//    }
}
