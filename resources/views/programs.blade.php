@extends('layouts.app')

@section('title')
    Programs
@endsection

@section('heading')
    Learn more about the age-specific groups into which we divide our campers, and what to expect for people of all ages.
@endsection

@section('image')
    url('/images/programs.jpg')
@endsection

@section('content')
    <div class="container px-3 py-5 px-lg-4 py-lg-6 bg-grey mb-5">
        @foreach($programs as $program)
            @component('components.blog', ['title' => $program->name])
                <div class="p-3">
                    {!! $program->blurb !!}
                </div>
            @endcomponent

        @endforeach
    </div>
@endsection
