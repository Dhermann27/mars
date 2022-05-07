@extends('layouts.app')

@section('title')
    Outstanding Balances
@endsection

@section('content')
    @include('includes.flash')

    <table class="table table-striped">
        <thead>
        <tr>
            <td colspan="@can('is-super') 8 @else 3 @endif" class="text-end">
                Total Outstanding This Year: ${{ number_format($charges->sum('thisamount'), 2) }}
            </td>
        </tr>
        <tr>
            <th>Family Name</th>
            <th>Last Year Balance</th>
            <th>This Year Balance</th>
            @can('is-super')
                <th>Year</th>
                <th>Chargetype</th>
                <th>Payment Amount</th>
                <th>Memo</th>
                <th>&nbsp;</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($charges as $charge)
            <form id="outstanding" class="form-horizontal" role="form" method="POST"
                  action="{{ url('/reports/outstanding/' . $charge->camper_id) }}">
                {{ csrf_field() }}
                <tr>
                    <td>{{ $charge->familyname }}</td>
                    <td class="text-end">{{ number_format($charge->lastamount, 2) }}</td>
                    <td class="text-end">{{ number_format($charge->thisamount, 2) }}</td>
                    @can('is-super')
                        <td>
                            <label for="year_id-{{ $charge->camper_id }}" class="sr-only">Year</label>
                            <select class="form-control" id="year_id-{{ $charge->camper_id }}"
                                    name="year_id" style="width: 75px;">
                                <option value="{{ $year->id }}" selected>{{ $year->year }}</option>
                                <option value="{{ $lastyear->id }}">{{ $lastyear->year }}</option>
                            </select>
                        </td>
                        <td>
                            <label for="chargetype-{{ $charge->camper_id }}" class="sr-only">Chargetype</label>
                            <select class="form-control" id="chargetype-{{ $charge->camper_id }}"
                                    name="chargetype_id">
                                @foreach($chargetypes as $chargetype)
                                    <option value="{{ $chargetype->id }}"
                                        {{ $chargetype->id == old('chargetype_id') ? ' selected' : '' }}>
                                        {{ $chargetype->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="form-group{{ $errors->has('amount-' . $charge->camper_id) ? ' has-error' : '' }}">
                            <div class="input-group">
                                <label for="amount-{{ $charge->camper_id }}" class="sr-only">Amount</label>
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" id="amount-{{ $charge->camper_id }}"
                                       class="form-control" step="any" name="amount"
                                       data-number-to-fixed="2" value="{{ old('amount') }}"/>
                            </div>
                        </td>
                        <td class="form-group">
                            <label for="memo-{{ $charge->camper_id }}" class="sr-only">Memo</label>
                            <input id="memo-{{ $charge->camper_id }}" class="form-control" name="memo"
                                   value="{{ old('memo') }}">
                        </td>
                        <td class="form-group">
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </td>
                    @endif
                </tr>
            </form>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="text-end">
            <td>Total Outstanding</td>
            <td>${{ number_format($charges->sum('lastamount'), 2) }}</td>
            <td>${{ number_format($charges->sum('thisamount'), 2) }}</td>
            @can('is-super')
                <td colspan="5">&nbsp;</td>
        @endif
        </tfoot>
    </table>
@endsection
