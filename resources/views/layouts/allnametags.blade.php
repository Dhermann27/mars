<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link
        href="https://fonts.googleapis.com/css?family=Jost|Bangers|Fredericka+the+Great|Great+Vibes|Indie+Flower|Mystery+Quest"
        rel="stylesheet">
    <title>All Nametags</title>
    <link rel="stylesheet" href="/css/print.css">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>
@php
    $backs = array();
@endphp
@foreach($campers as $camper)
    @php
        switch ($loop->index % 6) {
            case 0:
                $pointer = 1;
                break;
            case 1:
                $pointer = 0;
                break;
            case 2:
                $pointer = 3;
                break;
            case 3:
                $pointer = 2;
                break;
            case 4:
                $pointer = 5;
                break;
            case 5:
                $pointer = 4;
                break;
        }
        array_splice($backs, $pointer, 0, $camper->nametag_back);
    @endphp
    <x-nametag :camper="$camper" :index="$loop->index"/>
    @if((!$loop->first && ($loop->index+1) % 6 == 0) || $loop->last)
        <div class="page-break"></div>
        @foreach($backs as $back)
            <div class="label py-3" dusk="back-{{ $loop->index }}">
                <div class="container">
                    {!! $back !!}
                </div>
            </div>
        @endforeach
        @php
            $backs = array();
        @endphp
        <div class="page-break"></div>
    @endif
@endforeach
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
