<div class="btn-group" role="group" aria-label="Admin Controls">
    <a class="btn btn-secondary px-2 {{ $id == 0 ? 'disabled' : '' }}"
       href="{{ route('camperselect.index', ['id' => $id]) }}" data-mdb-toggle="tooltip" title="Camper Selection">
        <i class="fas fa-users fa-fw"></i>
    </a>
    <a class="btn btn-secondary px-2 {{ $id == 0 ? 'disabled' : '' }}"
       href="{{ route('household.index', ['id' => $id]) }}" data-mdb-toggle="tooltip" title="Billing Address">
        <i class="fas fa-home fa-fw"></i>
    </a>
    <a class="btn btn-secondary px-2 {{ $id == 0 ? 'disabled' : '' }}"
       href="{{ route('camperinfo.index', ['id' => $id]) }}" data-mdb-toggle="tooltip" title="Camper Information">
        <i class="fas fa-user-gear fa-fw"></i>
    </a>
    <a class="btn btn-secondary px-2 {{ $id == 0 ? 'disabled' : '' }}"
       href="{{ route('payment.index', ['id' => $id]) }}" data-mdb-toggle="tooltip" title="Account Statement">
        <i class="fas fa-usd-square fa-fw"></i>
    </a>
{{--    <a class="btn btn-secondary px-2 {{ $id == 0 ? 'disabled' : '' }}"--}}
{{--       href="{{ route('roomselection.index', ['id' => $id]) }}" data-mdb-toggle="tooltip" title="Room Selection">--}}
{{--        <i class="fas fa-bed fa-fw"></i>--}}
{{--    </a>--}}
    <a class="btn btn-secondary px-2 {{ $id == 0 ? 'disabled' : '' }}"
       href="{{ route('workshopchoice.index', ['id' => $id]) }}" data-mdb-toggle="tooltip" title="Workshops">
        <i class="fas fa-rocket fa-fw"></i>
    </a>
</div>
