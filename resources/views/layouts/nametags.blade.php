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
    <style>
        body {
            width: 8.5in;
            margin: 0in .25in 0 .25in;
        }

        @page {
            margin: 1in 0px 0px 0px;
        }

        .label {
            float: left;
        }

        .page-break {
            clear: left;
            display: block;
            page-break-after: always;
        }
    </style>
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
    @include('includes.nametag', ['camper' => $camper])
    @if((!$loop->first && ($loop->index+1) % 6 == 0) || $loop->last)
        <div class="page-break"></div>
        @foreach($backs as $back)
            <div class="label pt-3" dusk="back-{{ $loop->index }}">
                <p>{!! $back !!}</p>
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
