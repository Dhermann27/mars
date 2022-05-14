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

                            @include('includes.formgroup', ['label' => 'Email', 'attribs' => ['name' => 'email']])

                            <div class="form-group row col-md-6 offset-md-3">
                                <div class="form-outline">
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" name="password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                </div>
                            </div>

                            <div class="form-group row col-md-6 offset-md-3">
                                <div class="form-outline">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember"
                                               id="remember" @checked(old('remember'))>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                    @if (Route::has('register'))
                                        <a class="btn btn-link" href="{{ route('register') }}" data-toggle="tooltip"
                                           data-placement="top"
                                           title="New camper? Just click the 'Register Now' button to get started!">
                                            New Account?
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
