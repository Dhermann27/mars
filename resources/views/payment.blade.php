@extends('layouts.app')

@section('title')
    Account Statement
@endsection

@section('heading')
    Check back here to see your up-to-date statement, and mail payment via check or send online via PayPal.
@endsection

@section('content')

    <x-layouts.register :stepdata="$stepdata" step="4" previous="camperinfo" next="roomselection">
        <div class="display-6 mt-3 border-bottom text-end">Your Balance</div>
        <form id="muusapayment" class="form-horizontal" role="form" method="POST"
              action="{{ route('payment.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
            @include('includes.flash')
            @if(count($years) == 1 && $years->first()->first()->year_id == $year->id)
                <x-statement :stepdata="$stepdata" :charges="$years->first()" :deposit="$deposit"/>
            @elseif(count($years) == 0)
                <h4 class="m-4">No charges present</h4>
            @else
                <x-navtabs :tabs="$years->sortKeys()->keys()" option="year">
                    @foreach($years->sortKeys() as $thisyear => $charges)
                        <div role="tabpanel" id="year-{{ $thisyear }}"
                             class="tab-pane fade {{ $loop->index == 0 ? 'active show' : '' }}">
                            <x-statement :stepdata="$stepdata" :charges="$charges"/>
                        </div>
                    @endforeach
                </x-navtabs>
            @endif

            {{--            @if(session()->has('camper') && Gate::allows('is-super'))--}}

            {{--                <div class="well">--}}
            {{--                    <h4>Add New Charge</h4>--}}
            {{--                    <div class="form-group row @error('year_id') has-danger @enderror">--}}
            {{--                        <label for="year_id" class="col-md-4 col-form-label text-md-right">--}}
            {{--                            Fiscal Year--}}
            {{--                        </label>--}}

            {{--                        <div class="col-md-6">--}}
            {{--                            <select id="year_id" name="year_id"--}}
            {{--                                    class="form-control @error('year_id') is-invalid @enderror">--}}
            {{--                                @foreach($fiscalyears as $year)--}}
            {{--                                    <option--}}
            {{--                                        value="{{ $year->id }}" @selected(old('year_id') == $year->id)>--}}
            {{--                                        {{ $year->year }}--}}
            {{--                                    </option>--}}
            {{--                                @endforeach--}}
            {{--                            </select>--}}

            {{--                            @error('year_id')--}}
            {{--                            <span class="invalid-feedback" role="alert">--}}
            {{--                                                <strong>{{ $message }}</strong>--}}
            {{--                                            </span>--}}
            {{--                            @enderror--}}
            {{--                        </div>--}}
            {{--                    </div>--}}

            {{--                    @include('includes.formgroup', ['type' => 'select',--}}
            {{--                        'label' => 'Chargetype', 'attribs' => ['name' => 'chargetype_id'],--}}
            {{--                        'default' => 'Choose a chargetype', 'list' => $chargetypes, 'option' => 'name'])--}}

            {{--                    @include('includes.formgroup', ['label' => 'Amount', 'attribs' => ['name' => 'amount']])--}}

            {{--                    <div class="form-group row @error('timestamp') has-danger @enderror">--}}

            {{--                        <label for="timestamp" class="col-md-4 col-form-label text-md-right">--}}
            {{--                            Timestamp (yyyy-mm-dd)--}}
            {{--                        </label>--}}
            {{--                        <div class="col-md-6">--}}
            {{--                            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"--}}
            {{--                                 data-date-autoclose="true">--}}
            {{--                                <input id="timestamp" type="text" class="form-control" name="timestamp"--}}
            {{--                                       value="{{ old('timestamp', date('Y-m-d')) }}">--}}
            {{--                                <div class="input-group-append">--}}
            {{--                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>--}}
            {{--                                </div>--}}
            {{--                                <div class="input-group-addon">--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                            @error('timestamp')--}}
            {{--                            <span class="invalid-feedback" role="alert">--}}
            {{--                                                <strong>{{ $message }}</strong>--}}
            {{--                                            </span>--}}
            {{--                            @enderror--}}
            {{--                        </div>--}}
            {{--                    </div>--}}

            {{--                    @include('includes.formgroup', ['label' => 'Memo', 'attribs' => ['name' => 'memo']])--}}

            {{--                    @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes']])--}}
            {{--                </div>--}}
            {{--            @else if($year->is_accept_paypal)--}}

            @if(!session()->has('camper'))
                @can('accept-paypal', $year)
                    <div class="d-flex d-flex-row align-items-center note note-info text-black mb-5">
                        <label for="donation" class="visually-hidden">Donation</label>
                        <div class="input-group w-25">
                            <span class="input-group-text"><i class="fas fa-dollar-sign fa-fw"></i></span>
                            <input id="donation" name="donation"
                                   class="form-control amount-mask @error('donation') is-invalid @enderror"
                                   placeholder="Enter Donation Here" value="{{ old('donation') }}"/>
                        </div>

                        <div class="ms-3">
                            Please consider at least a $10.00 donation to the MUUSA Scholarship fund.
                            <input type="submit" value="Donate" class="btn btn-primary ms-2" data-mdb-toggle="tooltip"
                                title="Using any of the PayPal buttons will also process your donation."/>
                        </div>
                    </div>
                    @error('donation')
                    <span class="muusa-invalid-feedback" role="alert" style="margin-top: -2.75rem;">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <div class="row p-7">
                        <div class="col-md-6">
                            @if($pastJune)
                                <h4>To Pay via Mail:</h4>
                                Make checks payable to <strong>MUUSA, Inc.</strong><br/>
                                Mail check by May 31, {{ $year->year }} to<br/>
                                MUUSA, Inc.<br/>1371 Amesbury Dr<br/>
                                Cincinnati, OH 45231<br/> <br/>
                            @else
                                <h4>To Pay without Fees:</h4>
                                Make checks payable to <strong>MUUSA, Inc.</strong><br/>
                                and bring them to checkin at camp.<br/>
                                Please do not mail checks past June 1.
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h4>To Pay via PayPal:</h4>
                            <label for="donation" class="visually-hidden">Donation</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-dollar-sign fa-fw"></i></span>
                                <input id="payment" name="payment"
                                       class="form-control amount-mask @error('payment') is-invalid @enderror"
                                       placeholder="Or enter another amount..."
                                       value="{{ $deposit > 0 || count($years) == 0 ? number_format($deposit, 2, '.', '') : number_format(max($years->first()->sum('amount'), 0), 2, '.', '')}}"/>
                                @error('payment')
                                <span class="muusa-invalid-feedback"
                                      role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input type="hidden" value="0" name="addthree">
                                <input class="form-check-input @error('addthree') is-invalid @enderror"
                                       type="checkbox" id="addthree" name="addthree"
                                       value="1" @checked(old('addthree')) />
                                <label class="form-check-label" for="addthree">
                                    Add 3% to my payment to cover the PayPal service fee
                                </label>
                                @error('addthree')
                                <span class="muusa-invalid-feedback"
                                      role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <input type="hidden" id="orderid" name="orderid"/>
                            <div id="paypal-button"></div>
                        </div>
                    </div>
                @else
                    <div class="row p-7 justify-content-center my-4">
                        <div class="col-md-6">
                            <h4>To Pay Your Bill:</h4>
                            Please bring payment to checkin on the first day of camp, {{ $year->checkin }}.
                            While we do accept VISA, Mastercard, Discover, we prefer a check (to minimize fees).
                        </div>
                    </div>
                @endcan
            @endif
        </form>
    </x-layouts.register>

    <div class="modal fade bottom" id="paypalModal" data-mdb-backdrop="static"
         data-mdb-keyboard="false" tabindex="-1" aria-labelledby="paypalModal" aria-hidden="true">
        <div class="modal-dialog modal-frame modal-bottom">
            <div class="modal-content rounded-0">
                <div class="modal-body py-1">
                    <div class="d-flex justify-content-center align-items-center my-3">
                        <h5>We are processing your PayPal payment. <i class="fa fa-spinner-third fa-spin fa-xl m-1"></i>
                            Please do not navigate away from this screen. </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    @can('accept-paypal', $year)
        <script
            src="https://www.paypal.com/sdk/js?client-id={{ config('app.paypal_client_id') }}&enable-funding=venmo,paylater"></script>
        <script>
            window.addEvent(document.getElementById('donation'), 'change', function (e) {
                let total = parseFloat(e.target.value);
                if(typeof document.getElementById('amountNow') != "undefined") {
                    total += parseFloat(document.getElementById('amountNow').innerText);
                }
                if(total <= 0.0 && typeof document.getElementById('amountArrival') != "undefined") {
                    total += parseFloat(document.getElementById('amountArrival').innerText);
                }
                document.getElementById('payment').value = Math.max(0, total).toFixed(2);
                window.lastAmountMask.updateValue();
            });

            paypal.Buttons({
                style: {
                    label: 'pay'
                },

                // Sets up the transaction when a payment button is clicked

                createOrder: (data, actions) => {
                    const alerts = document.querySelectorAll('div.alert');
                    for(let i=0; i<alerts.length; i++) {
                        alerts[0].style.display = 'none';
                    }
                    var amt = parseFloat(document.getElementById('payment').value);
                    if (amt == 0.0) return false;
                    if (document.getElementById('addthree').checked) amt *= 1.03;
                    if (amt < 0) amt *= -1;

                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: amt.toFixed(2)
                            }
                        }]
                    });
                },

                onCancel: () => {
                    window.paypalModal.hide();
                },

                onError: () => {
                    window.paypalModal.hide();
                },

                onApprove: (data, actions) => {
                    window.paypalModal.show();
                    return actions.order.capture().then(function (orderData) {
                        document.getElementById('orderid').value = orderData?.id;
                        window.removeEvent(window, 'beforeunload', checkDirty);
                        document.getElementById('muusapayment').submit();
                    });
                }
            }).render('#paypal-button');

        </script>
    @endcan

@endsection
