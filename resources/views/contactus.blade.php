@extends('layouts.app')

@section('css')
    <style>
        .grecaptcha-badge {
            visibility: visible !important;
        }
    </style>
@endsection

@section('title')
    Contact Us
@endsection

@section('heading')
    Send an email to the right person to answer your questions or concerns.
@endsection

@section('image')
    url('/images/biographies.jpg')
@endsection

@section('content')
    <form id="contactus" class="form-horizontal m-5" role="form" method="POST"
          action="{{ route('contact.index') }}">
        @include('includes.flash')

        @if(isset(Auth::user()->camper))
            <x-form-group name="yourname" label="Your Name" is-readonly="true"
                          value="{{ Auth::user()->camper->firstname . ' ' . Auth::user()->camper->lastname }}"/>
        @else
            <x-form-group name="yourname" label="Your Name"/>
        @endif

        @auth
            <x-form-group name="email" label="Your Email" is-readonly="true " value="{{ Auth::user()->email }}"/>
        @else
            <x-form-group name="email" label="Your Email"/>
        @endauth

        <x-form-group type="select" name="mailbox" label="Recipient Mailbox">
            <option value="0">Choose a recipient mailbox</option>
            @foreach($mailboxes as $mailbox)
                <option value="{{ $mailbox->id }}" @selected(old('mailbox'))>{{ $mailbox->name }}</option>
            @endforeach
        </x-form-group>

        <x-form-group type="textarea" name="message" label="Message"/>

        {!! RecaptchaV3::field('contact') !!}

        <x-form-group type="submit" label="Send Message"/>
    </form>
@endsection
