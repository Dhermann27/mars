@foreach($timeslot->workshops->where('year_id', $year->id) as $workshop)
    <button type="button" data-content="{!! $workshop->blurb !!} Led by {{ $workshop->led_by }}." data-toggle="popover"
            data-trigger="hover" id="workshop-{{ $camper->id }}-{{ $workshop->id }}"
            data-bits="{{ $workshop->bit_days }}"
            @if($workshop->enrolled >= $workshop->capacity)
            class="list-group-item list-group-item-action text-muted" title="Workshop Full">
        <i class="far fa-times fa-pull-right"></i>
        @elseif($workshop->enrolled >= ($workshop->capacity * .75))
            class="list-group-item list-group-item-action" title="Filling Fast">
            <i class="far fa-exclamation-triangle fa-pull-right"></i>
        @else
            class="list-group-item list-group-item-action" title="Open for Enrollment">
        @endif
        {!! $workshop->name !!}
        ({{ $workshop->display_days }})
    </button>
@endforeach
