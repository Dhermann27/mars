@extends('layouts.app')

@section('title')
    Bank Deposits
@endsection

@section('content')
    <x-navtabs :tabs="$chargetypes" option="name">
        @foreach($chargetypes as $chargetype)
            <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $chargetype->id }}"
                 role="tabpanel">
                <form id="formdeposits" role="form" method="POST"
                      action="{{ route('reports.deposits.mark', ['id' => $chargetype->id]) }}">
                    @include('includes.flash')
                    <div class="accordion" id="accordion-{{ $chargetype->id }}">
                        @forelse($chargetype->byyearcharges->where('year', '>', $year->year-2)->groupBy('deposited_date')->sortKeys() as $deposited_date => $charges)
                            <x-accordioncard :id="'date-' . $chargetype->id . '-' . $deposited_date"
                                             :show="$loop->first"
                                             :parent="$chargetype->id"
                                             :heading="$deposited_date ? 'Deposited on ' . $deposited_date : 'Undeposited'">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Camper Name</th>
                                        <th>Amount</th>
                                        <th>Guarantee Amount</th>
                                        <th>Donation Amount</th>
                                        <th>Service Charge Amount</th>
                                        <th>Timestamp</th>
                                        <th>Memo</th>
                                        <th>Controls</th>
                                        @if(Gate::allows('is-super') && !$deposited_date)
                                            <th>Deposited Today?</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($charges->sortBy('timestamp') as $charge)
                                        <tr>
                                            <td>{{ $charge->camper->firstname }} {{ $charge->camper->lastname }}</td>
                                            <td class="amount">{{ number_format(abs($charge->amount), 2) }}</td>
                                            <td class="amount">{{ number_format(abs($charge->amount + $charge->children->sum('amount')), 2) }}</td>
                                            <td class="amount">{{ number_format(abs($charge->children->where('chargetype_id', \App\Enums\Chargetypename::Donation)->sum('amount')), 2) }}</td>
                                            <td class="amount">{{ number_format(abs($charge->children->where('chargetype_id', \App\Enums\Chargetypename::PayPalServiceCharge)->sum('amount')), 2) }}</td>
                                            <td>{{ $charge->timestamp }}</td>
                                            <td>{{ $charge->memo }}</td>
                                            <td>
                                                <x-admin.controls :id="$charge->camper->id" />
                                            </td>
                                            @if(Gate::allows('is-super') && !$deposited_date)
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                               dusk="mark{{ $charge->id }}" name="mark[]" id="mark[]"
                                                               value="{{ $charge->id }}"/>
                                                        <label class="form-check-label visually-hidden"
                                                               for="mark[]">Mark as Deposited?</label>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="9" class="text-md-end ">
                                            <strong>Total Deposit:
                                                ${{ number_format(abs($charges->sum('amount')), 2) }}
                                            </strong>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                                @if(Gate::allows('is-super') && !$deposited_date)
                                    <div class="mt-2">
                                        <x-form-group type="submit" label="Mark as Deposited"/>
                                    </div>
                                @endif
                            </x-accordioncard>
                        @empty
                            <h3 class="ml-5">No charges found for this chargetype</h3>
                        @endforelse
                    </div>
                </form>
            </div>
        @endforeach
    </x-navtabs>
@endsection

@section('script')
    <script type="text/javascript">
        window.addEvent(document.getElementById('formdeposits'), 'submit', function (e) {
            checks = document.querySelectorAll('input[type="checkbox"]:checked');
            if (checks.length === 0 && !confirm("You are about to mark all charges of this chargetype as deposited today. Is this correct?")) {
                return false;
            }
            return true;
        });
    </script>
@endsection

