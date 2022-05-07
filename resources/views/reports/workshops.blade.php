@extends('layouts.app')

@section('title')
    Workshop Attendees
@endsection

@section('content')
    <button class="btn btn-primary float-end printme" data-toggle="tooltip" title="Print Signup Sheets">
        <i class="far fa-print fa-2x"></i>
    </button>
    @component('components.navtabs', ['tabs' => $timeslots, 'id'=> 'id', 'option' => 'name'])
        @foreach($timeslots as $timeslot)
            <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $timeslot->id }}"
                 role="tabpanel">
                @if($timeslot->id != \App\Enums\Timeslotname::Excursions)
                    <h5>{{ $timeslot->start_time->format('g:i A') }} - {{ $timeslot->end_time->format('g:i A') }}</h5>
                @endif
                @foreach($timeslot->workshops->where('year_id', $year->id) as $workshop)
                    <h4>{{ $workshop->name }} ({{ count($workshop->choices) }} / {{ $workshop->capacity }})</h4>
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
                                        {{ $choice->created_at }}
                                    @endif
                                </td>
                                <td>
                                    @include('includes.admin.controls', ['id' => $choice->yearattending->camper->id])
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="d-print-none">
                            <td colspan="3">Distribution list: {{ $workshop->emails }}
                            </td>
                        </tr>
                        @for($i=count($workshop->choices); $i<min($workshop->capacity, count($workshop->choices)+5); $i++)
                            <tr class="d-none d-print-block">
                                <td colspan="2" style="border-bottom: 1px solid black;">&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                        </tfoot>
                    </table>
                @endforeach
            </div>
        @endforeach
    @endcomponent
@endsection

@section('script')
    <script>
        $("button.printme").on('click', function () {
            window.print();
        });
    </script>
@endsection
