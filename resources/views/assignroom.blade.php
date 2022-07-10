@extends('layouts.app')

@section('title')
    Assign Room
@endsection

@section('content')
    <div class="container">
        <form class="form-horizontal" role="form" method="POST"
              action="{{ route('roomselection.write', ['id' => $campers->first()->family_id]) }}">
            @include('includes.flash')

            @foreach($campers as $camper)
                <div class="form-group row @error('roomid-' . $camper->id) has-danger @enderror">
                    <label for="roomid-{{ $camper->id }}" class="col-md-4 control-label">
                        <button type="button" id="quickcopy" href="#" class="p-2 float-end" data-toggle="tooltip"
                                title="Assign entire family to this room"><i class="fas fa-copy"></i></button>
                        {{ $camper->firstname }} {{ $camper->lastname }}
                    </label>

                    @cannot('$readonly')
                        <div class="col-md-4">
                            <select id="roomid-{{ $camper->id }}"
                                    class="form-control roomlist @error('roomid-' . $camper->id) is-invalid @enderror"
                                    name="roomid-{{ $camper->id }}">
                                <option value="0">No Room Selected</option>
                                @foreach($buildings as $building)
                                    <optgroup label="{{ $building->name }}">
                                        @foreach($building->rooms as $room)
                                            <option
                                                value="{{ $room->id }}" @selected($camper->room_id == $room->id)>
                                                {{ $room->room_number }} {{ $room->is_handicap == '1' ? ' - HC' : '' }}
                                                ({{ count($room->occupants) }}/{{ $room->capacity }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('roomid-' . $camper->id)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @endif
                        </div>
                    @endif

                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Year</th>
                            <th>Building</th>
                            <th>Room Number</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($camper->yearsattending as $ya)
                            @if(isset($ya->room_id))
                                <tr>
                                    <td>{{ $ya->year->year}}</td>
                                    <td>{{ $ya->room->building->name}}</td>
                                    <td>{{ $ya->room->room_number }}</td>

                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="3">No Data</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
            @cannot('readonly')
                @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes']])
            @endif
        </form>
    </div>
@endsection

@section('script')
    <script>
        $('button#quickcopy').on('click', function (e) {
            e.preventDefault();
            var val = $(this).parent().next().find("select.roomlist").val();
            $("select.roomlist").val(val);
        })
    </script>
@endsection
