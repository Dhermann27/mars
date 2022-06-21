@extends('layouts.app')

@section('css')
    <style>
        div.tooltip-inner {
            max-width: 500px !important;
            text-align: left;
            font-size: 1.1em;
        }

        span.shop-note-full, span.shop-note-fill {
            float: right;
            padding: 6px 12px 6px 12px;
            margin: 0;
            border-radius: 3px;
            border-bottom-left-radius: 0px;
            border-bottom-right-radius: 0px;
        }

        span.shop-note-full {
            border: 2px solid darkred;
            background-color: darkred;
        }

        span.shop-note-fill {
            border: 2px solid darkkhaki;
            background-color: darkkhaki;
        }

        .list-group-item-action.active:hover, .list-group-item-action.active:focus {
            color: white;
        }
    </style>
@endsection

@section('title')
    Workshop Preferences
@endsection

@section('heading')
    Use this page to choose from the available workshops for the various timeslots of the day.
@endsection

@section('content')
    <x-layouts.register :stepdata="$stepdata" step="6" previous="roomselect" next="nametag">
        <div class="display-6 mt-3 border-bottom text-end">Choose workshops</div>
        <form id="workshops" class="form-horizontal" role="form" method="POST"
              action="{{ route('workshopchoice.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
            @include('includes.flash')

            <x-navtabs :tabs="$campers" option="firstname">
                @foreach($campers as $camper)
                    <div role="tabpanel" class="tab-pane fade {{ $loop->index == 0 ? 'active show' : ''}}"
                         aria-expanded="{{ $loop->index ? 'true' : 'false' }}" id="tab-{{ $camper->id }}">

                        <input type="hidden" id="workshops-{{ $camper->id }}"
                               name="workshops-{{ $camper->id }}" class="workshop-choices"/>
                        <h2 class="m-3">{{ $camper->firstname }} {{ $camper->lastname }}</h2>
                        <div class="container py-5 py-lg-6 bg-grey mb-5">
                            <div class="row">
                                @if(!$camper->program->is_minor)
                                    @foreach($timeslots as $timeslot)
                                        <div class="list-group col-md-6 col-sm-12 pb-5">
                                            <h5>{{ $timeslot->name }}
                                                @if($timeslot->id != 1005)
                                                    ({{ $timeslot->start_time->format('g:i A') }}
                                                    - {{ $timeslot->end_time->format('g:i A') }})
                                                @endif
                                            </h5>
                                            <h6 class="alert alert-danger mt-2 d-none">Your workshop selections are
                                                offered on conflicting days.</h6>
                                            @foreach($timeslot->workshops as $workshop)
                                                <x-workshop-button :id="$camper->id  . '-' . $workshop->id"
                                                                   :workshop="$workshop"/>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-md-8 col-sm-12 pt-5">
                                        Camper has been automatically enrolled in
                                        <strong>{{ $camper->programname }}</strong> programming.
                                    </div>
                                    @foreach($timeslots->where('id', '1005') as $timeslot)
                                        <div class="list-group col-md-4 col-sm-12">
                                            <h5>{{ $timeslot->name }}</h5>
                                            <h6 class="alert alert-danger mt-2 d-none">Your workshop selections are
                                                offered on conflicting days.</h6>
                                            @foreach($timeslot->workshops as $workshop)
                                                <x-workshop-button :id="$camper->id  . '-' . $workshop->id"
                                                                   :workshop="$workshop"/>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </x-navtabs>
            @if($stepdata["amountDueNow"] <= 0)
                @cannot('readonly')
                    <x-form-group type="submit" label="Save Changes"/>
                @endif
            @endif
        </form>
    </x-layouts.register>
@endsection

@section('script')
    <script type="text/javascript">
        function checkDays(item) {
            let days = parseInt(0, 2);
            const actives = item.querySelectorAll('button.active');
            for (j = 0; j < actives.length; j++) {
                var choice = parseInt(actives[j].getAttribute('data-bits'), 2);
                if (choice & days) {
                    for (k = 0; k < actives.length; k++) window.addClass(actives[k], 'list-group-item-danger');
                    window.removeClass(item.querySelector('.alert-danger'), 'd-none');
                } else {
                    days = choice | days;
                }
            }
        }

        @foreach($campers as $camper)
        @foreach($camper->yearattending->workshops as $choice)
        window.addClass(document.getElementById('workshop-{{ $camper->id }}-{{ $choice->id }}'), 'active');
        @endforeach
        @endforeach
        const lists = document.querySelectorAll('form#workshops div.tab-pane div.list-group');
        for (i = 0; i < lists.length; i++) {
            checkDays(lists[i]);
        }
        @cannot('readonly')
        const items = document.querySelectorAll('form#workshops button.list-group-item');
        for (i = 0; i < items.length; i++) {
            window.addEvent(items[i], 'click', function (e) {
                e.preventDefault();
                window.addClass(e.target.parentNode.querySelector('.alert-danger'), 'd-none');
                const dangers = e.target.parentNode.querySelectorAll('.list-group-item-danger');
                for (j = 0; j < dangers.length; j++) window.removeClass(dangers[j], 'list-group-item-danger');
                if (window.hasClass(e.target, 'active')) {
                    window.removeClass(e.target, 'active');
                } else {
                    window.addClass(e.target, 'active');
                }
                checkDays(e.target.parentNode);
            });
        }
        window.addEvent(document.getElementById('workshops'), 'submit', function () {
            const panes = document.querySelectorAll('div.tab-pane');
            for (i = 0; i < panes.length; i++) {
                let ids = [];
                const actives = panes[i].querySelectorAll('button.active');
                for (j = 0; j < actives.length; j++) {
                    ids.push(actives[j].id.split('-')[2]);
                }
                document.getElementById('workshops-' + panes[i].id.split('-')[1]).value = ids.join(',');
            }
            return true;
        });
        @endif
    </script>
@endsection
