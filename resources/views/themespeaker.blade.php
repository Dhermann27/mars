@extends('layouts.app')

@section('title')
    Guest Speaker: Rev. Misha Sanders
@endsection

@section('heading')
    Learn more about the {{ $year->year }} guest speaker.
@endsection

@section('image')
    url('/images/biographies.jpg')
@endsection

@section('content')
    <div class="container px-3 py-5 px-lg-10 py-lg-6 bg-grey mb-5">
        @component('components.blog', ['title' => 'Theme Speaker'])
            <p>
                <img class="float-right p-3" src="/images/mishasm.png"
                     alt="Rev. Misha Sanders" data-no-retina/>
                <strong>Rev. Misha Sanders</strong> (she/her/hers) is feeling very blessed to be the Senior Minister at
                Northwest Unitarian Universalist Congregation in beautiful Sandy Springs, Georgia. She is a fiery
                preacher of the good news of Unitarian Universalism, and believes that the whole world is built and
                rebuilt by the stories we tell ourselves and each other.
            </p>
        @endcomponent
    </div>
@endsection
