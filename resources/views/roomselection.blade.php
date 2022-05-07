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

        div#mytooltip {
            background: sandybrown;
            border: solid gray;
            position: absolute;
            max-width: 20em;
            font-size: 1.1em;
            pointer-events: none; /*let mouse events pass through*/
            opacity: 0;
            transition: opacity 0.3s;
            box-shadow: #0f0f0f;
            padding: 3px;
            z-index: 10;
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
    @include('includes.steps')
    <div class="container">
        <form id="roomselection" method="POST" action="{{ route('roomselection.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
            @include('includes.flash')

            <svg id="rooms" height="731" width="1152">
                <text x="30" y="40" font-family="Lato" font-size="36px" fill="white">Trout Lodge</text>
                <text x="15" y="80" font-family="Lato" font-size="36px" fill="white">Guest Rooms</text>
                <text x="320" y="-215" transform="rotate(90)" font-family="Lato" font-size="36px" fill="white">Loft
                    Suites
                </text>
                <text x="402" y="450" font-family="Lato" font-size="36px" fill="white">Lakeview Cabins</text>
                <text x="255" y="200" font-family="Lato" font-size="36px" fill="white">Forestview Cabins</text>
                <text x="540" y="267" font-family="Lato" font-size="36px" fill="white">Tent Camping</text>
                <text x="740" y="85" font-family="Lato" font-size="36px" fill="white">Camp Lakewood Cabins</text>
                <text x="910" y="460" font-family="Lato" font-size="36px" fill="white">Commuter</text>
                @foreach($rooms as $room)
                    <g>
                        <rect id="room-{{ $room->id }}"
                              class="{{ $ya->room_id == $room->id ? 'active' : '' }}
                              {{ ($room->available == '0' && $room->capacity < 10 && $ya->room_id != $room->id) || $locked ? 'unavailable' : 'available' }}"
                              width="{{ $room->pixelsize }}" height="{{ $room->pixelsize }}" x="{{ $room->xcoord }}"
                              y="{{ $room->ycoord }}" data-content="{{ $room->buildingname }}
                        @if($room->pixelsize < 50)
                        {{ $room->room_number }}
                        @endif
                        @if (isset($room->connected_with))
                        @if($room->buildingid == 1000)
                            <br /><i>Double Privacy Door with Room {{ $room->connected_with }}</i>
                            @else
                            <br /><i>Shares common area with Room {{ $room->connected_with }}</i>
                            @endif
                        @endif
                        @if(isset($room->names))
                            <hr />
                            @if($room->capacity < 10)
                            Locked by:<br />
                            @endif
                        {{ $room->names }}
                            @if($ya->room_id == $room->id)
                            <br /><strong>Current selection</strong>
                            <br />Please note that changing from this room will make it available to other campers. <i>This cannot be undone.</i>
                            @endif
                        @endif
                            "></rect>

                        <text class="svgText" x="{{ $room->xcoord+3 }}" y="{{ $room->ycoord+$room->pixelsize/1.62 }}"
                              font-size="12px">{{ $room->pixelsize < 50 ? $room->room_number : ''}}</text>
                    </g>
                @endforeach
            </svg>
            @cannot('readonly')
                @if($locked)
                    <div class="text-lg-right mt-2">
                        <button type="button" class="btn btn-primary disabled py-3 px-4">
                            Room Locked By Registrar
                        </button>
                    </div>
                @elseif($year->is_room_select)
                    <input type="hidden" id="room_id" name="room_id"/><p>&nbsp;</p>
                    @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Lock Room']])
                @endif
            @endif
        </form>
    </div>
    <p>&nbsp;</p>
    <div id="mytooltip"></div>
@endsection

@section('script')
    <script>
        $('rect').on('mouseover', function (event) {
            $(this).addClass('highlight');
            $('div#mytooltip').html($(this).attr('data-content')).css({
                'opacity': '1',
                'top': (window.pageYOffset + event.clientY + 30) + 'px',
                'left': (window.pageXOffset + event.clientX) + 'px'
            });
        }).on('mouseout', function () {
            $(this).removeClass('highlight');
            return $('div#mytooltip').css('opacity', '0');
        });
        @if(!$locked)
        $('rect.available').on('click', function () {
            $('rect.active').removeClass('active').removeClass('unavailable').addClass('available');
            $(this).addClass('active');
        });
        $('#roomselection').on('submit', function () {
            active = $("rect.active");
            if(active.length !== 1) {
                alert("Please select an available room by clicking on one of the white squares.");
                return false;
            }
            if (!confirm("You are moving {{ $count }} campers to a new room. This cannot be undone. Is this correct?")) {
                return false;
            }
            $("#room_id").val(active.first().attr("id").split("-")[1]);
            return true;
        });
        @endif
    </script>
@endsection
