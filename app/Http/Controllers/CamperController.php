<?php

namespace App\Http\Controllers;

use App\Enums\Programname;
use App\Http\ByyearCamper;
use App\Http\Camper;
use App\Http\Campers_view;
use App\Http\CamperStaff;
use App\Http\Family;
use App\Http\Foodoption;
use App\Http\Program;
use App\Http\Pronoun;
use App\Http\ThisyearCharge;
use App\Http\User;
use App\Http\Yearattending;
use App\Http\YearattendingStaff;
use App\Http\YearattendingWorkshop;
use App\Jobs\GenerateCharges;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use function array_push;
use function count;

class CamperController extends Controller
{
    private $messages = ['pronoun_id.*.exists' => 'Please choose a preferred pronoun.',
        'firstname.*.required' => 'Please enter a first name.',
        'lastname.*.required' => 'Please enter a last name.',
        'email.*.email' => 'Please enter a valid email address.',
        'email.*.distinct' => 'Please do not use the same email address for multiple campers.',
        'email.*.unique' => 'This email address has already been taken.',
        'phonenbr.*.regex' => 'Please enter your ten-digit phone number in 800-555-1212 format.',
        'birthdate.*.required' => 'Please enter your eight-digit birthdate in 2016-12-31 format.',
        'birthdate.*.regex' => 'Please enter your eight-digit birthdate in 2016-12-31 format.',
        'program_id.*.required' => 'Please choose a valid program for this camper.',];
    private $logged_in;

    public function store(Request $request)
    {
        $this->logged_in = Auth::user()->camper;

        $this->validate($request, [
            'days.*' => 'between:0,8',
            'pronoun_id.*' => 'exists:pronouns,id',
            'firstname.*' => 'required|max:255',
            'lastname.*' => 'required|max:255',
            'email.*' => 'nullable|email|max:255|distinct',
            'phonenbr.*' => 'nullable|regex:/^\d{3}-\d{3}-\d{4}$/',
            'birthdate.*' => 'required|regex:/^\d{4}-\d{2}-\d{2}$/',
            'program_id.*' => 'required|exists:programs,id',
            'roommate.*' => 'max:255',
            'sponsor.*' => 'max:255',
            'church_id.*' => 'exists:churches,id',
            'is_handicap.*' => 'in:0,1',
            'foodoption_id.*' => 'exists:foodoptions,id',
        ], $this->messages);


        $family_id = 0;
        for ($i = 0; $i < count($request->input('id')); $i++) {
            $id = (int)($request->input('id')[$i]);
            if ($id > 999) {
                $camper = Camper::findOrFail($id);
                $family_id = $camper->family_id;

                $this->validate($request, [
                    'email.' . $i => 'nullable|unique:campers,email,' . $id,
                ], $this->messages);

                if ($this->logged_in && $id == $this->logged_in->id) {
                    $this->validate($request, [
                        'email.' . $i => 'unique:users,email,' . Auth::user()->id,
                    ], $this->messages);
                }
                if (($this->logged_in && $camper->family_id == $this->logged_in->family_id)
                    || Gate::allows('is-super')) {
                    $this->upsertCamper($request, $camper, $i);
                }
            } else {
                $query = Camper::where(function ($query) use ($request, $i) {
                    $query->where('firstname', $request->input('firstname')[$i])
                        ->where('lastname', $request->input('lastname')[$i])
                        ->where('birthdate', $request->input('birthdate')[$i]);
                });
                if ($request->input('email')[$i] != '') {
                    $query = $query->orWhere('email', $request->input('email')[$i]);
                }
                $camper = $query->first();
                if (!$camper) {
                    $camper = new Camper();
                    if ($i != 0) {
                        $camper->family_id = $family_id;
                    }
                }
                $family_id = $this->upsertCamper($request, $camper, $i)->family_id;
            }
        }

        //Rollovers

        $campers = ByyearCamper::where('family_id', $family_id)
            ->where('year', ((int)$this->year->year) - 1)->where('is_program_housing', '0')->get();
        if (!$this->year->is_live && count($campers) > 0 && ThisyearCharge::where('family_id', $family_id)
                ->where(function ($query) {
                    $query->where('chargetype_id', 1003)->orWhere('amount', '<', '0');
                })->get()->sum('amount') <= 0) {
            foreach ($campers as $camper) {
                Yearattending::where('camper_id', $camper->id)->where('year_id', $this->year->id)
                    ->whereNull('room_id')->update(['room_id' => $camper->room_id]);
            }
        }


        GenerateCharges::dispatch($this->year->id);

        return 'You have successfully saved your changes. Click here to remit payment and complete your registration (if you have not already done so).';
    }

    public function index(Request $request, $id = null)
    {
        $campers = array();
        if ($id && Gate::allows('is-council')) {
            $request->session()->flash('camper', Camper::findOrFail($id));
            $campers = Campers_view::where('family_id', Camper::find($id)->family_id)->orderBy('birthdate')->get();
        } elseif (isset(Auth::user()->camper)) {
            $campers = Campers_view::where('family_id', Auth::user()->camper->family_id)->orderBy('birthdate')->get();
            if ($request->session()->has('login-campers') && count($request->session()->get('login-campers')) > 0) {
                foreach ($campers as $camper) {
                    foreach ($request->session()->get('login-campers') as $login) {
                        if ($camper->id == $login) $camper->currentdays = 6;
                    }
                }
            }
        } else {
            for ($i = 0; $i < max($request->session()->get('newcampers'), 1); $i++) {
                $camper = new Camper();
                $camper->firstname = "New Camper";
                $camper->currentdays = 6;
                $camper->id = 100 + $i;
                if ($i == 0) {
                    $camper->email = Auth::user()->email;
                }
                array_push($campers, $camper);
            }
        }

        $empty = new Camper();
        $empty->id = 999;
        return view('campers', ['pronouns' => Pronoun::all(), 'foodoptions' => Foodoption::all(),
            'campers' => $campers, 'programs' => Program::whereNotNull('title')->orderBy('order')->get(),
            'empty' => $empty]);

    }

    private function upsertCamper(Request $request, Camper $camper, $i)
    {
        if (!isset($camper->family_id)) {
            $family = new Family();
            $family->is_address_current = 0;
            $family->save();
            $camper->family_id = $family->id;
        }
        $camper->pronoun_id = $request->input('pronoun_id')[$i];
        $camper->firstname = $request->input('firstname')[$i];
        $camper->lastname = $request->input('lastname')[$i];

        if ($request->input('email')[$i] != '') {
            if ($camper->email != $request->input('email')[$i] && $camper->email) {
                $user = User::where('email', $camper->email)->first();
                if ($user) {
                    $user->email = $request->input('email')[$i];
                    $user->save();
                }
            }
            $camper->email = $request->input('email')[$i];
        }
        if ($request->input('phonenbr')[$i] != '') {
            $camper->phonenbr = str_replace('-', '', $request->input('phonenbr')[$i]);
        }
        $camper->birthdate = $request->input('birthdate')[$i];
        $program_id = $request->input('program_id')[$i];
        if ($program_id == Programname::YoungAdult && Carbon::createFromFormat('Y-m-d', $camper->birthdate)->diffInYears(Carbon::createFromFormat('Y-m-d', $this->year->checkin)) < 21) {
            $program_id = Programname::YoungAdultUnderAge;
        }

        $camper->roommate = $request->input('roommate')[$i];
        $camper->sponsor = $request->input('sponsor')[$i];
        if ($request->input('church_id') && array_key_exists($i, $request->input('church_id'))) {
            $camper->church_id = $request->input('church_id')[$i];
        }
        $camper->is_handicap = $request->input('is_handicap')[$i];
        $camper->foodoption_id = $request->input('foodoption_id')[$i];

        $camper->save();

        if ((int)$request->input('days')[$i] > 0) {
            $ya = Yearattending::where(['camper_id' => $camper->id, 'year_id' => $this->year->id])->first();
            if (!$ya) {
                $ya = new Yearattending();
                $ya->camper_id = $camper->id;
                $ya->year_id = $this->year->id;
            }
            $ya->days = $request->input('days')[$i];
            $ya->program_id = $program_id;
            $ya->save();

            $camper->yearattending_id = $ya->id;
            $staffs = CamperStaff::where('camper_id', $camper->id)->get();
            if (count($staffs) > 0) {
                foreach ($staffs as $staff) {
                    YearattendingStaff::updateOrCreate(['yearattending_id' => $ya->id, 'staffposition_id' => $staff->staffposition_id]);
                }
                CamperStaff::where('camper_id', $camper->id)->delete();
            }
        } else {
            $ya = Yearattending::where(['camper_id' => $camper->id, 'year_id' => $this->year->id])->first();
            if ($ya != null) {
                if ($request->input('days')[$i] == '0') {
                    YearattendingWorkshop::where('yearattending_id', $ya->id)->delete();
                    YearattendingStaff::where('yearattending_id', $ya->id)->delete();
                    $ya->delete();
                }
            }
        }

        return $camper;
    }
}
