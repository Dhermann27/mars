@if(session()->has('camper') && Gate::allows('is-council'))
    <h4 class="text-md-center">{{ session()->get('camper')->firstname }} {{ session()->get('camper')->lastname }}
        @if(isset(session()->get('camper')->email))
            &lt;{{ session()->get('camper')->email }}&gt;
            <a href="mailto:{{ session()->get('camper')->email }}"><i class="fa fa-envelope"></i></a>
        @endif
    </h4>
    <input type="hidden" id="camper-id" value="{{ session()->get('camper')->id }}"/>
@endif
@if(Gate::allows('has-paid') || (session()->has('camper') && Gate::allows('is-council')))
    <ul id="littlesteps" class="stepper">
        <li class="stepper-step">
            <a href="{{ route('household.index', ['id' => session()->has('camper') ? session()->get('camper')->id : null])}}">
                <span class="stepper-head-icon"><i class="fas fa-home"></i></span>
                Billing Information
            </a>
        </li>
        <li class="nav-item mx-5">
            <i id="camper-success" class="fas fa-check btn-success float-end d-none"></i>
            <a href="{{ route('campers.index', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}"
               class="nav-link @if(preg_match('/\/campers/', url()->current(), $matches)) active @endif"
               data-toggle="tooltip" data-placement="bottom" data-container="ul#littlesteps" title="Camper Listing">
                <i class="fas fa-users"></i>
            </a>
        </li>
        <li class="nav-item mx-5">
            <i id="payment-success" class="fas fa-check btn-success float-end d-none"></i>
            <a href="{{ route('payment.index', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}"
               class="nav-link @if(preg_match('/\/payment/', url()->current(), $matches)) active @endif"
               data-toggle="tooltip" data-placement="bottom" data-container="ul#littlesteps" title="Statement">
                <i class="fas fa-usd-square"></i>
            </a>
        </li>
        @if($year->is_brochure)
            <li class="nav-item mx-5">
                <i id="workshop-success" class="fas fa-check btn-success float-end d-none"></i>
                <a href="{{ route('workshopchoice.index', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}"
                   class="nav-link @if(preg_match('/\/workshopchoice/', url()->current(), $matches)) active @endif"
                   data-toggle="tooltip" data-placement="bottom" data-container="ul#littlesteps"
                   title="Workshop Preferences">
                    <i class="fas fa-rocket"></i>
                </a>
            </li>
            <li class="nav-item mx-5">
                <i id="room-success" class="fas fa-check btn-success float-end d-none"></i>
                <a href="{{ route('roomselection.index', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}"
                   class="nav-link @if(preg_match('/\/roomselection/', url()->current(), $matches)) active @endif"
                   data-toggle="tooltip" data-placement="bottom" data-container="ul#littlesteps" title="Room Selection">
                    <i class="fas fa-bed"></i>
                </a>
            </li>
            {{--            <li class="nav-item mx-5">--}}
            {{--                <i id="nametag-success" class="fas fa-check btn-success float-end d-none"></i>--}}
            {{--                <a href="{{ route('nametag.index', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}"--}}
            {{--                   class="nav-link @if(preg_match('/\/nametag/', url()->current(), $matches)) active @endif"--}}
            {{--                   data-toggle="tooltip" data-placement="bottom" data-container="ul#littlesteps"--}}
            {{--                   title="Nametag Customization">--}}
            {{--                    <i class="fas fa-id-card"></i>--}}
            {{--                </a>--}}
            {{--            </li>--}}
            {{--            <li class="nav-item mx-5">--}}
            {{--                <i id="medical-success" class="fas fa-check btn-success float-end d-none"></i>--}}
            {{--                <a href="{{ url('/confirm') . (preg_match('/\/(c|f)\/\d+$/', $_SERVER['REQUEST_URI'], $matches) ? substr($_SERVER['REQUEST_URI'], -7) : '') }}"--}}
            {{--                   class="nav-link @if(preg_match('/\/confirm/', url()->current(), $matches)) active @endif"--}}
            {{--                   data-toggle="tooltip" data-placement="bottom" data-container="ul#littlesteps"--}}
            {{--                   title="Medical Responses">--}}
            {{--                    <i class="fas fa-envelope"></i>--}}
            {{--                </a>--}}
            {{--            </li>--}}
        @else
            <li class="nav-item mx-5">
                <a href="#" class="nav-link lighter" data-toggle="tooltip" data-placement="bottom"
                   data-container="ul#littlesteps" title="Workshop Preference opens {{ $year->brochure_date }}">
                    <i class="fas fa-rocket"></i>
                </a>
            </li>
            <li class="nav-item mx-5">
                <a href="#" class="nav-link lighter" data-toggle="tooltip" data-placement="bottom"
                   data-container="ul#littlesteps" title="Room Selection opens {{ $year->brochure_date }}">
                    <i class="fas fa-bed"></i>
                </a>
            </li>
            {{--            <li class="nav-item mx-5">--}}
            {{--                <a href="#" class="nav-link lighter" data-toggle="tooltip" data-placement="bottom"--}}
            {{--                   data-container="ul#littlesteps" title="Nametags Customization opens {{ $year->brochure_date }}">--}}
            {{--                    <i class="fas fa-id-card"></i>--}}
            {{--                </a>--}}
            {{--            </li>--}}
            {{--            <li class="nav-item mx-5">--}}
            {{--                <a href="#" class="nav-link lighter" data-toggle="tooltip" data-placement="bottom"--}}
            {{--                   data-container="ul#littlesteps" title="Medical Responses open {{ $year->brochure_date }}">--}}
            {{--                    <i class="fas fa-envelope"></i>--}}
            {{--                </a>--}}
            {{--            </li>--}}
        @endif
    </ul>
@endif
