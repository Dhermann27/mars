<ul id="nav-tab" class="nav nav-tabs pt-lg-3" role="tablist">
    @foreach($tabs as $tab)
        <li class="nav-item{{ $loop->first ? ' pl-5' : '  ml-2' }}">
            <button id="tablink-{{ $tab->$id }}"
                    {{ $attributes->class(['nav-link', 'active' => $activeTab == $loop->index]) }}
                    data-mdb-toggle="tab" data-mdb-target="#tab-{{ $tab->id }}" type="button" role="tab"
                    aria-controls="{{ $getValue($loop->index) }}" aria-selected="{{ $activeTab == $loop->index }}">
                {{ $getValue($loop->index) }}
            </button>
        </li>
    @endforeach
</ul>
<div id="nav-tab{{ $id }}Content" class="tab-content p-3">
    {{ $slot }}
</div>
