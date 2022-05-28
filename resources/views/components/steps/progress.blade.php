<div class="stepper-mobile-footer bg-light">
    <div class="stepper-back-btn">
        <a href="{{ $previous }}" {{ $attributes->class(['btn', 'btn-link', 'disabled' => $previous == '#']) }}>
            <i class="fas fa-chevron-left"></i>
            Back
        </a>
    </div>

    <div class="stepper-mobile-progress gray-500">
        <div class="stepper-mobile-progress-bar bg-primary" style="width: {{ $width }}%;"></div>
    </div>

    <div class="stepper-next-btn">
        <a href="{{ $next }}" {{ $attributes->class(['btn', 'btn-link', 'disabled' => $next == '#']) }}>
            Next
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</div>
