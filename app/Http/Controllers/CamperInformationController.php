<?php

namespace App\Http\Controllers;

use App\Enums\Programname;
use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\Foodoption;
use App\Models\Program;
use App\Models\Pronoun;
use App\Models\User;
use App\Models\Yearattending;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use function count;

class CamperInformationController extends Controller
{
    private const LAST_PROGRAM_SELECT = '(SELECT program_id FROM yearsattending yap, years y WHERE yap.year_id=y.id AND yearsattending.camper_id=yap.camper_id AND y.year<? ORDER BY year DESC LIMIT 1) lastprogram_id';

    private $messages = ['pronoun_id.*.exists' => 'Please choose the pronoun(s) you use.',
        'sex_id.*.min' => 'Please choose a sex.',
        'gender_id.*.min' => 'Please choose a gender identity.',
        'firstname.*.required' => 'Please enter a first name.',
        'lastname.*.required' => 'Please enter a last name.',
        'email.*.email' => 'Please enter a valid email address.',
        'email.*.distinct' => 'Please do not use the same email address for multiple campers.',
        'email.*.unique' => 'This email address has already been taken. Please contact us to merge your records.',
        'phonenbr.*.regex' => 'Please enter your phone number in 800-555-1212 format. International numbers are not accepted.',
        'birthdate.*.required' => 'Please enter your birthdate in 2016-12-31 format.',
        'birthdate.*.regex' => 'Please enter your birthdate in 2016-12-31 format.'];

    public function store(Request $request, $id = null)
    {

        try {
            $this->validate($request, [
                'days.*' => 'between:0,8',
                'pronoun_id.*' => 'exists:pronouns,id',
                'sex_id.*' => 'min:1',
                'gender_id.*' => 'min:1',
                'firstname.*' => 'required|max:255',
                'lastname.*' => 'required|max:255',
                'email.*' => 'nullable|email|max:255|distinct',
                'phonenbr.*' => 'nullable|regex:/^\d{3}-\d{3}-\d{4}$/',
                'birthdate.*' => 'required|regex:/^\d{4}-\d{2}-\d{2}$/',
                'program_id.*' => 'exists:programs,id',
                'roommate.*' => 'max:255',
                'sponsor.*' => 'max:255',
                'church_id.*' => 'exists:churches,id',
                'foodoption_id.*' => 'exists:foodoptions,id',
            ], $this->messages);

            for ($i = 0; $i < count($request->input('id')); $i++) {
                $camper_id = (int)($request->input('id')[$i]);
                $camper = Camper::findOrFail($camper_id);

                $this->validate($request, [
                    'email.' . $i => 'nullable|unique:campers,email,' . $camper_id,
                ], $this->messages);

                $this->validate($request, [
                    'email.' . $i => 'unique:users,email' . (isset($camper->user) ? ',' . $camper->user->id : ''),
                ], $this->messages);

                if (($id && Gate::allows('is-super')) || $camper->family_id == Auth::user()->camper->family_id) {
                    $this->upsertCamper($request, $camper, $i);
                }

            }
        } catch (ValidationException $validationException) {
            $matches = array();
            foreach (array_keys($validationException->errors()) as $key)
                if (preg_match('/(\d+)$/', $key, $m))
                    $matches[] = $m[1];
            $request->session()->flash('activeTab', count($matches) > 0 ? min($matches) : 0);

            throw $validationException;
        }

        GenerateCharges::dispatch($this->year->id);

        $request->session()->flash('success', 'Your information has been saved successfully.');
        return redirect()->route('payment.index', ['id' => $id]);//camperinfo.index', ['id' => $id]); per Margaret
    }

    public function index(Request $request, $id = null)
    {
        $family_id = $this->getFamilyId($id);

        $campers = Camper::where('family_id', $family_id)
            ->with(['yearsattending' => function ($query) {
                $query->selectRaw('*, ' . self::LAST_PROGRAM_SELECT, [$this->year->year])
                    ->where('year_id', $this->year->id);
            }])->orderBy('birthdate')->get();

        if (count($campers) == 0) {
            $request->session()->flash('warning', 'Please identify the campers before proceeding.');
            return redirect()->route('camperselect.index', ['id' => $id]);
        }

        // Find most recent program_id and reset for 18-20 YA
        foreach ($campers as $camper) {
            $program_id = 0;
            if (isset($camper->yearsattending[0])) {
                $ya = $camper->yearsattending[0];
                if (isset($ya->program_id)) {
                    $program_id = $ya->program_id;
                } elseif (isset($ya->lastprogram_id)) {
                    $program_id = $ya->lastprogram_id;
                }
                if ($program_id == Programname::YoungAdultUnderAge) $program_id = Programname::YoungAdult;
                $camper->yearsattending[0]->program_id = $program_id;
            }
        }

        return view('camperinfo', ['pronouns' => Pronoun::all(), 'foodoptions' => Foodoption::all(),
            'campers' => $campers, 'programs' => Program::whereNotNull('title')->orderBy('order')->get(),
            'stepdata' => $this->getStepData($id), 'isReadonly' => false]);

    }

    private function upsertCamper(Request $request, Camper $camper, $i)
    {
        $camper->pronoun_id = $request->input('pronoun_id')[$i];
        $camper->sex_id = $request->input('sex_id')[$i];
        $camper->gender_id = $request->input('gender_id')[$i];
        $camper->firstname = $request->input('firstname')[$i];
        $camper->lastname = $request->input('lastname')[$i];

        if ($request->input('email')[$i] != '') {
            if (isset($camper->email) && $camper->email != $request->input('email')[$i]) {
                $user = User::where('email', $camper->email)->first();
                if (isset($user)) {
                    $user->email = $request->input('email')[$i];
                    $user->save();
                }
            }
        }
        $camper->email = $request->input('email')[$i];
        if (isset($request->input('phonenbr')[$i])) {
            $camper->phonenbr = preg_replace('/-/', '', $request->input('phonenbr')[$i]);
        } else {
            $camper->phonenbr = null;
        }
        $camper->birthdate = $request->input('birthdate')[$i];

        $program_id = $request->input('program_id')[$i];
        if ($program_id == Programname::YoungAdult && Carbon::createFromFormat('Y-m-d', $camper->birthdate)->diffInYears(Carbon::createFromFormat('Y-m-d', $this->year->checkin)) < 21) {
            $program_id = Programname::YoungAdultUnderAge;
        }

        $camper->roommate = $request->input('roommate')[$i];
        $camper->sponsor = $request->input('sponsor')[$i];
        $camper->church_id = $request->input('churchid')[$i];
        $camper->is_handicap = in_array($camper->id, $request->input('is_handicap') ?? array()) ? 1 : 0;
        $camper->foodoption_id = $request->input('foodoption_id')[$i];

        $camper->save();

        $ya = Yearattending::where('camper_id', $camper->id)->where('year_id', $this->year->id)->first();
        if ($ya) {
            if (Gate::allows('is-super') && (int)$request->input('days')[$i] > 0) {
                $ya->days = $request->input('days')[$i];
            }
            $ya->program_id = $program_id;

            $room_id = null;
            if ($request->input('room_id')[$i] != 0) $room_id = $request->input('room_id')[$i];
            if($program_id == Programname::Burt || $program_id == Programname::Meyer) $room_id = 1175;
            $ya->room_id = $room_id;

            $ya->save();
        }

        return $camper;
    }
}
