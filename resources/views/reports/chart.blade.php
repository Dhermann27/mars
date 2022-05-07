@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <style>
        div.tooltip-inner {
            max-width: 500px !important;
            text-align: left;
        }
    </style>
@endsection

@section('title')
    Registration Chart
@endsection

@section('heading')
    Updated at 1:00 AM and 1:00 PM
@endsection

@section('content')
    <div class="row-fluid">
        <div id="chart"></div>
    </div>
    <table class="table w-80 text-center">
        <tr>
            <th>Year</th>
            <th>Campers Lost</th>
            <th>New Campers</th>
            <th>Old Campers<br/>1 Year Missing
            </th>
            <th>Very Old Campers<br/>2 or More Years Missing
            </th>
            <th>Total</th>
        </tr>
        <tbody>
        @foreach ($years as $year)
            <tr>
                <td><b>{{ $year->year }}</b></td>
                <td>-
                    {{ count($year->chartdataLostcampers) }}
                    @if(count($year->chartdataLostcampers)>0)
                        <a href="#" class="p-2" data-toggle="tooltip" data-html="true" data-placement="right"
                           title="@foreach($year->chartdataLostcampers as $item)
                           {{ $item->camper->firstname }} {{ $item->camper->lastname}}@if(!$loop->last),@endif
                           @if(($loop->index+1) % 4 == 0) <br /> @endif
                           @endforeach
                               "><i class="far fa-info"></i></a>
                    @endif
                </td>
                <td>+
                    {{ count($year->chartdataNewcampers) }}
                    @if(count($year->chartdataNewcampers)>0)
                        <a href="#" class="p-2" data-toggle="tooltip" data-html="true" data-placement="right"
                           title="@foreach($year->chartdataNewcampers as $item)
                           {{ $item->yearattending->camper->firstname }}
                           {{ $item->yearattending->camper->lastname}}@if(!$loop->last),@endif
                           @if(($loop->index+1) % 4 == 0) <br /> @endif
                           @endforeach
                               "><i class="far fa-info"></i></a>
                    @endif
                </td>
                <td>+
                    {{ count($year->chartdataOldcampers) }}
                    @if(count($year->chartdataOldcampers)>0)
                        <a href="#" class="p-2" data-toggle="tooltip" data-html="true" data-placement="right"
                           title="@foreach($year->chartdataOldcampers as $item)
                           {{ $item->yearattending->camper->firstname }}
                           {{ $item->yearattending->camper->lastname}}@if(!$loop->last),@endif
                           @if(($loop->index+1) % 4 == 0) <br /> @endif
                           @endforeach
                               "><i class="far fa-info"></i></a>
                    @endif
                </td>
                <td>+
                    {{ count($year->chartdataVeryoldcampers) }}
                    @if(count($year->chartdataVeryoldcampers)>0)
                        <a href="#" class="p-2" data-toggle="tooltip" data-html="true" data-placement="right"
                           title="@foreach($year->chartdataVeryoldcampers as $item)
                           {{ $item->yearattending->camper->firstname }}
                           {{ $item->yearattending->camper->lastname}}@if(!$loop->last),@endif
                           @if(($loop->index+1) % 4 == 0) <br /> @endif
                           @endforeach
                               "><i class="far fa-info"></i></a>
                    @endif
                </td>
                <td>{{ count($year->yearsattending) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="6" class="text-center">
                <i>Previous Year's Total - Lost Campers + New Campers
                    + Old Campers + Very Old Campers = Total</i></td>
        </tr>
        </tfoot>
    </table>
@endsection

@section('script')
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script>
        var years = @json($years->pluck('year'));
        var mydata = [
                @foreach($chartdataDays as $day => $chartdays)
            {
                'y': '{{ $day }}',
                @foreach($chartdays as $chartday)
                '{{ $chartday->year }}':  {{ $chartday->count }},
                @endforeach
            },
            @endforeach
        ];
        new Morris.Line({
            element: 'chart',
            data: mydata,
            parseTime: false,
            xkey: 'y',
            ykeys: years,
            labels: years
        });
    </script>
@endsection
