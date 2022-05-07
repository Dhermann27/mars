@extends('layouts.app')

@section('title')
    Workshop Preferences
@endsection

@section('heading')
    Use this page to choose from the available workshops for the various timeslots of the day.
@endsection

@section('content')
    @include('includes.steps')
    <div class="container">
        <form id="workshops" class="form-horizontal" role="form" method="POST"
              action="{{ route('workshopchoice.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
            @include('includes.flash')

            @component('components.navtabs', ['tabs' => $campers, 'id'=> 'id', 'option' => 'firstname'])
                @foreach($campers as $camper)
                    <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $camper->id }}"
                         role="tabpanel">
                        <input type="hidden" id="workshops-{{ $camper->id }}"
                               name="workshops-{{ $camper->id }}" class="workshop-choices"/>
                        <h2 class="m-3">{{ $camper->firstname }} {{ $camper->lastname }}</h2>
                        <div class="container px-3 py-5 px-lg-4 py-lg-6 bg-grey mb-5">
                            <div class="row">
                                @if(!$camper->program->is_minor)
                                    @foreach($timeslots as $timeslot)
                                        <div class="list-group col-md-4 col-sm-6 pb-5">
                                            <h5>{{ $timeslot->name }}
                                                @if($timeslot->id != 1005)
                                                    ({{ $timeslot->start_time->format('g:i A') }}
                                                    - {{ $timeslot->end_time->format('g:i A') }})
                                                @endif
                                            </h5>
                                            @include('includes.workshops')
                                            <h6 class="alert alert-danger mt-2 d-none">Your workshop selections are
                                                offered on conflicting days.</h6>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-md-8 col-sm-6">
                                        <p>&nbsp;</p>
                                        Camper has been automatically enrolled in
                                        <strong>{{ $camper->programname }}</strong> programming.
                                    </div>
                                    @foreach($timeslots->where('id', '1005') as $timeslot)
                                        <div class="list-group col-md-4 col-sm-6">
                                            <h5>{{ $timeslot->name }}</h5>
                                            @include('includes.workshops')
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endcomponent
            @cannot('readonly')
                @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Preferences']])
            @endif
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(function () {
            @foreach($campers as $camper)
            @foreach($camper->yearattending->workshops as $choice)
            $("#workshop-{{ $camper->id }}-{{ $choice->id }}").addClass("active");
            @endforeach
            @endforeach
            $("form#workshops div.tab-pane div.list-group").each(function () {
                var days = parseInt(0, 2);
                var actives = $(this).find("button.active");
                actives.each(function () {
                    var choice = parseInt($(this).attr("data-bits"), 2);
                    if (choice & days) {
                        actives.addClass("btn-danger");
                        $(this).parent().find(".alert-danger").removeClass("d-none");
                    } else {
                        days = choice | days;
                    }
                });
            });
            @cannot('readonly')
            $("form#workshops button.list-group-item").on("click", function (e) {
                e.preventDefault();
                $(this).parent().find(".alert-danger").addClass("d-none");
                $(this).toggleClass("active").removeClass("btn-danger");
                var days = parseInt(0, 2);
                var actives = $(this).parent().find("button.active");
                actives.removeClass("btn-danger");
                actives.each(function () {
                    var choice = parseInt($(this).attr("data-bits"), 2);
                    if (choice & days) {
                        actives.addClass("btn-danger");
                        $(this).parent().find(".alert-danger").removeClass("d-none");
                        return true;
                    } else {
                        days = choice | days;
                    }
                });
            });
            $("form#workshops").on("submit", function (e) {
                $(this).find("div.tab-pane").each(function () {
                    var ids = new Array();
                    $(this).find("button.active").each(function () {
                        ids.push($(this).attr("id").split('-')[2]);
                    });
                    $("#workshops-" + $(this).attr("id").split('-')[1]).val(ids.join(","));
                });
                return true;
            });
            @endif
            $('[data-toggle="popover"]').popover({
                trigger: 'hover'
            });
        });
    </script>
@endsection
