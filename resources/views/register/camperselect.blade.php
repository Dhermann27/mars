@extends('layouts.app')

@section('title')
    Camper Selection
@endsection

@section('heading')
    Enter a list of campers in your family, then choose which ones are coming in {{ $year->year }}.
@endsection

@section('content')
    <x-layouts.register :stepdata="$stepdata" step="1" previous="#" next="household">
        <div class="display-6 mt-3 border-bottom text-end">Who is attending in {{ $year->year }}?</div>
        <form id="camperselect" class="form-horizontal" role="form" method="POST"
              action="{{ route('camperselect.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
            @include('includes.flash')

            <ul class="list-group list-group-light container-md col-lg-6">
                @if(count($campers) != 1 || $campers[0]->firstname != 'New Camper')
                    @foreach($campers as $camper)
                        <li class="list-group-item ps-3">
                            <x-form-group type="checkbox" name="camper-{{ $camper->id }}"
                                          :label="$camper->firstname . ' ' . $camper->lastname"
                                          :value="count($camper->yearsattending) == 1"/>
                        </li>
                    @endforeach
                @else
                    <li class="list-group-item ps-3">
                        <div class="input-group mb-3 pe-3">
                            <div class="input-group-text border-0 ps-0">
                                <input type="hidden" value="0" name="newcheck-{{ $campers[0]->id }}">
                                <input class="form-check-input me-1" type="checkbox"
                                       name="newcheck-{{ $campers[0]->id }}" value="1"
                                       aria-label="Check this box if attending in {{ $year->year }}"
                                    @checked(count($campers[0]->yearsattending) == 1) />
                            </div>
                            <input name="newname-{{ $campers[0]->id }}" type="text" class="form-control"
                                   style="width: 50%;" value="New Camper" placeholder="Enter Camper Name"/>
                            <label for="newname-{{ $campers[0]->id }}" class="visually-hidden">New Camper</label>
                        </div>
                    </li>
                @endif
                <li id="additem" class="list-group-item px-2">
                    <button class="btn btn-info p-1 float-end" data-mdb-toggle="tooltip" data-mdb-html="true"
                            title="Need to remove an existing camper from your family? Please use the Contact Us form to reach the Registrar.">
                        <i class="fas fa-circle-question fa-xl"></i>
                    </button>
                    <button id="addcamper" class="btn btn-lg btn-secondary" onclick="addCamper();" type="button">
                        <i class="fas fa-user-plus"></i> Add New Camper
                    </button>
                </li>
            </ul>
            <x-form-group type="submit" label="Save Changes"/>
        </form>
    </x-layouts.register>
@endsection

@section('script')
    <script>
        let i = 0;

        function addCamper() {
            var el = document.createElement('li');
            el.classList.add('list-group-item');
            el.classList.add('ps-3');
            el.innerHTML = `<div class="input-group mb-3 pe-3">
                <div class="input-group-text border-0 ps-0">
                    <input type="hidden" value="0" name="newcheck-` + i + `">
                    <input class="form-check-input me-1" type="checkbox" name="newcheck-` + i + `" value="1"
                        aria-label="Check this box if attending in {{ $year->year }}" checked />
                </div>
                <input name="newname-` + i + `" type="text" class="form-control" style="width: 50%;" placeholder="Enter Camper Name" />
                <label for="newname-` + i + `" class="visually-hidden">New Camper</label>
                <button id="delete-` + i++ + `" class="btn btn-outline-secondary" type="button" onclick="removeCamper(event);">
                    <i class="fas fa-user-xmark"></i> Remove Camper
                </button>
            </div>`;
            var additem = document.getElementById('additem');
            additem.parentNode.insertBefore(el, additem);
        }

        function removeCamper(e) {
            var el = e.target.parentNode.parentNode;
            el.parentNode.removeChild(el);
        }
    </script>
@endsection
