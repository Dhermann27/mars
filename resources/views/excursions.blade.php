@extends('layouts.app')

@section('title')
    Excursions
@endsection

@section('heading')
    These single-day trips off the YMCA of the Ozarks campus are a blast every year!
@endsection

@section('image')
    url('/images/workshops.jpg')
@endsection

@section('content')
    <div class="container px-3 py-5 px-lg-4 py-lg-6 bg-grey mb-5">
        @foreach($timeslot->workshops->where('year_id', $year->id) as $workshop)
            @component('components.layouts.blog', ['title' => $workshop->name])
                @include('includes.filling', ['workshop' => $workshop])
                <h5>Led by {{ $workshop->led_by }}</h5>
                <p>{{ $workshop->blurb }} <i>Days: {{ $workshop->displayDays }}</i></p>
            @endcomponent
        @endforeach
    </div>
@endsection
