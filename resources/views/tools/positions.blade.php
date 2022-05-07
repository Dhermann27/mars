@extends('layouts.app')

@section('title')
    Staff Assignments
@endsection

@section('content')
    <div class="container">
        <form id="positions" class="form-horizontal" role="form" method="POST"
              action="{{ route('tools.staff.store') }}">
            @include('includes.flash')

            @component('components.navtabs', ['tabs' => $programs, 'id'=> 'id', 'option' => 'name'])
                @foreach($programs as $program)
                    <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $program->id }}"
                         role="tabpanel">
                        <p>&nbsp;</p>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Position</th>
                                <th>Name</th>
                                <th>Maximum Compensation</th>
                                <th>Controls</th>
                                <th>Delete?</th>
                            </tr>
                            </thead
                            @if($staff->has($program->id))>
                            <tbody>
                            @foreach($staff[$program->id] as $assignment)
                                <tr>
                                    <td>{!! $assignment->staffpositionname !!}</td>
                                    <td>{{ $assignment->lastname }}, {{ $assignment->firstname }}
                                        @if($assignment->yearattending_id == 0)
                                            <a href="#" class="pl-2" data-toggle="tooltip" data-html="true"
                                               title="Not yet registered for {{ $year->year }}">
                                                <i class="far fa-thumbs-down"></i></a>
                                        @endif
                                    </td>
                                    <td>${{ number_format($assignment->max_compensation, 2) }}</td>
                                    <td>
                                        @include('includes.admin.controls', ['id' => $assignment->camper_id])
                                    </td>
                                    <td>
                                        @include('includes.admin.delete', ['id' => $assignment->camper_id . '-' . $assignment->staffposition_id])
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5" class="text-md-right"><strong>Maximum Compensation:</strong>
                                    ${{ number_format($staff[$program->id]->sum('max_compensation'), 2) }}</td>
                            </tr>
                            </tfoot>
                            @else

                                <tr>
                                    <td colspan="5"><h5>No staff assigned</h5></td>
                                </tr>
                            @endif
                        </table>
                    </div>
                @endforeach
            @endcomponent

            @include('includes.formgroup', ['type' => 'select', 'class' => ' camperlist',
            'label' => 'Camper', 'attribs' => ['name' => 'camper_id'], 'list' => []])

            @include('includes.formgroup', ['type' => 'select',
                'label' => 'Position', 'attribs' => ['name' => 'staffposition_id'],
                'default' => 'Choose a position', 'option' => 'name', 'list' => $positions])

            @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes']])
        </form>
    </div>
@endsection
