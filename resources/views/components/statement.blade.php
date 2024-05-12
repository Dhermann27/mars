<table class="table">
    <thead>
    <tr>
        <th scope="col" class="text-md-center">Date</th>
        <th scope="col">Charge Type</th>
        <th scope="col">Debit</th>
        <th scope="col">Credit</th>
        <th scope="col">Memo</th>
        @if(request()->route()->hasParameter('id') && Gate::allows('is-super'))
            <th scope="col">Delete?</th>
        @endif
    </tr>
    </thead>
    <tbody class="table-striped">
    @foreach($charges as $charge)
        <tr>
            <th scope="row" class="text-md-center" nowrap="nowrap">{{ $charge->timestamp }}</th>
            <td>{{ $charge->chargetypename }}</td>
            @if($charge->amount >= 0)
                <td class="amount">{{ number_format($charge->amount, 2) }}</td>
                <td>&nbsp;</td>
            @else
                <td>&nbsp;</td>
                <td class="amount">{{ number_format(abs($charge->amount), 2) }}</td>
            @endif
            <td>{{ $charge->memo }}</td>
            @if(request()->route()->hasParameter('id') && Gate::allows('is-super'))
                <td>
                    @if($charge->id != 0)
                        @include('components.admin.delete', ['id' => $charge->id, 'dusk' => 'charge' . $charge->id])
                    @else
                        &nbsp;
                    @endif
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    @if(request()->route()->hasParameter('id'))
        <tr class="text-md-end {{ $charges->sum('amount') > 0 ? 'text-red' : 'text-green' }}">
            <td colspan="2">&nbsp;</td>
            <td class="amount">{{ number_format(abs($charges->sum('amount')), 2) }}</td>
            <td colspan="2"><strong>Amount {{ $charges->sum('amount') >= 0 ? 'Due' : 'Owed' }}</strong></td>
        </tr>
    @else
        @if(Gate::allows('accept-paypal', $year))
            <tr class="text-md-end">
                <td colspan="2">&nbsp;</td>
                <td class="amount">
                    <span id="amountNow">{{ number_format(max($deposit, 0), 2) }}</span>
                </td>
                <td colspan="2"><strong>Amount Due Now</strong></td>
            </tr>
        @endif
        @if($stepdata["isRoomsSelected"])
            <tr class="text-md-end">
                <td colspan="2">&nbsp;</td>
                <td class="amount">
                    <span id="amountArrival">{{ number_format(max(0, $charges->sum('amount')), 2) }}
                </td>
                <td colspan="2"><strong>Amount Due Upon Arrival</strong></td>
            </tr>
        @endif
    @endif
    </tfoot>
</table>
