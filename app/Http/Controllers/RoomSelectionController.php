<?php

namespace App\Http\Controllers;

use App\Jobs\ExposeRoomselection;
use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\RoomselectionExpo;
use App\Models\ThisyearCamper;
use App\Models\Yearattending;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RoomSelectionController extends Controller
{
    public function store(Request $request, $id = null)
    {
        $this->validate($request, ['room_id' => 'nullable|exists:rooms,id']);

        $family_id = $id && Gate::allows('is-super') ? Camper::findOrFail($id)->family_id : Auth::user()->camper->family_id;

        $campers = ThisyearCamper::where(['is_program_housing' => '0', 'family_id' => $family_id])->get();
        foreach ($campers as $camper) {
            $ya = Yearattending::findOrFail($camper->yearattending_id);
            $ya->room_id = $request->input('room_id');
            if ($id && Gate::allows('is-super')) {
                $ya->is_setbyadmin = 1;
            }
            $ya->save();
        }

        GenerateCharges::dispatch($this->year->id);
        ExposeRoomselection::dispatch($this->year->id);

        $request->session()->flash('success', 'Room selection complete! Your room is locked in for the '
            . count($campers) . ' eligible members of your household. (This screen will update any lock changes in a few moments.');

        return redirect()->action([RoomSelectionController::class, 'index'], ['id' => $id]);
    }

    public function index(Request $request, $id = null)
    {

        $family_id = $this->getFamilyId($id);
        $step = $this->getStepData($id);
        $locked = 0;
        $ya = Yearattending::where('camper_id', isset($id) && Gate::allows('is-council') ? $id : Auth::user()->camper->id)
            ->where('year_id', $this->year->id)->first();
        $campers = ThisyearCamper::where('family_id', $family_id)->where('is_program_housing', '0')
            ->orderBy('birthdate')->get();
        if (!$id) {
            $locked = 1;
            if ($step["amountDueNow"] > 0) {
                $request->session()->flash('warning', 'You need to pay your deposit before selecting a room.');
            } elseif (count($campers) == 0) {
                $request->session()->flash('warning', 'There are no campers whose rooms can be changed.');
            } elseif ($ya && $ya->is_setbyadmin == '1') {
                $request->session()->flash('warning', 'This room has been locked by the Registrar. Please use the Contact Us form above to request any changes at this point.');
            } else {
                $locked = 0;
            }
        }

        $rooms = RoomselectionExpo::all();
        if (count($rooms) == 0 || !Carbon::parse($rooms->first()->created_at)->isCurrentDay()) {
            ExposeRoomselection::dispatchSync($this->year->id);
            $rooms = RoomselectionExpo::with('room.building')->get();
        } else {
            $rooms->load('room.building');
        }

        return view('roomselection', ['currentRoom' => $ya->room_id ?? 0, 'rooms' => $rooms, 'campers' => $campers,
            'locked' => $locked, 'stepdata' => $step]);
    }

//    public function write(Request $request, $id)
//    {
//        $campers = ThisyearCamper::where('family_id', $id)->get();
//
//        foreach ($campers as $camper) {
//            $ya = Yearattending::find($camper->yearattending_id);
//            $ya->room_id = $request->input('roomid-' . $camper->id);
//            if ($ya->room_id == 0) $ya->room_id = null;
//            $ya->is_setbyadmin = '1';
//            $ya->save();
//        }
//
//        GenerateCharges::dispatch($this->year->id);
//
//        $request->session()->flash('success', 'Awwwwwwww yeahhhhhhhhh');
//
//        return redirect()->action([RoomSelectionController::class, 'read'], ['id' => $campers[0]->id]);
//    }
//
//    public function read(Request $request, $id)
//    {
//        $family = Family::findOrFail(Camper::findOrFail($id)->family_id);
//        $campers = ThisyearCamper::where('family_id', $family->id)->with('yearsattending.year', 'yearsattending.room.building')
//            ->orderBy('birthdate')->get();
//
//        if (count($campers) == 0) {
//            $request->session()->flash('warning', 'No members of this family are registered for the current year.');
//        }
//
//        return view('assignroom', ['buildings' => Building::with('rooms.occupants')->get(),
//            'campers' => $campers]);
//    }
//
//    public function map()
//    {
//        $empty = new Yearattending();
//        $rooms = RoomselectionExpo::all();
//        return view('roomselection', ['ya' => $empty, 'rooms' => $rooms, 'count' => 0, 'locked' => true]);
//    }

}
