<a @if($isLinkActive())
       href="{{ route($url . '.index', ['id' => request()->route('id')]) }}"
   @else
       data-mdb-toggle="tooltip" data-mdb-placement="right" title="{{ $tooltip }}"
   @endif>
    <i {{ $attributes->class(['fas', 'stepper-state-icon', 'fa-' . ($isLarge ? '5x' : '2x'), $getIconState()]) }}
       dusk="step-{{ $url }}"></i>
    <div {{ $attributes->class(['stepper-head', $getDataState()]) }}>
        <span
            class="stepper-head-icon"><i {{ $attributes->class(['far', 'fa-' . $icon, 'fa-2x' => $isLarge]) }}></i></span>
        <span class="stepper-head-text">
            {{ $slot }}
        </span>
    </div>
</a>
