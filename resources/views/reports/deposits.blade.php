@extends('layouts.app')

@section('title')
    Bank Deposits
@endsection

@section('content')
    @component('components.navtabs', ['tabs' => $chargetypes, 'id'=> 'id', 'option' => 'name'])
        @foreach($chargetypes as $chargetype)
            <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $chargetype->id }}"
                 role="tabpanel">
                <form class="deposits" role="form" method="POST"
                      action="{{ route('reports.deposits.mark', ['id' => $chargetype->id]) }}">
                    @include('includes.flash')
                    <div class="accordion" id="accordion-{{ $chargetype->id }}">
                        @forelse($chargetype->byyearcharges->where('year', '>', $year->year-2)->groupBy('deposited_date')->sortKeys() as $deposited_date => $charges)
                            @component('components.accordioncard', ['id' => $deposited_date, 'show' => $loop->first, 'heading' => $deposited_date ? 'Deposited on ' . $deposited_date : 'Undeposited' , 'parent' => $chargetype->id])
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
                                        @can('is-super')
                                            <th>Deposited Today?</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($charges->sortBy('timestamp') as $charge)
                                        <tr>
                                            <td>{{ $charge->camper->firstname }} {{ $charge->camper->lastname }}</td>
                                            <td>{{ number_format(abs($charge->amount), 2) }}</td>
                                            <td>{{ number_format(abs($charge->amount + $charge->children->sum('amount')), 2) }}</td>
                                            <td>{{ number_format(abs($charge->children->where('chargetype_id', \App\Enums\Chargetypename::Donation)->sum('amount')), 2) }}</td>
                                            <td>{{ number_format(abs($charge->children->where('chargetype_id', \App\Enums\Chargetypename::PayPalServiceCharge)->sum('amount')), 2) }}</td>
                                            <td>{{ $charge->timestamp }}</td>
                                            <td>{{ $charge->memo }}</td>
                                            <td>
                                                @include('includes.admin.controls', ['id' => $charge->camper->id])
                                            </td>
                                            @can('is-super')
                                                <td>
                                                    @include('includes.admin.delete', ['id' => $charge->id])
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="9" class="text-md-right">
                                            Total Deposit: ${{ number_format(abs($charges->sum('amount')), 2) }}
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            @endcomponent
                        @empty
                            <h3 class="ml-5">No charges found for this chargetype</h3>
                        @endforelse
                    </div>
                    @can('is-super')
                        <div class="mt-2">
                            @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Mark as Deposited']])
                        </div>
                    @endif
                </form>
            </div>
        @endforeach
    @endcomponent
@endsection

@section('script')
    <script type="text/javascript">
        $('form.deposits').on('submit', function (e) {
            if ($('input[type="checkbox"]:checked').length === 0 && !confirm("You are about to mark all charges of this chargetype as deposited today. Is this correct?")) {
                return false;
            }
            return true;
        });
    </script>
@endsection

