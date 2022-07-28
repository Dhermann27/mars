@extends('layouts.app')

@section('title')
    Staff Assignments
@endsection

@section('content')
    <div class="container">
        <form id="positions" class="form-horizontal mt-3" role="form" method="POST"
              action="{{ route('tools.staff.store') }}">
            @include('includes.flash')

            <x-navtabs :tabs="$programs" option="name">
                @foreach($programs as $program)
                    <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $program->id }}"
                         role="tabpanel">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Position</th>
                                <th>Name</th>
                                <th>Maximum Compensation</th>
                                <th>Controls</th>
                                <th>Delete Assignment?</th>
                            </tr>
                            </thead
                            @if($staff->has($program->id))>
                            <tbody>
                            @foreach($staff[$program->id] as $assignment)
                                <tr>
                                    <td>{!! $assignment->staffpositionname !!}</td>
                                    <td>{{ $assignment->lastname }}, {{ $assignment->firstname }}
                                        @if($assignment->yearattending_id == 0)
                                            <button class="btn btn-warning ms-3 p-1" data-mdb-toggle="tooltip"
                                                    title="This camper has not yet registered.">
                                                <i class="fas fa-thumbs-down fa-xl"></i></button>
                                        @endif
                                    </td>
                                    <td class="amount">{{ number_format($assignment->max_compensation, 2) }}</td>
                                    <td>
                                        {{--                                        @include('includes.admin.controls', ['id' => $assignment->camper_id])--}}
                                    </td>
                                    <td>
                                        <x-admin.delete :id="$assignment->camper_id . '-' . $assignment->staffposition_id" />
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="2" class="text-md-end"><strong>Maximum Compensation:</strong>
                                <td colspan="3" class="amount">
                                    {{ number_format($staff[$program->id]->sum('max_compensation'), 2) }}
                                </td>
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
            </x-navtabs>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Assign New Position</div>

                        <div class="card-body">

                            <div class="row align-self-center mb-3">
                                <div class="container-md col-lg-6">
                                    <div class="form-outline autocomplete">
                                        <input id="campersearch" name="campersearch" type="text"
                                               class="form-control camper-search"
                                               placeholder="Begin typing the camper name or email"/>
                                        <label for="campersearch" class="form-label">Camper Name</label>
                                        <input id="camper_id" name="camper_id" type="hidden"
                                               class="autocomplete-custom-content"/>
                                    </div>
                                </div>
                            </div>

                            <x-form-group label="Position" name="staffposition_id" type="select" data-mdb-filter="true">
                                <option value="0">Choose a position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }}
                                @endforeach
                            </x-form-group>

                            <x-form-group type="submit" label="Save Changes"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
