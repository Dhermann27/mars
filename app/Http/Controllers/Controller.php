<?php

namespace App\Http\Controllers;

use App\Enums\Chargetypename;
use App\Models\Camper;
use App\Models\Family;
use App\Models\Medicalresponse;
use App\Models\ThisyearCharge;
use App\Models\Year;
use App\Models\Yearattending;
use App\Models\YearattendingWorkshop;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    protected $year;
    private $steps = array('campersSelected' => 0, 'isAddressCurrent' => null, 'isCamperDetail' => null,
        'amountDueNow' => null, 'workshopsSelected' => null, 'isRoomsSelected' => null,
        'nametagsCustomized' => null, 'medicalResponsesNeeded' => null);

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->year = Year::where('is_current', '1')->first();
        View::share('year', $this->year);

    }

    /**
     * If User has an associated camper, get the family_id, otherwise create one
     * @return int
     */
    public function getFamilyId($id = null): int
    {
        if (isset($id) && Gate::allows('is-council')) {
            $family_id = ($id != 0) ? Camper::findOrFail($id)->family_id : $this->createFamilyAndCamper();
        } else {
            if (!isset(Auth::user()->camper)) {
                $family_id = $this->createFamilyAndCamper(Auth::user()->email);
                Auth::user()->load('camper');
            } else {
                $family_id = Auth::user()->camper->family_id;
            }
        }
        return $family_id;
    }

    private function createFamilyAndCamper($email = null) :int {
        $family = new Family();
        $family->save();
        $family_id = $family->id;
        $newcamper = new Camper();
        $newcamper->family_id = $family->id;
        $newcamper->email = $email;
        if (config('app.name') == 'MUUSADusk') $newcamper->roommate = __FUNCTION__;
        $newcamper->save();
        return $family_id;
    }

    public function getStepData()
    {
        if (isset(Auth::user()->camper->family)) {
            $family = Auth::user()->camper->family;
            $campers = $family->campers;
            $yearsattending = Yearattending::where('year_id', $this->year->id)
                ->whereIn('camper_id', $campers->pluck('id'))->with('program')->get();
            $charges = ThisyearCharge::where('family_id', $family->id)->get();

            $this->steps["campersSelected"] = $yearsattending->count();
            if ($this->steps["campersSelected"] > 0) {
                $this->steps["isAddressCurrent"] = $family->is_address_current == 1 && $family->address1 != 'NEED ADDRESS';
                if ($this->steps["isAddressCurrent"]) {
                    $this->steps["isCamperDetail"] = $campers->filter(function ($camper) {
                            return $camper->birthdate == null || $camper->foodoption_id == null;
                        })->count() == 0 && $yearsattending->whereNull('program_id')->count() == 0;
                    if ($this->steps["isCamperDetail"]) {
                        $this->steps["amountDueArrival"] = max($charges->sum('amount'), 0);
                        $this->steps["amountDueNow"] = $charges->filter(function ($charge) {
                            return $charge->chargetype_id == Chargetypename::Deposit || $charge->amount < 0;
                        })->sum('amount');
                        if ($this->year->is_brochure && $this->steps["amountDueNow"] <= 0) {
                            $this->runPostPaymentChecks($yearsattending, $campers);
                        }
                    }
                }
            }
        }
        return $this->steps;
    }

    /**
     * @param $yearsattending
     * @param $campers
     * @return void
     */
    private function runPostPaymentChecks($yearsattending, $campers): void
    {
        $this->steps["isRoomsSelected"] = $yearsattending->filter(function ($yearattending) {
                return $yearattending->program->is_program_housing == 1 || $yearattending->room_id != null;
            })->count() == $yearsattending->count();
        $this->steps["workshopsSelected"] = YearattendingWorkshop::whereIn('yearattending_id', $yearsattending->filter(function ($yearattending) {
            return $yearattending->program->is_minor == 0;
        })->pluck('id'))->count();
        $this->steps["nametagsCustomized"] = $yearsattending->filter(function ($yearattending) {
            return $yearattending->nametag != 222215521;
        })->count();
        $children = $campers->filter(function ($camper) {
            return Carbon::parse($camper->birthdate)->diffInYears($this->year->checkin) < 18;
        });
        if ($children->count() > 0) {
            $this->steps["medicalResponsesNeeded"] = $children->count() -
                Medicalresponse::whereIn('yearattending_id',
                    $yearsattending->whereIn('camper_id', $children->pluck('id'))->pluck('id'))->count();
        }
    }
}
