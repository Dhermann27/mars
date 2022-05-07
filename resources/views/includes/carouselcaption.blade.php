<div class="carousel-caption d-none d-md-block">
    <h1 class="font-weight-bold text-letter-spacing-xs">
        Midwest Unitarian Universalist Summer Assembly
    </h1>
    <h6>
        An annual intergenerational Unitarian Universalist retreat for fun, fellowship, and personal growth
    </h6>
    <h4 class="my-2">
        {{ $year->first_day }} through {{ $year->last_day }} {{ $year->year }}
    </h4>
    <p>
        @can('has-paid')
            <a href="{{ route('campers.index') }}" class="btn btn-info btn-lg">
                See Your Information for {{ $year->year }} <i class="fas fa-sign-in"></i>
            </a>
        @else
            <button type="button" class="btn btn-info btn-lg" data-toggle="modal"
                    data-target="#modal-register">
                Register for {{ $year->year }} <i class="fas fa-sign-in"></i>
            </button>
        @endif
    </p>
</div>
