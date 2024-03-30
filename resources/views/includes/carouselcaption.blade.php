{{--<div class="carousel-caption d-none d-md-block">--}}
    <h1 class="font-weight-bold text-letter-spacing-xs">
        Midwest Unitarian Universalist Summer Assembly
    </h1>
    <h6>
        An annual intergenerational Unitarian Universalist retreat for fun, fellowship, and personal growth
    </h6>
    <h4 class="my-2">
{{--        {{ $year->first_day }} through {{ $year->last_day }} {{ $year->year }}--}}
        In the interim, Wednesday, July 3rd through Sunday, July 7th 2024<br /><br />
        Meeting this year in Bloomington, Indiana
    </h4>
    @can('register', $year)
        <p>
            <a href="{{ route('dashboard') }}" class="btn btn-lg btn-primary">Your Registration</a>
        </p>
    @endif
{{--</div>--}}
