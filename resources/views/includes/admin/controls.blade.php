<div class='dropdown @if($id) d-print-none @endif'>
    <button class='btn btn-secondary dropdown-toggle @if(!$id) disabled @else btn-sm @endif' type='button'
            data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Details
    </button>
    <div class='dropdown-menu'>
        <a class='dropdown-item' href='{{ route('household.index', ['id' => $id]) }}'><i class='fas fa-home'></i> Billing</a>
        <a class='dropdown-item' href='{{ route('camperinfo.index', ['id' => $id]) }}'><i class='fas fa-users'></i> Campers</a>
        <a class='dropdown-item' href='{{ route('payment.index', ['id' => $id]) }}'><i class='fas fa-usd-circle'></i> Statement</a>
        <a class='dropdown-item' href='{{ route('workshopchoice.index', ['id' => $id]) }}'><i class='fas fa-rocket'></i> Workshops</a>
        <a class='dropdown-item' href='{{ route('roomselection.index', ['id' => $id]) }}'><i class='fas fa-bed'></i> Room Selection
            (Map View)</a>
        <a class='dropdown-item' href='{{ route('roomselection.read', ['id' => $id]) }}'><i class='fas fa-bed'></i> Room Selection
            (Tool View)</a>
        {{--        <a class='dropdown-item' href='{{ route('/volunteer/' . $id) }}'><i class='fas fa-handshake'></i>--}}
        {{--            Volunteer</a>--}}
        {{--        <a class='dropdown-item' href='{{ route('confirm.index') }}'><i class='fas fa-envelope'></i>--}}
        {{--            Confirmation</a>--}}
        {{--        <a class='dropdown-item' href='{{ route('nametag.index') }}'><i class='fas fa-id-card'></i> Customize--}}
        {{--            Nametags</a>--}}
        {{--        <a class='dropdown-item' href='{{ route('/tools/nametags/' . $id) }}'><i class='fas fa-print'></i> Print--}}
        {{--            Nametags</a>--}}
        {{--        <a class='dropdown-item' href='{{ route('/calendar/' . $id) }}'><i class='fas fa-calendar'></i> Calendar</a>--}}
    </div>
</div>
