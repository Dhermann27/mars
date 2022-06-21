<button type="button" title="{{ $getBlurb() }}" data-mdb-toggle="tooltip"
        data-mdb-html="true" id="workshop-{{ $id }}"
        data-bits="{{ $workshop->bit_days }}"
    {{ $attributes->class(['list-group-item', 'list-group-item-action',
        'text-muted' => $workshop->enrolled >= $workshop->capacity]) }}>
    @if($workshop->enrolled >= $workshop->capacity)
        <i class="far fa-times fa-pull-right fa-fw mt-2"></i>
    @elseif($workshop->enrolled >= ($workshop->capacity * .75))
        <i class="far fa-exclamation-triangle fa-pull-right fa-fw mt-2"></i>
    @endif
    {!! $workshop->name !!}
    ({{ $workshop->display_days }})
</button>
