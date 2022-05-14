@extends('layouts.app')

@section('title')
    Housing Options
@endsection

@section('heading')
    Check out all the available room types we have in the wonderful YMCA of the Ozarks facilities!
@endsection

@section('image')
    url('/images/housing.jpg')
@endsection

@section('content')
    <div class="container px-3 py-5 px-lg-4 py-lg-6 bg-grey mb-5">
        @foreach($buildings as $building)
            @component('components.blog', ['title' => $building->name])

                <div class="mt-2">{!! $building->blurb !!}</div>

                @if(isset($building->image))
                    <div id="carousel{{ $building->id }}" class="carousel slide building-carousel"
                         data-mdb-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach($building->image_array as $image)
                                <button type="button" data-mdb-target="#carousel{{ $building->id }}"
                                        data-mdb-slide-to="{{ $loop->index }}"
                                        @if($loop->first) class="active" @endif></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach($building->image_array as $image)
                                <div class="carousel-item @if($loop->first) active @endif">
                                    <img src="/images/buildings/{{ $image }}" alt="Image of {{ $building->name }} room"
                                         class="d-block w-100" data-no-retina/>
                                </div>
                            @endforeach
                        </div>

                        <!-- Controls -->
                        <button class="carousel-control-prev" type="button"
                                data-mdb-target="#carousel{{ $building->id }}" data-mdb-slide="prev">
                            <i class="fas fa-chevron-left fa-3x"></i> <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button"
                                data-mdb-target="#carousel{{ $building->id }}" data-mdb-slide="next">
                            <i class="fas fa-chevron-right fa-3x"></i> <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                @endif
            @endcomponent
        @endforeach
    </div>
@endsection
