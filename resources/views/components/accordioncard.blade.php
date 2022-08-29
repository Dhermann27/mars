{{--<div class="accordion" id="accordionExample">--}}
{{--@foreach($chargestypes as $chargetype)--}}
{{--@component('component.accordioncard', ['id' => $chargetype->id, 'show' => true, 'heading' => $ddate, 'parent' => 'Example'])--}}
<div class="accordion-item">
    <h2 class="accordion-header" id="heading-{{ $id }}">
        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapse-{{ $id }}"
                aria-expanded="true" aria-controls="collapse-{{ $id }}">
            {{ $heading }}
        </button>
    </h2>

    <div id="collapse-{{ $id }}" class="accordion-collapse collapse @if($show) show @endif"
         aria-labelledby="heading-{{ $id }}" data-mdb-parent="#accordion-{{ $parent }}">
        <div class="accordion-body">
            {!! $slot !!}
        </div>
    </div>
</div>
