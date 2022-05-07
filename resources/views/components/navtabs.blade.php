{{--@component('components.navtabs', ['tabs' => $timeslots, 'id'=> 'id', 'option' => 'name'])--}}
{{--@foreach($timeslots as $timeslot)--}}
{{--<div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $timeslot->id }}" role="tabpanel">--}}
<ul id="nav-tab" class="nav nav-tabs pt-lg-3" role="tablist">
    @foreach($tabs as $tab)
        <li class="nav-item{{ $loop->first ? ' pl-5' : '  ml-2' }}">
            <button class="nav-link{{ $loop->first ? ' active' : '' }}" data-mdb-toggle="tab"
                    data-mdb-target="#tab-{{ $tab->id }}" type="button" role="tab"
                    aria-controls="{{ $option == 'fullname' ? $tab->firstname . $tab->lastname : $tab->$option }}"
                    aria-selected="{{ $loop->first ? ' true' : 'false' }}">
                {{ $option == 'fullname' ? $tab->firstname . ' ' . $tab->lastname : $tab->$option }}
            </button>
        </li>
    @endforeach
</ul>
<div id="nav-tab{{ $id }}Content" class="tab-content p-3">
    {{ $slot }}
</div>
