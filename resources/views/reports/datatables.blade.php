@extends('layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <ul id="nav-tab-years" class="nav nav-tabs" role="tablist">
        @foreach($tabs as $tab)
            <li class="nav-item{{ $loop->first ? ' pl-5' : '  ml-2' }}">
                <a class="nav-link" data-toggle="tab" href="#year-{{ $tab->id }}" role="tab">
                    {{ $tab->$tabfield }}
                </a>
            </li>
        @endforeach
    </ul>

    <div id="nav-tab-yearContent" class="tab-content p-3">
        @foreach($tabs as $tab)
            <div role="tabpanel" class="tab-pane fade" aria-expanded="false" id="year-{{ $tab->id }}">
                <table id="table-{{ $tab->id }}"
                       class="table table-striped table-bordered display responsive nowrap" style="width:100%">
                    <thead>
                    <tr>
                        @foreach($columns as $column => $display)
                            <th>{{ $display }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    {{--                <tr>--}}
                    {{--                    <td colspan="4">--}}
                    {{--                        <form id="roomchange-{{ $family->id }}" class="form-horizontal" role="form" method="POST"--}}
                    {{--                              action="{{ url('/admin/massassign') . '/f/' . $family->id}}">--}}
                    {{--                            {{ csrf_field() }}--}}
                    {{--                            <input type="hidden" name="familyid" value="{{ $family->id }}"/>--}}
                    {{--                            <table class="table table-sm">--}}
                    {{--                                @foreach($campers->get($family->id) as $camper)--}}
                    {{--                                    <tr>--}}
                    {{--                                        <td width="25%">{{ $camper->lastname }}, {{ $camper->firstname }}--}}
                    {{--                                            @if(isset($camper->email))--}}
                    {{--                                                <a href="mailto:{{ $camper->email }}" class="px-2 float-end"><i--}}
                    {{--                                                        class="far fa-envelope"></i></a>--}}
                    {{--                                            @endif--}}
                    {{--                                        </td>--}}
                    {{--                                        <td width="15%">{{ $camper->birthdate }}</td>--}}
                    {{--                                        <td width="20%">{{ $camper->programname }}</td>--}}
                    {{--                                        <td width="30%">--}}
                    {{--                                            @if(isset($buildings))--}}
                    {{--                                                <label for="{{ $camper->yearattendingid }}-roomid"--}}
                    {{--                                                       class="sr-only">{{ $camper->yearattendingid }}</label>--}}
                    {{--                                                <select id="{{ $camper->yearattendingid }}-roomid"--}}
                    {{--                                                        data-familyid="{{ $family->id }}" class="form-control roomlist"--}}
                    {{--                                                        name="{{ $camper->yearattendingid }}-roomid">--}}
                    {{--                                                    <option value="0">No Room Selected</option>--}}
                    {{--                                                    @foreach($buildings as $building)--}}
                    {{--                                                        <optgroup label="{{ $building->name }}">--}}
                    {{--                                                            @foreach($building->rooms as $room)--}}
                    {{--                                                                <option value="{{ $room->id }}"{{ $camper->roomid == $room->id ? ' selected' : '' }}>--}}
                    {{--                                                                    {{ $room->room_number }} {{ $room->is_handicap == '1' ? ' - HC' : '' }}--}}
                    {{--                                                                    {{'(' . count($room->occupants) . '/' . $room->capacity . ')'}}--}}
                    {{--                                                                </option>--}}
                    {{--                                                            @endforeach--}}
                    {{--                                                        </optgroup>--}}
                    {{--                                                    @endforeach--}}
                    {{--                                                </select>--}}
                    {{--                                            @else--}}
                    {{--                                                {{ empty($camper->room_number) ? 'Unassigned' : $camper->room_number }}--}}
                    {{--                                            @endif--}}
                    {{--                                        </td>--}}
                    {{--                                        <td width="10%">--}}
                    {{--                                            @include('admin.controls', ['id' => 'c/' . $camper->id])--}}
                    {{--                                        </td>--}}
                    {{--                                    </tr>--}}
                    {{--                                @endforeach--}}
                    {{--                            </table>--}}
                    {{--                        </form>--}}
                    {{--                    </td>--}}
                    {{--                </tr>--}}
                </table>
            </div>
        @endforeach
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('ul#nav-tab-years a:last').tab('show');
        @foreach($tabs as $tab)
        var table = $('table#table-{{ $tab->id }}').DataTable({
            pageLength: 50,
            buttons: [
                {
                    extend: 'columnVisibility',
                    text: 'Show all',
                    visibility: true
                },
                {
                    extend: 'colvis',
                    columnText: function (dt, idx, title) {
                        return (idx + 1) + ': ' + title;
                    }
                },
                {
                    extend: 'columnVisibility',
                    text: 'Hide all',
                    visibility: false
                },
                {
                    extend: 'copy',
                    text: '<u>C</u>opy <i class="fa fa-copy"></i>',
                    key: {
                        key: 'c',
                        altKey: true
                    }
                }, 'excel', 'pdf'
            ],
            columnDefs: [
                    @if(isset($visible))
                {
                    visible: false, targets: [{{ implode(',', $visible) }}]
                },
                    @endif
                    @if(isset($groupColumn))
                {
                    visible: false, targets: {{ $groupColumn }}
                },
                @endif
            ],

            @if(isset($groupColumn))
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({page: 'current'}).nodes();
                var last = null;

                api.column({{ $groupColumn }}, {page: 'current'}).data().each(function (group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            '<tr class="group">' +
                            '<td colspan="{{ count($columns)-1 }}">' + group + '</td>' +
                            '</tr>'
                        );

                        last = group;
                    }
                });
            },
            order: [[{{ $groupColumn }}, 'asc']],
            @endif
            data: [
                    @foreach($tab->$datafield as $data)
                [
                    @foreach($columns as $column => $display)
                    @if($column == "controls")
                    `@include('includes.admin.controls', ['id' => $data->id])`,
                    @else
                        "{{ $data->$column }}@if(isset($groupColumn) && $loop->index == $groupColumn)<span class='d-none'>{{ $data->family_id }}</span>@endif",
                    @endif
                    @endforeach
                ],
                @endforeach
            ]
        });
        table.buttons().container().insertBefore('table#table-{{ $tab->id }}');
        @endforeach

    </script>
@endsection

