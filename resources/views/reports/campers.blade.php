@extends('layouts.app')

@section('css')
    <style>
        table.table-sm td:nth-child(1) {
            width: 8%;
        }

        table.table-sm td:nth-child(2) {
            width: 32%;
        }

        table.table-sm td:nth-child(3) {
            width: 8%;
        }

        table.table-sm td:nth-child(4) {
            width: 16%;
        }

        table.table-sm td:nth-child(5) {
            width: 16%;
        }

        table.table-sm td:nth-child(6) {
            text-align: right;
            width: 16%;
        }
    </style>
@endsection

@section('title')
    Registered Campers
@endsection


@section('heading')
    All campers that are registered for {{ $year->year }}
@endsection

@section('content')
    {{--    <x-navtabs :tabs="$years->keys()" option="year"--}}
    {{--               :active-tab="array_search($year->year, array_keys($years->toArray()))">--}}
    {{--        @foreach($years as $thisyear => $families)--}}
    {{--            <div role="tabpanel" id="tab-{{ $thisyear }}"--}}
    {{--                 class="tab-pane fade {{ $thisyear == $year->year ? 'active show' : '' }} ">--}}
    <table class="table">
        <thead>
        <tr class="text-md-end">
            <td colspan="4"><h4>Total Campers Attending: {{ $years->sum('count') }}</h4></td>
        </tr>
        <tr>
            <th>Family Name</th>
            <th>Location</th>
            <th>Registration Date</th>
        </tr>
        </thead>
        @foreach($years as $family)
            <tr>
                <td>{{ $family->familyname }}</td>
                <td>{{ $family->city }}, {{ $family->provincecode }}
                    @if($family->is_scholar == '1')
                        <i class="far fa-universal-access ps-2" data-mdb-toggle="tooltip"
                           title="This family has indicated that they are applying for a scholarship."></i>
                    @endif
                </td>
                <td>
                    {{ $family->created_at->toDateString() }}
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <table class="table table-sm">
                        @foreach($family->thisyearcampers as $camper)
                            <tr>
                                <td>{{ $camper->pronounname }}</td>
                                <td>{{ $camper->lastname }}, {{ $camper->firstname }}
                                    @if($camper->email)
                                        <a class="m-1 p-1" href="mailto:{{ $camper->email }}">
                                            <i class="far fa-envelope"></i>
                                        </a>
                                    @endif
                                    @if($camper->phonenbr)
                                        <a class="m-1 p-1" href="tel:{{ $camper->phonenbr }}">
                                            <i class="far fa-phone"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $camper->age }}</td>
                                <td>{{ $camper->programname ?? 'Unknown' }}</td>
                                <td>
                                    {{ $camper->room_id ? $camper->buildingname . ' ' . $camper->room_number : 'Unassigned' }}
                                </td>
                                <td>
                                    <x-admin.controls :id="$camper->id"/>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        @endforeach
        <tfoot>
        <tr>
            <td colspan="4" class="text-md-end"><h4>Total Campers Attending: {{ $years->sum('count') }}</h4></td>
        </tr>
        </tfoot>
    </table>
    {{--            </div>--}}
    {{--        @endforeach--}}
    {{--    </x-navtabs>--}}
@endsection
