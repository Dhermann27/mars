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

                            <x-form-group type="submit" :label="__('Send Password Reset Link')"/>
                        </form>
                        @if (Route::has('register'))
                            <a class="btn btn-lg btn-link float-md-end" href="{{ route('register') }}"
                               data-mdb-toggle="tooltip"
                               data-mdb-placement="top"
                               title="New camper? Create a new account to get started!">
                                New Camper?
                            </a>
                        @endif
                        @if (Route::has('login'))
                            <a class="btn btn-lg btn-link" href="{{ route('login') }}"
                               data-mdb-toggle="tooltip"
                               data-mdb-placement="top"
                               title="Returning camper? Login to your account to get started!">
                                Returning Camper?
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
