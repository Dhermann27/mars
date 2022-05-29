@extends('layouts.app')

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
    <form id="contactus" class="form-horizontal m-5" role="form" method="POST" action="{{ route('contact.index') }}">
        @include('includes.flash')

        @if(Auth::check() && !empty(Auth::user()->camper))
            <x-form-group name="yourname" label="Your Name"
                          readonly="{{ Auth::user()->camper->firstname . ' ' . Auth::user()->camper->lastname }}"/>
        @else
            <x-form-group name="yourname" label="Your Name"/>
        @endif

        @auth
            <x-form-group name="email" label="Your Email" readonly="{{ Auth::user()->email }}"/>
        @else
            <x-form-group name="email" label="Your Email"/>
        @endauth

        <x-form-group type="select" name="mailbox" label="Recipient Mailbox">
            <option value="0">Choose a recipient mailbox</option>
            @foreach($mailboxes as $mailbox)
                <option value="{{ $mailbox->id }}" @selected(old('mailbox'))>{{ $mailbox->name }}</option>
            @endforeach
        </x-form-group>

        <x-form-group type="textarea" name="message" label="Message">{{ old('message') }}</x-form-group>

        <div class="col-md-6 offset-md-3 mb-1">
            <span id="captchaimg">{!! captcha_img() !!}</span>
            <button type="button" id="refreshcaptcha" class="btn btn-primary" onclick="safeRefreshPage();"><i
                    class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div class="mb-3 pb-1">
            <div class="form-outline col-md-6 offset-md-3">
                <input id="captcha" name="captcha" type="text"
                       class="form-control @error('captcha') is-invalid @enderror"
                placeholder="Type the code you see in the box above to verify that you are a human."/>
                <label for="captcha" class="form-label">CAPTCHA Test</label>
                @error('captcha')
                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                @enderror
            </div>
        </div>

        <x-form-group type="submit" label="Send Message"/>
    </form>
@endsection

@section('script')
    <script type="text/javascript">
        function safeRefreshPage() {
            var form = document.getElementById('contactus');
            form.action = '{{ route('contact.contact-refresh-page') }}';
            form.submit();
        }
        // var refreshCap = function () {
        //     getAjax('/refreshcaptcha', function (data) {
        //         document.getElementById('captchaimg').innerHTML = data.captcha;
        //     })
        // }
        //
        // addEvent(document.getElementById('refreshcaptcha'), 'click', refreshCap);
    </script>
@endsection
