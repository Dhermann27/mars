@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="/css/print.css"/>
@endsection

@section('title')
    Workshop Attendees
@endsection

@section('content')
    <x-navtabs :tabs="$timeslots" option="name">
        @foreach($timeslots as $timeslot)
            <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $timeslot->id }}"
                 role="tabpanel">
                @if($timeslot->id != \App\Enums\Timeslotname::Excursions)
                    <div class="d-none d-print-block display-1">{{ $timeslot->name }}</div>
                    <div class="d-none d-print-block display-2">{{ $timeslot->start_time->format('g:i A') }}
                        - {{ $timeslot->end_time->format('g:i A') }}</div>
                    <h5 class="d-print-none">{{ $timeslot->start_time->format('g:i A') }}
                        - {{ $timeslot->end_time->format('g:i A') }}</h5>
                    <div class="page-break"></div>
                @endif
                @foreach($timeslot->thisyearWorkshops as $workshop)
                    <div style="page-break-inside: avoid">
                        <h4>{!! $workshop->name !!} ({{ count($workshop->choices) }} / {{ $workshop->capacity }})</h4>
                        <table class="table">
                            <thead>
                            <tr>
                                <th width="50%">Name</th>
                                <th width="25%">Sign Up Date</th>
                                <th width="25%">Controls</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($workshop->choices()->orderBy('is_leader', 'desc')->orderBy('created_at')->get() as $choice)
                                <tr @if($choice->is_enrolled == '0') class="table-danger"@endif>
                                    <td>{{ $choice->yearattending->camper->lastname }},
                                        {{ $choice->yearattending->camper->firstname }}</td>
                                    <td>
                                        @if($choice->is_leader == '1')
                                            <strong>Leader</strong>
                                        @else
                                            {{ $choice->is_enrolled == 1 ? $choice->created_at : 'Waitlist' }}
                                        @endif
                                    </td>
                                    <td>
                                        {{--                                        @include('includes.admin.controls', ['id' => $choice->yearattending->camper->id])--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            @if(count($workshop->choices) > 0)
                                <tr class="d-print-none">
                                    <td colspan="3">Distribution
                                        list: {{ $workshop->choices->yearattending->camper->pluck('email') }}
                                    </td>
                                </tr>
                            @endif
                            <tr class="d-none d-print-block">
                                <td colspan="3"><h4>Walk Ins:</h4></td>
                            </tr>
                            @for($i=count($workshop->choices); $i<min($workshop->capacity, count($workshop->choices)+5); $i++)
                                <tr class="d-none d-print-block" style="width:100%; border-bottom: 1px solid black;">
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                            @endfor
                            </tfoot>
                        </table>
                    </div>
                    <div class="page-break"></div>
                @endforeach
            </div>
        @endforeach
    </x-navtabs>
@endsection
