@extends('layouts.app')

@section('title')
    Rooms
@endsection

@section('content')
    @component('components.navtabs', ['tabs' => $years, 'id' => 'id', 'option' => 'year'])
        @foreach($years as $year)
            <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $year->id }}"
                 role="tabpanel">
                <div class="accordion" id="accordion-{{ $year->id }}">
                    @forelse($year->byyearcampers->whereNotNull('room_id')->sortBy('room_number')->groupBy('building_id')->transform(function($item, $k) {
    return $item->groupBy('room_id');
}) as $building_id => $rooms)
                        @component('components.accordioncard', ['id' => $building_id, 'show' => $loop->first, 'heading' => $rooms->first()->first()->buildingname, 'parent' => $year->id])
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Pronouns</th>
                                    <th>Camper Name</th>
                                    <th>Age</th>
                                    <th>Program</th>
                                    <th>Controls</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rooms as $room_id => $campers)
                                    <tr class="bg-info">
                                        <td colspan="5">{{ $campers->first()->room_number }}</td>
                                    </tr>
                                    @foreach($campers->sortBy('birthdate') as $camper)
                                        <tr>
                                            <td>{{ $camper->pronounname  }}</td>
                                            <td>{{ $camper->firstname }} {{ $camper->lastname }}</td>
                                            <td>{{ $camper->age }}</td>
                                            <td>{{ $camper->programname }}</td>
                                            <td>
                                                @include('includes.admin.controls', ['id' => $camper->id])
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        @endcomponent
                    @empty
                        <h3 class="ml-5">No rooms assigned in {{ $year->year }}</h3>
                    @endforelse
                </div>
            </div>
        @endforeach
    @endcomponent
@endsection

@section('script')
    <script>
        $('ul#nav-tab a:last').tab('show');
    </script>
@endsection
