<?php

namespace App\Http\Controllers;

use App\Enums\Chargetypename;
use App\Models\Medicalresponse;
use App\Models\ThisyearCharge;
use App\Models\Yearattending;
use App\Models\YearattendingWorkshop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $steps = array('campersSelected' => 0, 'isAddressCurrent' => null, 'isCamperDetail' => null,
        'amountDueNow' => null, 'workshopsSelected' => null, 'isRoomsSelected' => null,
        'nametagsCustomized' => null, 'medicalResponsesNeeded' => null);

    public function index()
    {
        if (Auth::user()->camper && Auth::user()->camper->family) {
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
                            return $camper->birthdate == null;
                        })->count() == 0 && $yearsattending->whereNull('program_id')->count() == 0;
                    if ($this->steps["isCamperDetail"]) {
                        $this->steps["amountDueArrival"] = $charges->sum('amount');
                        $this->steps["amountDueNow"] = $charges->filter(function ($charge) {
                            return $charge->chargetype_id == Chargetypename::Deposit || $charge->amount < 0;
                        })->sum('amount');
                        if ($this->steps["amountDueNow"] <= 0) {
                            $this->runPostPaymentChecks($yearsattending, $campers);
                        }
                    }
                }
            }
        }
        return view('dashboard', ['stepdata' => $this->steps]);
    }

    /**
     * @param $yearsattending
     * @param $campers
     * @return void
     */
    public function runPostPaymentChecks($yearsattending, $campers): void
    {
        $this->steps["isRoomsSelected"] = $yearsattending->filter(function ($yearattending) {
                return $yearattending->program->is_program_housing == 1 || $yearattending->room_id != null;
            })->count() == $yearsattending->count();
        $this->steps["workshopsSelected"] = YearattendingWorkshop::whereIn('yearattending_id', $yearsattending->pluck('id'))
            ->count();
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
