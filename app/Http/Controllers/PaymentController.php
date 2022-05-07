<?php

namespace App\Http\Controllers;

use App\Enums\Chargetypename;
use App\Http\ByyearCamper;
use App\Http\ByyearCharge;
use App\Http\Camper;
use App\Http\Charge;
use App\Http\Chargetype;
use App\Http\Family;
use App\Http\Province;
use App\Http\ThisyearCamper;
use App\Http\ThisyearCharge;
use App\Http\Year;
use App\Http\Yearattending;
use App\Jobs\GenerateCharges;
use App\Mail\Confirm;
use App\PayPalClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
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

        return redirect()->action('PaymentController@index', ['id' => $id]);

    }

    public function store(Request $request)
    {
        $messages = [
            'donation.max' => 'Please use the Contact Us form at the top of the screen (Subject: Treasurer) to commit to a donation above $100.00.',
        ];

        $this->validate($request, [
            'donation' => 'nullable|min:0|max:100',
            'addthree' => 'nullable|accepted',
            'orderid' => 'alpha_num'
        ], $messages);

        $thiscamper = Auth::user()->camper;
        if ($thiscamper !== null) {
            if (!empty($request->input('orderid'))) {

                $before = Gate::allows('has-paid');
                $order = $this->getOrder($request->input('orderid'));
                $txn = $order->purchase_units[0]->payments->captures[0]->id;
                $charge = Charge::where(['camper_id' => $thiscamper->id,
                    'chargetype_id' => Chargetypename::PayPalPayment, 'memo' => $txn])->first();
                if (!$charge) {
                    $charge = new Charge();
                    $charge->camper_id = $thiscamper->id;
                    $charge->chargetype_id = Chargetypename::PayPalPayment;
                    $charge->memo = $txn;
                }
                $charge->amount = $order->purchase_units[0]->amount->value * -1;
                $charge->year_id = $this->year->id;
                $charge->timestamp = date("Y-m-d");
                $charge->created_at = Carbon::now();
                $charge->save();

                $paid = ThisyearCharge::where('family_id', Auth::user()->camper->family_id)
                    ->where(function ($query) {
                        $query->where('chargetype_id', Chargetypename::Deposit)->orWhere('amount', '<', 0);
                    })->get()->sum('amount');
                if (!$before && $paid <= 0) {
                    $request->session()->flash('newreg', true);
                    Mail::to(Auth::user()->email)
                        ->send(new Confirm($this->year, ThisyearCamper::where('family_id', Auth::user()->camper->family_id)->get()));
                }


                $family = Family::where('id', $thiscamper->family_id)->whereNull('city')->first();
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

                if (!empty($request->input('addthree'))) {
                    $addthree = Charge::where(['camper_id' => $thiscamper->id,
                        'memo' => 'Optional payment to offset PayPal Invoice #' . $txn])->first();
                    if (!$addthree) {
                        $addthree = new Charge();
                        $addthree->camper_id = $thiscamper->id;
                        $addthree->memo = 'Optional payment to offset PayPal Invoice #' . $txn;
                    }
                    $addthree->year_id = $this->year->id;
                    $addthree->chargetype_id = Chargetypename::PayPalServiceCharge;
                    $addthree->amount = $order->purchase_units[0]->amount->value / 1.03 * .03;
                    $addthree->timestamp = date("Y-m-d");
                    $addthree->parent_id = $charge->id;
                    $addthree->save();
                }


                $success = 'Payment received! You should receive a receipt via email for your records.';
                $campers = ByyearCamper::where('family_id', $thiscamper->family_id)
                    ->where('year', ((int)$this->year->year) - 1)->where('is_program_housing', '0')->get();
                if (!$this->year->is_live && count($campers) > 0 && ThisyearCharge::where('family_id', $thiscamper->family_id)
                        ->where(function ($query) {
                            $query->where('chargetype_id', 1003)->orWhere('amount', '<', '0');
                        })->get()->sum('amount') <= 0) {
                    foreach ($campers as $camper) {
                        Yearattending::where('camper_id', $camper->id)->where('year_id', $this->year->id)
                            ->whereNull('room_id')->update(['room_id' => $camper->room_id]);
                    }
                    GenerateCharges::dispatch($this->year->id);

                    $success = 'Payment received! By paying your deposit, your room from ' . ((int)($this->year->year) - 1)
                        . ' has been assigned. You should receive a receipt via email for your records.';
                }

                $request->session()->flash('success', $success);
            }

            if ($request->input('donation') > 0) {
                $donation = Charge::where(['camper_id' => $thiscamper->id, 'chargetype_id' => Chargetypename::Donation,
                    'year_id' => $this->year->id])->first();
                if (!$donation) {
                    $donation = new Charge();
                    $donation->camper_id = $thiscamper->id;
                    $donation->chargetype_id = Chargetypename::Donation;
                    $donation->year_id = $this->year->id;
                }
                $donation->amount = $request->input('donation');
                $donation->memo = 'MUUSA Scholarship Fund';
                $donation->timestamp = date("Y-m-d");
                if ($charge) {
                    $donation->parent_id = $charge->id;
                }
                $donation->save();
            }
        } else {
            $request->session()->flash('error', 'Payment was not processed by MUUSA. If you believe that PayPal has transmitted funds, please contact the Treasurer so we can confirm and update your account.');
        }

        return redirect()->action('PaymentController@index');

    }

    public function index(Request $request, $id = null)
    {
        $chargetypes = array();
        if ($id && Gate::allows('is-council')) {
            $request->session()->flash('camper', Camper::findOrFail($id));
            $chargetypes = Chargetype::where('is_shown', 1)->get();
        }
        $token = env('PAYPAL_CLIENT');
        $deposit = 0.0;

        if (!isset(Auth::user()->camper)) {
            $request->session()->flash('warning', 'Please fill out your camper information to continue.');
            return redirect()->action('CamperController@index');
        } else {
            if ($id && Gate::allows('is-council')) {
                $family_id = $request->session()->get('camper')->family_id;
                $years = ByyearCharge::where('family_id', $family_id)->orderBy('timestamp')->orderBy('amount', 'desc')->get()->groupBy('year');
            } else {
                $years = ThisyearCharge::where('family_id', Auth::user()->camper->family_id)->orderBy('timestamp')->orderBy('amount', 'desc')->get();
                if (count($years) == 0) {
                    $request->session()->flash('warning', 'Please choose which campers are attending this year.');
                    return redirect()->action('CamperController@index');
                }
                foreach ($years as $charge) {
                    if ($charge->amount < 0 || $charge->chargetype_id == Chargetypename::Deposit) {
                        $deposit += $charge->amount;
                    }
                }
                $years = $years->groupBy('year');
            }
        }

        return view('payment', ['token' => $token, 'years' => $years,
            'fiscalyears' => Year::orderBy('year', 'desc')->get(), 'deposit' => $deposit, 'chargetypes' => $chargetypes]);
    }

    protected function getOrder($orderId)
    {
        $client = PayPalClient::client();
        $response = $client->execute(new OrdersGetRequest($orderId));
        return $response->result;
    }
}
