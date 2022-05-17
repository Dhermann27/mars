@extends('layouts.app')

@section('title')
    Password Reset
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <x-form-group name="email" label="Your Email"/>

                            <x-form-group type="submit" :label="__('Send Password Reset Link')"/>                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
