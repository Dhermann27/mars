@extends('layouts.app')

@section('title')
    Staff Positions
@endsection

@section('content')
    <div class="container">
        <form id="positions" class="form-horizontal" role="form" method="POST"
              action="{{ route('admin.positions.store') }}">
            @include('includes.flash')
            <x-navtabs :tabs="$programs" option="name">
                @foreach($programs as $program)
                    <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $program->id }}"
                         role="tabpanel">
                        <p>&nbsp;</p>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Program</th>
                                <th>Name</th>
                                <th>Compensation Level</th>
                                <th>Position Type</th>
                                <th>Maximum Compensation</th>
                                <th>
                                    End Staff Position?
                                    <button class="btn btn-info ms-3 p-1" data-mdb-toggle="tooltip"
                                            title="Although you will no longer be able to assign users to this position, previous years' assignments and their applied compensation will not be affected.">
                                        <i class="fas fa-circle-question fa-xl"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="editable">
                            @forelse($program->staffpositions()->where('start_year', '<=', $year->year)->where('end_year', '>', $year->year)->orderBy('name')->get() as $position)
                                <tr id="{{ $position->id }}">
                                    <td>{{ $program->name }}</td>
                                    <td>{!! $position->name !!}</td>
                                    <td>{{ $position->compensationlevel->name }}</td>
                                    <td>
                                        @if($position->pctype == 1)
                                            APC
                                        @elseif($position->pctype == 2)
                                            XC
                                        @elseif($position->pctype == 3)
                                            Programs
                                        @elseif($position->pctype == 4)
                                            Consultants
                                        @endif
                                    </td>
                                    <td class="amount">
                                        {{ number_format($position->compensationlevel->max_compensation, 2) }}
                                    </td>
                                    <td>
                                        <x-admin.delete :id="$position->id" :dusk="'delete' . $position->name"/>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"><h5>No positions found</h5></td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </x-navtabs>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Add New Position</div>

                        <div class="card-body">
                            <x-form-group label="Associated Program" name="program_id" type="select">
                                <option value="0">Choose an program</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                                @endforeach
                            </x-form-group>

                            <x-form-group label="Position Name" name="name"/>

                            <x-form-group label="Compensation Level" name="compensationlevel_id" type="select">
                                <option value="0">Choose a compensation level</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </x-form-group>

                            <x-form-group label="PC Type" name="pctype" type="select">
                                <option value="0">Standard Member</option>
                                <option value="1">Adult Programming Committee</option>
                                <option value="2">Executive Council</option>
                                <option value="3">Program Staff</option>
                                <option value="4">Consultants</option>
                            </x-form-group>

                            <x-form-group type="submit" label="Save Changes"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

