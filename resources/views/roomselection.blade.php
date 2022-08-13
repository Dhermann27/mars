@extends('layouts.app')

@section('css')
    <style>
        form#roomselection svg {
            background-image: url('/images/rooms.png');
            overflow: visible;
        }

        form#roomselection .svgText {
            pointer-events: none;
        }

        form#roomselection rect.available {
            opacity: 1;
            fill: #fff;
            cursor: pointer;
        }

        form#roomselection rect.highlight {
            opacity: 1;
            fill: #67b021;
            cursor: pointer;
        }

        form#roomselection rect.unavailable {
            opacity: 1;
            fill: darkgray;
            cursor: not-allowed;
        }

        form#roomselection rect.active {
            opacity: 1;
            fill: #daa520;
        }

        div.tooltip-inner {
            max-width: 20em;
            font-size: 1.1em;
            pointer-events: none; /*let mouse events pass through*/
            text-align: left;
        }
    </style>
@endsection

@section('title')
    Room Selection Tool
@endsection

@section('heading')
    This easy-to-use tool will let you choose from the remaining available rooms this year, and see who your neighbors might be!
@endsection

@section('content')
    <div class="d-flex bg-light mb-3">
        <div class="container p-3">
            <form id="roomselection" method="POST"
                  action="{{ route('roomselection.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
                @include('includes.flash')
                <svg id="rooms" height="731" width="1152" class="d-none d-xl-block">
                    <text x="30" y="40" font-family="Antic Slab" font-size="36px" fill="white">Trout Lodge</text>
                    <text x="15" y="80" font-family="Antic Slab" font-size="36px" fill="white">Guest Rooms</text>
                    <text x="320" y="-215" transform="rotate(90)" font-family="Antic Slab" font-size="36px"
                          fill="white">Loft
                        Suites
                    </text>
                    <text x="402" y="450" font-family="Antic Slab" font-size="36px" fill="white">Lakeview Cabins</text>
                    <text x="255" y="200" font-family="Antic Slab" font-size="36px" fill="white">Forestview Cabins
                    </text>
                    <text x="540" y="267" font-family="Antic Slab" font-size="36px" fill="white">Tent Camping</text>
                    <text x="740" y="85" font-family="Antic Slab" font-size="36px" fill="white">Camp Lakewood Cabins
                    </text>
                    <text x="910" y="460" font-family="Antic Slab" font-size="36px" fill="white">Commuter</text>
                    @foreach($rooms as $room)
                        <g>
                            <rect id="room-{{ $room->room_id }}"
                                  class="{{ $currentRoom == $room->room_id ? 'active' : '' }}
                              {{ (isset($room->names) && $room->room->capacity < 10 && $currentRoom != $room->room_id) || $locked ? 'unavailable' : 'available' }}"
                                  width="{{ $room->room->pixelsize }}" height="{{ $room->room->pixelsize }}"
                                  x="{{ $room->room->xcoord }}" y="{{ $room->room->ycoord }}"
                                  data-mdb-toggle="tooltip" data-mdb-html="true"
                                  title="{{ $room->room->building->name }}
                        @if($room->room->pixelsize < 50)
                            {{ $room->room->room_number }}
                        @endif
                        @if(isset($room->room->connected_with))
                            @if($room->room->building_id == 1000)
                            <br /><i>Double Privacy Door with Room {{ $room->room->connected_with }}</i>
                            @else
                            <br /><i>Shares common area with Room {{ $room->room->connected_with }}</i>
                            @endif
                        @endif
                        @if(isset($room->names))
                            <hr />
                            @if($room->room->capacity < 10) Locked by:<br />@endif
                            {{ $room->names }}
                            @if($currentRoom == $room->room_id)
                            <br /><strong>Current selection</strong>
                            <br />Please note that changing from this room will make it available to other campers. <i>This cannot be undone.</i>
                            @endif
                        @endif"></rect>

                            <text class="svgText" x="{{ $room->room->xcoord+3 }}"
                                  y="{{ $room->room->ycoord+$room->room->pixelsize/1.62 }}" font-size="12px">
                                {{ $room->room->pixelsize < 50 ? $room->room->room_number : ''}}
                            </text>
                        </g>
                    @endforeach
                </svg>

                <div class="d-lg-block d-xl-none">
                    <div class="note note-info text-black mb-3">
                        <strong>Campers to Move:</strong><br/>
                        @foreach($campers as $camper)
                            {{ $camper->firstname }} {{ $camper->lastname }}<br/>
                        @endforeach
                    </div>
                    <x-form-group label="Room" name="roomselect_id" type="select"
                                  data-mdb-clear-button="true" data-mdb-filter="true">
                        <option value="0">No room selected</option>
                        @foreach($rooms->groupBy('room.building_id') as $buildingrooms)
                            {{--                            @if(count($buildingrooms) > 1)--}}
                            <optgroup label="{{ $buildingrooms->first()->room->building->name }}">
                                @foreach($buildingrooms as $buildingroom)
                                    <option value="room-{{ $buildingroom->room_id }}"
                                            @selected(old('roomselect_id', $currentRoom) == $buildingroom->room_id)
                                            @if(isset($buildingroom->names))
                                                @if($buildingroom->room->capacity<=10 && $buildingroom->room_id != $currentRoom) disabled
                                            @endif
                                            data-mdb-secondary-text="@if($buildingroom->room->capacity <= 10)Locked by:
                                            @endif
                                            {{ preg_replace('/<br \/>/', ', ', $buildingroom->names)}}"
                                        @endif>
                                        @if($buildingroom->room->pixelsize < 50)
                                            {{ $buildingroom->room->room_number }}
                                        @else
                                            Click here to choose this housing
                                        @endif
                                    </option>
                                @endforeach
                            </optgroup>
                            {{--                            @else--}}
                            {{--                                <option value="{{$buildingrooms->first()->room_id}}" class="text-white">--}}
                            {{--                                    {{ $buildingrooms->first()->room->building->name  }}--}}
                            {{--                                </option>--}}
                            {{--                            @endif--}}
                        @endforeach
                    </x-form-group>
                </div>
                @cannot('readonly')
                    @if($locked)
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-lg btn-primary py-3 px-4 disabled">No changes allowed</button>
                        </div>
                    @elseif(Gate::allows('select-room', $year))
                        <input type="hidden" id="room_id" name="room_id"/>
                        <x-form-group type="submit" label="Lock Room"/>
                    @endif
                @endcannot
            </form>

            <x-steps.progress :width="5/8" previous="payment" next="workshopchoice"/>

        </div>
    </div>
@endsection

@section('script')
    @if(Gate::allows('select-room', $year) && !$locked)
        <script>
            const rects = document.querySelectorAll('rect.available');
            for (i = 0; i < rects.length; i++) {
                window.addEvent(rects[i], 'click', function (e) {
                    const active = document.querySelector('rect.active');
                    if (active) {
                        window.removeClass(active, 'active');
                        window.removeClass(active, 'unavailable')
                        window.addClass(active, 'available');
                    }
                    window.addClass(e.target, 'active');
                    document.getElementById('room_id').value = e.target.id.substring(5);
                    window.setSelect('#roomselect_id', e.target.id);
                });
            }

            const roomselect = document.getElementById('roomselect_id');
            window.addEvent(roomselect, 'change', function (e) {
                const active = document.querySelector('rect.active');
                if (active) {
                    window.removeClass(active, 'active');
                    window.removeClass(active, 'unavailable')
                    window.addClass(active, 'available');
                }
                window.addClass(document.getElementById(e.target.value), 'active');
                document.getElementById('room_id').value = e.target.value.substring(5);
            });

            window.addEvent(document.getElementById('roomselection'), 'submit', function () {
                if (!confirm("You are moving {{ count($campers) }} campers to a new room. This cannot be undone. Is this correct?")) {
                    return false;
                }
                return true;
            });
        </script>
    @endif
@endsection
