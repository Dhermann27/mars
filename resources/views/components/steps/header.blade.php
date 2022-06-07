<a href="{{ $isLinkActive() ? route($url . '.index', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) : '#' }}"
   @if(!$isLinkActive()) data-mdb-toggle="tooltip" data-mdb-placement="right"
   title="This link is not available until the marked step has been completed." @endif>
{{--    @if($isRequired === true)--}}
        <i {{ $attributes->class(['fas', 'stepper-state-icon', 'fa-' . ($isLarge ? '5x' : '2x'), $getIconState()]) }}
           dusk="step-{{ $url }}"></i>
{{--    @endif--}}
    <div {{ $attributes->class(['stepper-head', $getDataState()]) }}>
        <span
            class="stepper-head-icon"><i {{ $attributes->class(['far', 'fa-' . $icon, 'fa-2x' => $isLarge]) }}></i></span>
        <span class="stepper-head-text">
            {{ $slot }}
        </span>
    </div>
</a>
