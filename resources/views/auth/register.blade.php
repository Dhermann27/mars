@extends('layouts.app')

@section('title')
    Create Account
@endsection

@section('heading')
    New to the site? Create an account to get things started.
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <input type="hidden" name="name" value="none" />
                            <x-form-group name="email" label="Your Email"/>

                            <x-form-group name="password" type="password" :label="__('Password')"/>

                            <x-form-group name="password_confirmation" type="password" :label="__('Confirm Password')"/>

                            <x-form-group type="submit" :label="__('Create Account')"/>

                            @if (Route::has('password.request'))
                                <a class="btn btn-lg btn-link" href="{{ route('password.request') }}">
                                    Forgot Password?
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
