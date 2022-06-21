@extends('layouts.app')

@section('title')
    Workshop List
@endsection

@section('heading')
    This page contains a list of the workshops
    @if($year->is_live)
        we have on offer in {{ $year->year }}, grouped by timeslot.
    @else
        we had on offer in {{ $year->year }}, as an example of what might be available.
    @endif
@endsection

@section('image')
    url('/images/workshops.jpg')
@endsection

@section('content')
    <x-navtabs :tabs="$timeslots" option="name">
        @foreach($timeslots as $timeslot)
            <div role="tabpanel" class="tab-pane fade {{ $loop->index == 0 ? 'active show' : ''}}"
                 aria-expanded="{{ $loop->index ? 'true' : 'false' }}" id="tab-{{ $timeslot->id }}">
                <div class="note note-info text-black m-3">Workshop Time: {{ $timeslot->start_time->format('g:i A') }}
                    - {{ $timeslot->end_time->format('g:i A') }}</div>

                <div class="container px-3 py-5 px-lg-4 py-lg-6 bg-grey mb-5">
                    @foreach($timeslot->workshops->where('year_id', $year->id) as $workshop)
                        <x-layouts.blog :title="$workshop->name">

                            @include('includes.filling', ['workshop' => $workshop])

                            <div class="lead d-block">Led by {{ $workshop->led_by }}
                                / Days: {{ $workshop->display_days }}
                                @if($workshop->fee > 0)
                                    / Fee: ${{ $workshop->fee }}
                                @endif
                            </div>

                            <p>{!! $workshop->blurb !!}</p>
                        </x-layouts.blog>
                    @endforeach
                </div>
            </div>
        @endforeach
    </x-navtabs>
@endsection
