@extends('layouts.app')

@section('title')
    Registration
@endsection

@section('heading')
    Learn about the registration process and where you are in it.
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <x-steps :stepdata="$stepdata" />
            </div>
        </div>
    </div>
@endsection
