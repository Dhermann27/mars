@extends('layouts.app')

@section('title')
    Positions
@endsection

@section('content')
    <div class="container">
        <form id="positions" class="form-horizontal" role="form" method="POST"
              action="{{ route('admin.positions.store') }}">
            @include('includes.flash')
            {{--<div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $timeslot->id }}" role="tabpanel">--}}
            @component('components.navtabs', ['tabs' => $programs, 'id'=> 'id', 'option' => 'name'])
                @foreach($programs as $program)
                    <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $program->id }}"
                         role="tabpanel">
                        <p>&nbsp;</p>
                        <table class="table">
                            <thead>
                            <tr>
                                <th id="program_id" class="select">Program</th>
                                <th id="name">Name</th>
                                <th id="compensationlevel_id" class="select">Compensation Level</th>
                                <th id="pctype" class="select">Position Type</th>
                                <th>Maximum Compensation</th>
                                <th><a href="#" class="float-end" data-toggle="tooltip" data-html="true"
                                       title="Although you will no longer be able to assign users to this position, previous years' assignments and their applied compensation will not be affected."><i
                                            class="far fa-info"></i></a> End Staff Position?
                                </th>
                            </tr>
                            </thead>
                            <tbody class="editable">
                            @forelse($program->staffpositions()->where('start_year', '<=', $year->year)->where('end_year', '>', $year->year)->orderBy('name')->get() as $position)
                                <tr id="{{ $position->id }}">
                                    <td class="teditable">{{ $program->name }}</td>
                                    <td class="teditable">{!! $position->name !!}</td>
                                    <td class="teditable">{{ $position->compensationlevel->name }}</td>
                                    <td class="teditable">
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
                                    <td>
                                        ${{ number_format($position->compensationlevel->max_compensation, 2) }}
                                    </td>
                                    <td>
                                        @include('includes.admin.delete', ['id' => $position->id])
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
            @endcomponent

            <div class="well">
                <h4>Add New Position</h4>
                @include('includes.formgroup', ['type' => 'select', 'class' => ' program_id',
                    'label' => 'Associated Program', 'attribs' => ['name' => 'program_id'],
                    'default' => 'Choose a program', 'list' => $programs, 'option' => 'name'])

                @include('includes.formgroup', ['label' => 'Position Name',
                    'attribs' => ['name' => 'name', 'placeholder' => 'Position Name']])

                @include('includes.formgroup', ['type' => 'select', 'class' => ' compensationlevel_id',
                    'label' => 'Compensation Level', 'attribs' => ['name' => 'compensationlevel_id'],
                    'default' => 'Choose a compensation level', 'list' => $levels, 'option' => 'name'])

                @include('includes.formgroup', ['type' => 'select', 'label' => 'PC Type', 'attribs' => ['name' => 'pctype'],
                    'list' => [['id' => '0', 'name' => 'None'], ['id' => '1', 'name' => 'APC'],
                    ['id' => '2', 'name' => 'XC'], ['id' => '3', 'name' => 'Program Staff'],
                    ['id' => '4', 'name' => 'Consultants']], 'option' => 'name'])

                @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes']])
            </div>
        </form>
    </div>
@endsection

