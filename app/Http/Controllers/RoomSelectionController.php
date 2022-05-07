<?php

namespace App\Http\Controllers;

use App\Http\Building;
use App\Http\Camper;
use App\Http\Family;
use App\Http\Roomselection;
use App\Http\ThisyearCamper;
use App\Http\Yearattending;
use App\Jobs\GenerateCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RoomSelectionController extends Controller
{
    public function store(Request $request, $id = null)
    {
        $this->validate($request, ['room_id' => 'required|exists:rooms,id']);

        $family_id = $id && Gate::allows('is-super') ? Camper::findOrFail($id)->family_id : Auth::user()->camper->family_id;

        $family = ThisyearCamper::where(['is_program_housing' => '0', 'family_id' => $family_id])->get();
        foreach ($family as $item) {
            $ya = Yearattending::findOrFail($item->yearattending_id);
            $ya->room_id = $request->room_id;
            if ($id && Gate::allows('is-super')) {
                $ya->is_setbyadmin = 1;
            }
            $ya->save();
        }

        GenerateCharges::dispatch($this->year->id);

        $success = 'Room selection complete! Your room is locked in for the ' . count($family) . ' eligible members of your household.';

        $request->session()->flash('success', $success);

        return redirect()->action('RoomSelectionController@index', ['id' => $id]);
    }

    public function index(Request $request, $id = null)
    {
        $camper = 0;
        if ($id && Gate::allows('is-council')) {
            $camper = Camper::findOrFail($id);
            $request->session()->flash('camper', $camper);
        } else {
            $camper = Auth::user()->camper;
        }

        $ya = Yearattending::where('camper_id', $camper->id)->where('year_id', $this->year->id)->first();
        $locked = ($ya->is_setbyadmin == '1' || $ya->program->is_program_housing == '1') && !($id && Gate::allows('is-super'));
        $count = ThisyearCamper::where('family_id', $camper->family_id)->where('is_program_housing', '0')->count();
        $rooms = Roomselection::all();

        if ($locked) {
            $request->session()->flash('warning', 'This room has been locked by the Registrar. Please use the Contact Us form above to request any changes at this point.');
        }

        return view('roomselection', ['ya' => $ya, 'rooms' => $rooms, 'count' => $count, 'locked' => $locked]);
    }

    public function write(Request $request, $id)
    {
        $campers = ThisyearCamper::where('family_id', $id)->get();

        foreach ($campers as $camper) {
            $ya = Yearattending::find($camper->yearattending_id);
            $ya->room_id = $request->input('roomid-' . $camper->id);
            if ($ya->room_id == 0) $ya->room_id = null;
            $ya->is_setbyadmin = '1';
            $ya->save();
        }

        GenerateCharges::dispatch($this->year->id);

        $request->session()->flash('success', 'Awwwwwwww yeahhhhhhhhh');

        return redirect()->action('RoomSelectionController@read', ['id' => $campers[0]->id]);
    }

    public function read(Request $request, $id)
    {
        $family = Family::findOrFail(Camper::findOrFail($id)->family_id);
        $campers = ThisyearCamper::where('family_id', $family->id)->with('yearsattending.year', 'yearsattending.room.building')
            ->orderBy('birthdate')->get();

        if (count($campers) == 0) {
            $request->session()->flash('warning', 'No members of this family are registered for the current year.');
        }

        return view('assignroom', ['buildings' => Building::with('rooms.occupants')->get(),
            'campers' => $campers]);
    }

    public function map()
    {
        $empty = new Yearattending();
        $rooms = Roomselection::all();
        return view('roomselection', ['ya' => $empty, 'rooms' => $rooms, 'count' => 0, 'locked' => true]);
    }

}
