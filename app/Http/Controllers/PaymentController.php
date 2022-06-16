<?php

namespace App\Http\Controllers;

use App\Enums\Chargetypename;
use App\Jobs\GenerateCharges;
use App\Mail\Confirm;
use App\Models\ByyearCamper;
use App\Models\Charge;
use App\Models\Family;
use App\Models\Province;
use App\Models\ThisyearCamper;
use App\Models\ThisyearCharge;
use App\Models\Year;
use App\Models\Yearattending;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use function date;
use function preg_match;
use function redirect;

class PaymentController extends Controller
{

    public function write(Request $request, $id)
    {

        foreach ($request->all() as $key => $value) {
            $matches = array();
            if (preg_match('/(delete|chargetype_id|amount|timestamp|memo)-(\d+)/', $key, $matches)) {
                $charge = Charge::findOrFail($matches[2]);
                if ($matches[1] == 'delete') {
                    if ($value == 'on') {
                        $charge->delete();
                    }
                } else {
                    $charge->{$matches[1]} = $value;
                    $charge->save();
                }
            }
        }

        if ($request->input('amount') != '') {

            $this->validate($request, [
                'chargetype_id' => 'exists:chargetypes,id',
                'amount' => 'nullable|numeric',
                'timestamp' => 'date_format:Y-m-d',
                'memo' => 'nullable|max:255'
            ]);

            $charge = new Charge();
            $charge->camper_id = $id;
            $charge->chargetype_id = $request->input('chargetype_id');
            $charge->amount = (float)$request->input('amount');
            $charge->timestamp = $request->input('timestamp');
            $charge->memo = $request->input('memo');
            $charge->year_id = $request->input('year_id');
            $charge->save();
        }

        $request->session()->flash('success', 'Rocking it today!');

        return redirect()->action([PaymentController::class, 'index'], ['id' => $id]);

    }

    public function store(Request $request)
    {
        $messages = [
            'donation.max' => 'Please use the Contact Us form at the top of the screen (Subject: Treasurer) to commit to a donation above $1,000.00.',
        ];

        $this->validate($request, [
            'donation' => 'nullable|numeric|min:0|max:1000',
            'orderid' => 'nullable|alpha_num'
        ], $messages);

        $charge = null;
        $success = "";
        if (!empty($request->input('orderid'))) {

            $before = Gate::allows('has-paid');
            $order = $this->getOrder($request->input('orderid'));
            $txn = $order["purchase_units"][0]["payments"]["captures"][0]["id"];
            $charge = Charge::updateOrCreate(['camper_id' => Auth::user()->camper->id,
                'chargetype_id' => Chargetypename::PayPalPayment, 'memo' => $txn],
                ['amount' => $order["purchase_units"][0]["amount"]["value"] * -1, 'year_id' => $this->year->id,
                    'timestamp' => date("Y-m-d")]);

            $success = 'Payment received!';

            $paid = ThisyearCharge::where('family_id', Auth::user()->camper->family_id)
                ->where(function ($query) {
                    $query->where('chargetype_id', Chargetypename::Deposit)->orWhere('amount', '<', 0);
                })->get()->sum('amount');
            if (!$before && $paid <= 0) {
                $success .= " You should receive a receipt via email for your records" .
                    $campers = ByyearCamper::where('family_id', Auth::user()->camper->family_id)
                        ->where('year', ((int)$this->year->year) - 1)->where('is_program_housing', '0')->get();
                if (!$this->year->is_live && count($campers) > 0) {
                    foreach ($campers as $camper) {
                        Yearattending::whereIn('camper_id', $camper->id)->where('year_id', $this->year->id)
                            ->whereNull('room_id')->update(['room_id' => $camper->room_id]);
                    }

                    $success .= ' By paying your deposit, your room from ' . ((int)($this->year->year) - 1)
                        . ' has been assigned.';
                }

                Mail::to(Auth::user()->email)
                    ->send(new Confirm($this->year, ThisyearCamper::where('family_id', Auth::user()->camper->family_id)->get()));
            }
            GenerateCharges::dispatch($this->year->id);


            $family = Family::where('id', Auth::user()->camper->family_id)->where('is_address_current', 0)->first();
            if ($family) {
                $family->address1 = $order->purchase_units[0]->shipping->address->address_line_1;
                if (isset($order->purchase_units[0]->shipping->address->address_line_2)) {
                    $family->address2 = $order->purchase_units[0]->shipping->address->address_line_2;
                }
                $family->city = $order->purchase_units[0]->shipping->address->admin_area_2;
                $family->province_id = Province::where('code', $order->purchase_units[0]->shipping->address->admin_area_1)->first()->id;
                $family->zipcd = $order->purchase_units[0]->shipping->address->postal_code;
                $family->country = $order->purchase_units[0]->shipping->address->country_code;
                $family->is_address_current = 1;
                $family->save();
            }

            if ($request->input('addthree') == '1') {
                $addthree = Charge::updateOrCreate(['camper_id' => Auth::user()->camper->id,
                    'memo' => 'Optional payment to offset PayPal Invoice #' . $txn],
                    ['year_id' => $this->year->id, 'chargetype_id' => Chargetypename::PayPalServiceCharge,
                        'amount' => $order["purchase_units"][0]["amount"]["value"] / 1.03 * .03,
                        'timestamp' => date("Y-m-d"), 'parent_id' => $charge->id]);
                $addthree->save();
            }
        } else {
            $request->session()->flash('warning', 'Payment was not processed by MUUSA. If you believe that PayPal has transmitted funds, please contact the Treasurer.');
        }

        if ($request->input('donation') > 0) {
            $donation = Charge::updateOrCreate(['camper_id' => Auth::user()->camper->id,
                'chargetype_id' => Chargetypename::Donation, 'year_id' => $this->year->id,
                'timestamp' => date("Y-m-d"), 'amount' => $request->input('donation')]);
            $donation->memo = 'MUUSA Scholarship Fund';
            $donation->timestamp = date("Y-m-d");
            if ($charge) {
                $donation->parent_id = $charge->id;
            }
            $donation->save();
            $success .= " Thank you for your donation. Your generosity will help others attend MUUSA.";
        }

        $request->session()->flash('success', $success);
        return redirect()->action([PaymentController::class, 'index']);
    }

    public function index(Request $request, $id = null)
    {
        $chargetypes = array();
//        if ($id && Gate::allows('is-council')) {
//            $request->session()->flash('camper', Camper::findOrFail($id));
//            $chargetypes = Chargetype::where('is_shown', 1)->get();
//        }
        $deposit = 0.0;


//        if ($id && Gate::allows('is-council')) {
//            $family_id = Camper::findOrFail($id)->family_id;
//            $years = ByyearCharge::where('family_id', $family_id)->orderBy('timestamp')->orderBy('amount', 'desc')->get()->groupBy('year');
//        } else {
        $family_id = parent::getFamilyId();
        $years = ThisyearCharge::where('family_id', $family_id)->orderBy('timestamp')->orderBy('amount', 'desc')->get();
        foreach ($years as $charge) {
            if ($charge->amount < 0 || $charge->chargetype_id == Chargetypename::Deposit) {
                $deposit += $charge->amount;
            }
        }
        $years = $years->groupBy('year');
//        }
        return view('payment', ['years' => $years, 'fiscalyears' => Year::orderBy('year', 'desc')->get(),
            'deposit' => $deposit, 'chargetypes' => $chargetypes, 'stepdata' => parent::getStepData(),
            'pastJune' => Carbon::now()->lt(Carbon::create($this->year->year, 5, 31))]);
    }

    protected function getOrder($orderId)
    {
        $provider = new PayPalClient;
        $provider->getAccessToken();
        return $provider->showOrderDetails($orderId);
    }
}
