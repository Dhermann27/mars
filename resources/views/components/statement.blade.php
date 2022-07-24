<table class="table">
    <thead>
    <tr>
        <th scope="col" class="text-md-center">Date</th>
        <th scope="col">Charge Type</th>
        <th scope="col">Debit</th>
        <th scope="col">Credit</th>
        <th scope="col">Memo</th>
        @if(session()->has('camper') && Gate::allows('is-super'))
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
            @if(session()->has('camper') && Gate::allows('is-super'))
                <td>
                    @if($charge->id != 0)
                        @include('includes.admin.delete', ['id' => $charge->id])
                    @else
                        &nbsp;
                    @endif
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    @if($year->is_accept_paypal && !session()->has('camper'))
        <tr class="text-md-right">
            <td colspan="2">&nbsp;</td>
            <td class="amount">
                <span id="amountNow">{{ number_format(max($deposit, 0), 2) }}</span>
            </td>
            <td colspan="2"><strong>Amount Due Now</strong></td>
        </tr>
    @endif
    @if($stepdata["isRoomsSelected"] || session()->has('camper'))
        <tr class="text-md-right">
            <td colspan="2">&nbsp;</td>
            <td class="amount">
                    <span id="amountArrival">{{ number_format(max(0, $charges->sum('amount')), 2) }}
            </td>
            <td colspan="2"><strong>Amount Due Upon Arrival</strong></td>
        </tr>
    @endif
    </tfoot>
</table>
