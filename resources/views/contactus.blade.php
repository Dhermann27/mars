@extends('layouts.app')

@section('title')
    Contact Us
@endsection

@section('heading')
    Send an email to the right person to answer your questions or concerns.
@endsection

@section('content')
    <div class="container">
        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact.index') }}">
            @include('includes.flash')

            @if(Auth::check() && !empty(Auth::user()->camper))
                @include('includes.formgroup', ['type' => 'info', 'label' => 'Your Name', 'attribs' => ['name' => 'name'],
                    'default' => Auth::user()->camper->firstname . ' ' . Auth::user()->camper->lastname])

                @include('includes.formgroup', ['type' => 'info', 'label' => 'Email Address', 'attribs' => ['name' => 'email'],
                    'default' => Auth::user()->email])
            @else
                @include('includes.formgroup', ['label' => 'Your Name', 'attribs' => ['name' => 'name']])

                @include('includes.formgroup', ['label' => 'Email Address', 'attribs' => ['name' => 'email']])
            @endif

            @include('includes.formgroup', ['type' => 'select', 'label' => 'Recipient Mailbox',
                'attribs' => ['name' => 'mailbox'], 'default' => 'Choose a recipient mailbox', 'list' => $mailboxes,
                'option' => 'name'])

            @include('includes.formgroup', ['type' => 'text', 'label' => 'Message', 'attribs' => ['name' => 'message']])

            @include('includes.formgroup', ['type' => 'captcha', 'label' => 'CAPTCHA Test',
                'attribs' => ['name' => 'captcha']])

            @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Send Message']])
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('button#refreshcaptcha').click(function () {
            $.ajax({
                type: 'GET',
                url: '/refreshcaptcha',
                success: function (data) {
                    $("span#captchaimg").html(data.captcha);
                }
            });
        }).click();
    </script>
@endsection
