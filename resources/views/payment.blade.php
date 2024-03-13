@extends('layouts.app')

@section('title')
    Account Statement
@endsection

@section('heading')
    Check back here to see your up-to-date statement, and mail payment via check or send online via PayPal.
@endsection

@section('content')

    <x-layouts.register :stepdata="$stepdata" step="4" previous="camperinfo" next="#">
        <div class="display-6 mt-3 border-bottom text-end">Your Balance</div>
        <form id="muusapayment" class="form-horizontal" role="form" method="POST"
              action="{{ route('payment.store', ['id' => request()->route('id')]) }}">
            @include('includes.flash')
            @if(count($years) == 1 && $years->first()->first()->year_id == $year->id)
                <x-statement :stepdata="$stepdata" :charges="$years->first()" :deposit="$deposit"/>
            @elseif(count($years) == 0)
                <h4 class="m-4">No charges present</h4>
            @else
                <x-navtabs :tabs="$years->keys()" option="year"
                           :active-tab="array_search($year->year, array_keys($years->toArray()))">
                    @foreach($years as $thisyear => $charges)
                        <div role="tabpanel" id="tab-{{ $thisyear }}"
                             class="tab-pane fade {{ $thisyear == $year->year ? 'active show' : '' }} ">
                            <x-statement :stepdata="$stepdata" :charges="$charges"/>
                        </div>
                    @endforeach
                </x-navtabs>
            @endif

            @if(request()->route()->hasParameter('id'))
                @can('is-super')

                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">Add Charge</div>
                                <div class="card-body">
                                    <x-form-group type="select" name="year_id" label="Fiscal Year">
                                        @foreach($fiscalyears as $fiscalyear)
                                            <option value="{{ $fiscalyear->id }}"
                                                @selected( $fiscalyear->year == date('Y'))>
                                                {{ $fiscalyear->year }}
                                            </option>
                                        @endforeach
                                    </x-form-group>

                                    <x-form-group type="select" name="chargetype_id" label="Chargetype">
                                        <option value="0">Choose a chargetype</option>
                                        @foreach($chargetypes as $chargetype)
                                            <option value="{{ $chargetype->id }}">{{ $chargetype->name }}</option>
                                        @endforeach
                                    </x-form-group>

                                    <x-form-group name="amount" label="Amount (negative for credit)"
                                                  placeholder="0.00"/>

                                    <div class="row align-self-center mb-3">
                                        <div class="container-md col-lg-6">
                                            <div class="form-outline datepicker" data-mdb-inline="true"
                                                 data-mdb-format="yyyy-mm-dd">
                                                <input id="timestamp" name="timestamp" data-mdb-toggle="datepicker"
                                                       class="form-control @error('timestamp') is-invalid @enderror"
                                                       value="{{ old('timestamp') }}" placeholder="yyyy-mm-dd"/>
                                                <label for="timestamp" class="form-label">Timestamp</label>
                                                <button class="datepicker-toggle-button" data-mdb-toggle="datepicker">
                                                    <i class="fas fa-calendar datepicker-toggle-icon me-1"></i>
                                                </button>
                                                @error('timestamp')
                                                <span
                                                    class="muusa-invalid-feedback"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <x-form-group name="memo" label="Memo"/>

                                </div>
                            </div>
                        </div>
                    </div>

                    <x-form-group type="submit" label="Save Changes" />
                @endcan
            @else
                @can('accept-paypal', $year)
                    <div
                        class="d-flex d-flex-row align-items-center note note-info text-black mb-5">
                        <label for="donation" class="visually-hidden">Donation</label>
                        <div class="input-group w-25">
                            <span class="input-group-text"><i class="fas fa-dollar-sign fa-fw"></i></span>
                            <input id="donation" name="donation"
                                   class="form-control amount-mask @error('donation') is-invalid @enderror"
                                   placeholder="Enter Donation Here" value="{{ old('donation') }}"/>
                        </div>

                        <div class="ms-3">
                            Please consider at least a $10.00 donation to the MUUSA Scholarship
                            fund.
                            <button type="submit" dusk="donate" class="btn btn-primary ms-2"
                                    data-mdb-toggle="tooltip"
                                    title="Using any of the PayPal buttons will also process your donation.">
                                Donate
                            </button>
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
                                                    <span class="input-group-text"><i
                                                            class="fas fa-dollar-sign fa-fw"></i></span>
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
                                <input
                                    class="form-check-input @error('addthree') is-invalid @enderror"
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
                            Please bring payment to checkin on the first day of
                            camp, {{ $year->checkin }}.
                            While we do accept VISA, Mastercard, Discover, we prefer a check (to
                            minimize fees).
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
    @if(!request()->route()->hasParameter('id') && Gate::allows('accept-paypal', $year))
        <script
            src="https://www.paypal.com/sdk/js?client-id={{ config('app.paypal_client_id') }}&enable-funding=venmo,paylater"></script>
        <script>
            const donation = document.getElementById('donation');
            if(donation) {
                window.addEvent(donation, 'change', function (e) {
                    let total = parseFloat(e.target.value);
                    const now = document.getElementById('amountNow');
                    if (now) {
                        total += parseFloat(now.innerText);
                    }
                    const arrival = document.getElementById('amountArrival');
                    if (total <= 0.0 && arrival) {
                        total += parseFloat(arrival.innerText);
                    }
                    document.getElementById('payment').value = Math.max(0, total).toFixed(2);
                    window.lastAmountMask.updateValue();
                });
            }

            paypal.Buttons({
                style: {
                    label: 'pay'
                },

                // Sets up the transaction when a payment button is clicked

                createOrder: (data, actions) => {
                    const alerts = document.querySelectorAll('div.alert');
                    for (let i = 0; i < alerts.length; i++) {
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
    @endif

@endsection
