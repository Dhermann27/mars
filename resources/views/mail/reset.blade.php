@extends('layouts.email')

@section('title')
    Password Reset
@endsection

@section('content')
    <p>Hello!</p>

    <p>You are receiving this email because we received a password reset request for your account.</p>

    <div align="center"><a target="_blank" rel="noopener noreferrer" href="{{ url('password/reset') }}/{{ $token }}?email={{ $email }}" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; box-sizing: border-box; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); color: #fff; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; background-color: #3490dc; border-top: 10px solid #3490dc; border-right: 18px solid #3490dc; border-bottom: 10px solid #3490dc; border-left: 18px solid #3490dc;">Reset Password</a></div>

    <p>This password reset link will expire in 60 minutes.</p>

    <p>If you did not request a password reset, no further action is required.</p>

    <hr />

    <p style="font-size: 10px;">If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br />
        {{ url('password/reset') }}/{{ $token }}?email={{ $email }}</p>
@endsection
