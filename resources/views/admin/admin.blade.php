@extends('layouts.app')

@section('title')
    Admin Dashboard
@endsection

@section('content')
    <div class="m-0 px-5">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Registered for {{ $year->year }}</h5>
                        <div
                            class="card-text display-4 text-{{ intdiv(count($campers), $average->average) >= .95 ? 'green' : 'red'}}">
                            {{ count($campers) }} / <span data-mdb-toggle="tooltip"
                                                          title="Average count on {{ $average->onlyday }}: {{ number_format($average->average, 0) }}">
                                {{ number_format(intdiv(count($campers), $average->average)*100, 0) }}%
                            </span>
                        </div>
                        <div class="card-footer">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                        id="last7Button" data-mdb-toggle="dropdown" aria-expanded="false">
                                    Last 7 days: {{ count($last7) }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="last7Button">
                                    @foreach($last7 as $ya)
                                        <li><a class="dropdown-item"
                                               href="{{ route('camperinfo.index', ['id' => $ya->camper_id]) }}">
                                                {{ $ya->camper->firstname }} {{ $ya->camper->lastname }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Actions Required</h5>
                        <div class="card-text">
                            <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                                <a type="button" href="{{ route('reports.deposits') }}"
                                   class="btn btn-outline-{{ $deposits > 0 ? 'warning' : 'success'}}">
                                    {{ $deposits }} Undeposited Payments <i class="far fa-chevron-right"></i>
                                </a>
                                <a type="button" href="#"
                                   class="btn btn-outline-{{ $homeless > 0 ? 'warning' : 'success'}}">
                                    {{ $homeless }} Unknown Addresses <i class="far fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Guarantee</h5>
                        <div class="card-text">
                            <canvas
                                data-mdb-chart="doughnut"
                                data-mdb-dataset-label="Amounts Charged"
                                data-mdb-labels="{{ $charges->pluck('name') }}"
                                data-mdb-dataset-data="{{ $charges->pluck('total') }}"
                                data-mdb-dataset-background-color="['rgba(239, 83, 80, 1)', 'rgba(63, 81, 181, 0.5)', 'rgba(77, 182, 172, 0.5)', 'rgba(66, 133, 244, 0.5)', 'rgba(156, 39, 176, 0.5)', 'rgba(233, 30, 99, 0.5)', 'rgba(66, 73, 244, 0.4)', 'rgba(66, 133, 244, 0.2)']"
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row my-5">
            <h3>All Camper Search</h3>
            <form id="campersearch">
                <div class="input-group adminControls">
                    <div class="form-outline autocomplete w-md-50 w-lg-75">
                        <input id="campersearch" name="campersearch" type="text"
                               class="form-control camper-search" autocomplete="off"
                               placeholder="Begin typing the camper name or email"/>
                        <label for="campersearch" class="form-label">Camper Name</label>
                        <input id="camper_id" name="camper_id" type="hidden"
                               class="autocomplete-custom-content"/>
                    </div>
                    <x-admin.controls id="0"/>
                </div>
            </form>
        </div>
        <div class="input-group float-end w-25">
            <input type="text" class="form-control" id="adminSearch" placeholder="Search Table" autocomplete="off"/>
            <button class="btn btn-primary" type="button">
                <i class="fa fa-search"></i>
            </button>
        </div>
        <h3 class="mt-3">Registered Campers for {{ $year->year }}</h3>
        <div id="datatable">
            <table>
                <thead>
                <tr>
                    <th class="th-sm">Family Name</th>
                    <th class="th-sm">City, State</th>
                    <th class="th-sm">First Name</th>
                    <th class="th-sm">Last Name</th>
                    <th class="th-sm">Age</th>
                    <th class="th-sm">Room</th>
                    <th class="th-sm">Details</th>
                </tr>
                </thead>
                <tbody>
                @foreach($campers as $camper)
                    <tr>
                        <td>{{ strlen($camper->familyname) > 15 ? substr($camper->familyname,0,10) . '&hellip;' : $camper->familyname }}</td>
                        <td>{{ $camper->city }}, {{ $camper->provincecode }}</td>
                        <td>{{ $camper->firstname }}</td>
                        <td>
                            {{ $camper->lastname }}
                            @if($camper->email)
                                <a class="m-1 p-1" href="mailto:{{ $camper->email }}"><i
                                        class="far fa-envelope"></i></a>
                            @endif
                            @if($camper->phonenbr)
                                <a class="m-1 p-1" href="tel:{{ $camper->phonenbr }}"><i class="far fa-phone"></i></a>
                            @endif
                            @if($camper->is_handicap)
                                <i class="m-1 p-1 far fa-universal-access"></i>
                            @endif
                        </td>
                        <td>{{ $camper->age }}</td>
                        <td>
                            @if($camper->room_id)
                                {{ $camper->room_number }}
                            @else
                                <i class="far fa-location-xmark"></i>
                            @endif
                        </td>
                        <td>
                            <x-admin.controls id="{{ $camper->id }}"/>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
