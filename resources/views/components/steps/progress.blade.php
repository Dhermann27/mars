<div class="stepper-mobile-footer bg-light">
    <div class="stepper-back-btn">
        <a @if($previous != '#') href="{{ route($previous . '.index', ['id' => request()->route('id')]) }}" @endif
            {{ $attributes->class(['btn', 'btn-link', 'disabled' => $previous == '#']) }} dusk="previous">
            <i class="fas fa-chevron-left"></i>
            Back
        </a>
    </div>

    <div class="stepper-mobile-progress gray-500">
        <div class="stepper-mobile-progress-bar bg-primary" style="width: {{ $width }}%;"></div>
    </div>

    <div class="stepper-next-btn">
        <a @if($next != '#') href="{{ route($next . '.index', ['id' => request()->route('id')]) }}" @endif
            {{ $attributes->class(['btn', 'btn-link', 'disabled' => $next == '#']) }} dusk="next">
            Next
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</div>
