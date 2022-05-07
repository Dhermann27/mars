{{--<div class="accordion" id="accordionExample">--}}
{{--@foreach($chargestypes as $chargetype)--}}
{{--@component('component.accordioncard', ['id' => $chargetype->id, 'show' => true, 'heading' => $ddate, 'parent' => 'Example'])--}}
<div class="card">
    <div class="card-header" id="heading-{{ $id }}">
        <h2 class="mb-0">
            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-{{ $id }}"
                    aria-expanded="true" aria-controls="collapse-{{ $id }}">
                {{ $heading }}
            </button>
        </h2>
    </div>

    <div id="collapse-{{ $id }}" class="collapse @if($show) show @endif" aria-labelledby="heading-{{ $id }}"
         data-parent="#accordion-{{ $parent }}">
        <div class="card-body">
            {!! $slot !!}
        </div>
    </div>
</div>
