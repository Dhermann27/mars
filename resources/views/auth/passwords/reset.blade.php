@extends('layouts.app')

@section('title')
    Update Password
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->token }}">


                            <x-form-group name="email" label="Your Email"/>

                            <x-form-group name="password" type="password" :label="__('Password')"/>

                            <x-form-group name="password_confirmation" type="password" :label="__('Confirm Password')"/>

                            <x-form-group type="submit" :label="__('Reset Password')"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
