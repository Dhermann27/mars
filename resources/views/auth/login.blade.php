@extends('layouts.app')

@section('title')
    Login
@endsection

@section('heading')
    Returning camper? Login here using your email and password.
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif


                            <x-form-group name="email" label="Email"/>

                            <x-form-group name="password" type="password" :label="__('Password')"/>

                            <x-form-group name="remember" type="checkbox" :label="__('Remember Me') "/>


                            <x-form-group type="submit" :label="__('Login')"/>


                            @if (Route::has('password.request'))
                                <a class="btn btn-lg btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                            @if (Route::has('register'))
                                <a class="btn btn-lg btn-link float-md-end" href="{{ route('register') }}"
                                   data-mdb-toggle="tooltip"
                                   data-mdb-placement="top"
                                   title="New camper? Create a new account to get started!">
                                    New Account?
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
