@extends('layouts.email')

@section('subject')
    MUUSA {{ $year->year }} Registration Confirmation
@endsection

@section('content')
    <p>Hello MUUSA Friends,</p>

    <p>Thank you for submitting your registration! We are looking forward to seeing you "next week".</p>

    <p>You have successfully registered the following campers for MUUSA {{ $year->year }}:</p>
    <ul>
        @foreach($campers as $camper)
            <li>{{ $camper->firstname }} {{ $camper->lastname }}</li>
        @endforeach
    </ul>

    <p>Your balance is due on the first day of camp, {{ $year->first_day }}.
        @if($year->is_brochure)
            Workshop preferences are available at <a href="https://muusa.org">muusa.org</a>.
        @else
            Room selection, workshop preferences, nametag customization, and confirmation letters will become
            available on {{ $year->brochure_date }} at <a href="https://muusa.org">muusa.org</a>.
        @endif
    </p>

    <p>Cheryl Heinz<br/>MUUSA Registar</p>
@endsection
