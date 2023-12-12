<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description"
          content="Midwest Unitarian Universalist Summer Assembly, located outside Potosi, Missouri (Formerly Lake Geneva Summer Assembly in Williams Bay, Wisconsin)">
    <meta name="author" content="Dan Hermann">
    <title>
        @hassection('title')
            MUUSA: @yield('title')
        @else
            Midwest Unitarian Universalist Summer Assembly
        @endif
    </title>
    <!-- CSS Global Compulsory -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Jost|Antic Slab&display=swap">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    @yield('css')

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=bOMnaKo3RO"/>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=bOMnaKo3RO"/>
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=bOMnaKo3RO"/>
    <link rel="manifest" href="/site.webmanifest?v=bOMnaKo3RO"/>
    <link rel="mask-icon" href="/safari-pinned-tab.svg?v=bOMnaKo3RO" color="#5bbad5"/>
    <link rel="shortcut icon" href="/favicon.ico?v=bOMnaKo3RO"/>
    <meta name="apple-mobile-web-app-title" content="MUUSA"/>
    <meta name="application-name" content="MUUSA"/>
    <meta name="msapplication-TileColor" content="#da532c"/>
    <meta name="theme-color" content="#ffffff"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="//cdnjs.cloudflare.com/ajax/libs/retina.js/1.3.0/retina.min.js"></script>

</head>
<body>
{!! RecaptchaV3::initJs() !!}
<a id="top" href="#content" class="visually-hidden">Skip to content</a>
<!--Main Navigation-->
<header>
    <!-- Animated navbar-->
    <nav @class(['navbar', 'navbar-expand-lg', 'navbar-scroll', 'fixed-top' => config('app.name') != 'MUUSADusk'])>
        <div class="container-fluid">
            <button class="navbar-toggler ps-0" type="button" data-mdb-toggle="collapse"
                    data-mdb-target="#navbarMars" aria-controls="navbarMars" aria-expanded="false"
                    aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon d-flex justify-content-start align-items-center">
                        <i class="fas fa-bars"></i>
                      </span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMars">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="navbar-brand nav-link" href="/" aria-label="" data-no-retina>
                            <img class="navbar-brand-logo mx-3" src="/images/brand35.png" alt="MUUSA Logo"
                                 data-no-retina> MUUSA
                        </a>

                    </li>
                    <li class="nav-item pe-3 dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="menuCampInfo"
                           data-mdb-toggle="dropdown"
                           aria-expanded="false">
                            Camp Information
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="menuCampInfo">
                            @if($year->is_brochure)
                                <li class="mt-2"><h5><a href="{{ route('brochure') }}" class="dropdown-item">
                                            <i class="fas fa-desktop fa-fw"></i> Web Brochure</a></h5>
                                </li>
                                <li>
                                    <hr class="dropdown-divider"/>
                                </li>
                            @endif
                            <li><a href="{{ route('housing') }}" class="dropdown-item"><i class="fas fa-bath fa-fw"></i>
                                    Housing Options</a>
                            </li>
                            <li><a href="{{ route('programs') }}" class="dropdown-item">
                                    <i class="fas fa-sitemap fa-fw"></i> Programs
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('workshops.display') }}" class="dropdown-item">
                                    <i class="fas fa-map fa-fw"></i> Workshop List
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('themespeaker') }}" class="dropdown-item">
                                    <i class="fas fa-microphone fa-fw"></i> Theme Speakers
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cost') }}" class="dropdown-item">
                                    <i class="fas fa-calculator fa-fw"></i> Cost Calculator
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('scholarship') }}" class="dropdown-item">
                                    <i class="fas fa-universal-access fa-fw"></i> Scholarships
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('workshops.excursions') }}" class="dropdown-item">
                                    <i class="fas fa-binoculars fa-fw"></i> Excursions
                                </a>
                            </li>
                            {{--                            <li>--}}
                            {{--                                <hr class="dropdown-divider"/>--}}
                            {{--                            </li>--}}
                            {{--                            <span class="text-muted ms-2">For Campers Only</span>--}}
                            @if($year->is_calendar)
                                <li>
                                    <a href="#" class="dropdown-item">
                                        <i class="fas fa-calendar-alt fa-fw"></i> Daily Schedule</a>
                                </li>
                            @endif
                            {{--                            <li>--}}
                            {{--                                <a href="{{ route('directory') }}" class="dropdown-item">--}}
                            {{--                                    <i class="fas fa-address-book fa-fw"></i> Online Directory</a>--}}
                            {{--                            </li>--}}
                            @if($year->is_artfair)
                                <li>
                                    <a href="#" class="dropdown-item">
                                        <i class="fas fa-shopping-bag fa-fw"></i> Art Fair Submission</a>
                                </li>
                            @endif
                            @if($year->is_workshop_proposal)
                                <li>
                                    <a href="https://forms.gle/fNRBeXJ4nmTdvHjs8"
                                       class="dropdown-item">
                                        <i class="fas fa-chalkboard-teacher fa-fw"></i> Workshop Proposal
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                    @if($year->next_muse !== false)
                        <li class="nav-item pe-3"><a>{{ $year->next_muse }}</a>
                        </li>
                    @endif
                    <li class="nav-item pe-3"><a href="{{ route('contact.index') }}" class="nav-link">Contact Us</a>
                    {{--                    <li class="nav-item pe-3"><a href="https://www.bonfire.com/store/muusa/" class="nav-link">Store</a>--}}
                    {{--                    <li class="nav-item pe-3"><a href="{{ route('activityproposal') }}" class="nav-link">Activity Proposal</a>--}}

                    @can('is-council')
                        <li class="nav-item pe-3 dropdown has-megamenu">
                            <a class="nav-link dropdown-toggle" href="#" role="button" id="menuAdmin"
                               data-mdb-toggle="dropdown"
                               aria-expanded="false">
                                Admin
                            </a>
                            <div class="dropdown-menu megamenu" role="menu">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-6">
                                        <div class="col-megamenu">
                                            <div>
                                                <a class="btn btn-lg btn-primary"
                                                   href="{{ route('admin') }}">Dashboard</a>
                                            </div>
                                            <strong>Recent Searches</strong>
                                            <ul id="recentSearches" class="list-unstyled">
                                                <li>Loading campers... <i class="fa fa-spinner-third fa-spin m-1"></i>
                                                </li>
                                            </ul>
                                        </div>  <!-- col-megamenu.// -->
                                    </div><!-- end col-3 -->
                                    <div class="col-lg-3 col-6">
                                        <div class="col-megamenu">
                                            <h6 class="title">Reports</h6>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <a href="{{ route('reports.deposits') }}">Bank Deposits</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('reports.campers') }}">Campers ({{ $year->year }}
                                                        )</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('reports.workshops') }}"
                                                       data-mdb-toggle="tooltip" title="Slow call">
                                                        Workshop Attendees
                                                        <i class="fas fa-turtle fa-xl"></i>
                                                    </a>
                                                </li>
                                                {{--                                                <li><a href="#">Custom Menu</a></li>--}}
                                                {{--                                                <li><a href="#">Custom Menu</a></li>--}}
                                                {{--                                                <li><a href="#">Custom Menu</a></li>--}}
                                                {{--                                                <li><a href="#">Custom Menu</a></li>--}}
                                                {{--                                                <li><a href="#">Custom Menu</a></li>--}}
                                                {{--                                                <li><a href="#">Custom Menu</a></li>--}}
                                            </ul>
                                        </div>  <!-- col-megamenu.// -->
                                    </div><!-- end col-3 -->
                                    <div class="col-lg-3 col-6">
                                        <div class="col-megamenu">
                                            <h6 class="title">Tools</h6>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <a href="{{ route('tools.cognoscenti') }}">Cognoscenti</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('tools.nametags.all') }}"
                                                       data-mdb-toggle="tooltip" title="Firefox only, Slow call">
                                                        Nametags Print
                                                        <i class="fab fa-firefox-browser fa-xl"></i>
                                                        <i class="fas fa-turtle fa-xl"></i>
                                                    </a>
                                                </li>
                                                <li><a href="{{ route('tools.staff.index') }}">Position Assignments</a>
                                                </li>
                                            </ul>
                                        </div>  <!-- col-megamenu.// -->
                                    </div><!-- end col-3 -->
                                    @can('is-super')
                                        <div class="col-lg-3 col-6">
                                            <div class="col-megamenu">
                                                <h6 class="title">Superuser</h6>
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <a href="{{ route('camperselect.index', ['id' => 0]) }}">
                                                            Create New Family
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.positions.index') }}">Staff
                                                            Positions</a>
                                                    </li>
                                                </ul>
                                            </div>  <!-- col-megamenu.// -->
                                        </div>
                                    @endcan
                                </div><!-- end row -->
                            </div>
                        </li>
                    @endcan
                </ul>

                <div class="d-flex align-items-center">
                    @auth
                        @can('register', $year)
                            <div class="btn-group ms-4">
                                <a href="{{ route('dashboard') }}" class="btn btn-lg btn-primary">Registration</a>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <h6 class="dropdown-header">
                                            <i class="fas fa-circle-user"></i>
                                            {{ Auth::user()->email }}
                                        </h6>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider"/>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('camperselect.index')}}">
                                            <i class="fas fa-users fa-fw"></i> Camper Selection
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('household.index')}}">
                                            <i class="fas fa-home fa-fw"></i> Billing Address
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('camperinfo.index')}}">
                                            <i class="fas fa-user-gear fa-fw"></i> Camper Information
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('payment.index')}}">
                                            <i class="fas fa-usd-square fa-fw"></i> Account Statement
                                        </a>
                                    </li>
                                    @if(!$year->is_brochure)
                                        <li>
                                            <hr class="dropdown-divider"/>
                                        </li>
                                        <h6 class="dropdown-header">
                                            Opens {{ $year->brochure_date }}
                                        </h6>
                                        <li>
                                            <a href="#" class="dropdown-item disabled">
                                                <i class="fas fa-rocket fa-fw"></i> Workshop List
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item disabled">
                                                <i class="fas fa-bed fa-fw"></i> Room Selection
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item disabled">
                                                <i class="fas fa-id-card fa-fw"></i> Nametags
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item disabled">
                                                <i class="fas fa-envelope fa-fw"></i> Confirmation
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{ route('roomselection.index') }}" class="dropdown-item">
                                                <i class="fas fa-bed fa-fw"></i> Room Selection
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('workshopchoice.index') }}" class="dropdown-item">
                                                <i class="fas fa-rocket fa-fw"></i> Workshops
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('nametag.index') }}" class="dropdown-item">
                                                <i class="fas fa-id-card fa-fw"></i> Nametags
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('medicalresponse.index') }}" class="dropdown-item">
                                                <i class="fas fa-envelope fa-fw"></i> Confirmation
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <hr class="dropdown-divider"/>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('logout') }}"
                                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <i class="fas fa-circle-user px-2"></i> {{ Auth::user()->email }}

                            <a title="LogoutS"
                               href="{{ route('logout') }}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt ms-5"></i>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                  style="display: none;">
                                @csrf
                            </form>
                        @endcan
                    @else
                        <a class="btn btn-lg btn-secondary px-3 me-2" href="{{ route('login') }}"
                           role="button">Login</a>
                        {{--                        <a class="btn btn-lg btn-primary" href="{{ route('register') }}" role="button">--}}
                        {{--                            Get Started--}}
                        {{--                        </a>--}}
                    @endif

                </div>
            </div>
        </div>
    </nav>
    <!-- Animated navbar -->


    @hasSection('title')
        <div class="jumbotron jumbotron-fluid text-white @hasSection('image') bg-image @else bg-primary py-5 @endif"
             @hasSection('image') style="background-size: cover; background-position-y: bottom; background-image: @yield('image');" @endif>
            <div class="container mt-5 pt-5 text-shadow">
                <h1 class="display-4">
                    @yield('title')
                </h1>
                @hassection('heading')
                    <p>
                        @yield('heading')
                    </p>
                @endif
            </div>
        </div>
    @endif
</header>
<!--Main Navigation-->

{{--    <!--Navbar Start-->--}}
{{--    <nav class="navbar navbar-expand-lg fixed-top navbar-custom" id="navbar">--}}
{{--        <div class="container-fluid">--}}

{{--            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"--}}
{{--                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">--}}
{{--                <i class="fas fa-bars"></i>--}}
{{--            </button>--}}
{{--            <div class="collapse navbar-collapse" id="navbarCollapse">--}}
{{--                <ul class="navbar-nav ml-auto navbar-center" id="mySidenav">--}}

{{--                    @can('is-council')--}}
{{--                        <li class="nav-item mt-1">--}}
{{--                            <div class="dropdown">--}}
{{--                                <a class="nav-link dropdown-toggle" href="" role="button" data-toggle="dropdown"--}}
{{--                                   aria-haspopup="true" aria-expanded="false">--}}
{{--                                    Admin--}}
{{--                                </a>--}}
{{--                                <div class="dropdown-menu dropdown-menu-right mt-0">--}}
{{--                                    <a href="{{ route('tools.cognoscenti') }}" class="dropdown-item">Cognoscenti</a>--}}
{{--                                    <div class="dropdown-divider"></div>--}}
{{--                                    @can('is-super')--}}
{{--                                        <a class="disabled pl-2" tabindex="-1" href="#">Superuser Functions</a>--}}
{{--                                        <a class="dropdown-item" href="{{ route('household.index', ['id' => 0]) }}">--}}
{{--                                            Create New Family</a>--}}
{{--                                        <a class="dropdown-item" href="{{ route('admin.distlist.index') }}">--}}
{{--                                            Distribution List</a>--}}
{{--                                        <a class="dropdown-item" href="{{ route('admin.roles.index') }}">Roles</a>--}}
{{--                                        <a class="dropdown-item" href="{{ route('admin.positions.index') }}">--}}
{{--                                            Staff Positions</a>--}}
{{--                                        <div class="dropdown-divider"></div>--}}
{{--                                    @endif--}}
{{--                                    <a class="disabled pl-2" tabindex="-1" href="#">Reports</a>--}}
{{--                                    <a class="dropdown-item" href="{{ route('reports.campers') }}">Campers</a>--}}
{{--                                    <a class="dropdown-item" href="{{ route('reports.outstanding') }}">--}}
{{--                                        Outstanding Balances</a>--}}
{{--                                    <a class="dropdown-item" href="{{ route('reports.programs') }}">Programs</a>--}}
{{--                                    <a class="dropdown-item" href="{{ route('reports.chart') }}">--}}
{{--                                        Registration Chart</a>--}}
{{--                                    <a class="dropdown-item" href="{{ route('roomselection.map') }}">--}}
{{--                                        Room Selection Map</a>--}}
{{--                                    <a class="dropdown-item" href="{{ route('reports.rooms') }}">Rooms</a>--}}
{{--                                    <a class="dropdown-item" href="{{ route('reports.workshops') }}">--}}
{{--                                        Workshop Attendees</a>--}}
{{--                                    <div class="dropdown-divider"></div>--}}
{{--                                    <a class="disabled pl-2" tabindex="-1" href="#">Tools</a>--}}
{{--                                    <a class="dropdown-item" tabindex="-1" href="{{ route('tools.staff.index') }}">--}}
{{--                                        Position Assignments</a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </li>--}}
{{--                    @endif--}}



{{--                    <li class="nav-item mt-1">--}}
{{--                        @can('has-paid')--}}
{{--                            <div class="dropdown">--}}
{{--                                <a class="nav-link dropdown-toggle" href="" role="button" data-toggle="dropdown"--}}
{{--                                   aria-haspopup="true" aria-expanded="false">--}}
{{--                                    Registration--}}
{{--                                </a>--}}
{{--                                <div class="dropdown-menu dropdown-menu-right mt-0">--}}
{{--                                    <a href="{{ route('household.index') }}" class="dropdown-item">--}}
{{--                                        <i class="fas fa-home fa-fw"></i> Household</a>--}}
{{--                                    <a href="{{ route('camperinfo.index') }}" class="dropdown-item">--}}
{{--                                        <i class="fas fa-users fa-fw"></i> Campers</a>--}}
{{--                                    <a href="{{ route('payment.index') }}" class="dropdown-item">--}}
{{--                                        <i class="fas fa-usd-square fa-fw"></i> Statement</a>--}}
{{--                                    @if(!$year->is_brochure)--}}
{{--                                        <div class="dropdown-divider"></div>--}}
{{--                                        <h6 class="dropdown-header">--}}
{{--                                            Opens {{ $year->brochure_date }}--}}
{{--                                        </h6>--}}
{{--                                        <a href="#" class="dropdown-item disabled">Workshop List</a>--}}
{{--                                        <a href="#" class="dropdown-item disabled">Room Selection</a>--}}
{{--                                        <a href="#" class="dropdown-item disabled">Nametags</a>--}}
{{--                                        <a href="#" class="dropdown-item disabled">Confirmation</a>--}}
{{--                                    @else--}}
{{--                                        <a href="{{ route('workshopchoice.index') }}" class="dropdown-item">--}}
{{--                                            <i class="fas fa-rocket fa-fw"></i> Workshops</a>--}}
{{--                                        <a href="{{ route('roomselection.index') }}" class="dropdown-item">--}}
{{--                                            <i class="fas fa-bed fa-fw"></i> Room Selection</a>--}}
{{--                                        <a href="{{ route('nametag.index') }}" class="dropdown-item">--}}
{{--                                            <i class="fas fa-id-card fa-fw"></i> Nametags</a>--}}
{{--                                        --}}{{--                                        <a href="{{ route('confirm') }}" class="dropdown-item">--}}
{{--                                        --}}{{--                                            <i class="fas fa-envelope fa-fw"></i> Confirmation</a>--}}
{{--                                    @endif--}}


<section id="content" class="p-0">
    @yield('content')
</section>

<!-- Footer -->
<footer class="bg-dark text-center text-white">
    <!-- Grid container -->
    <div class="container p-4">
        <!-- Section: Social media -->
        <section class="mb-4">
            <!-- Facebook -->
            <a class="btn btn-outline-light btn-floating m-1 btn-amber-hover"
               href="@auth https://www.facebook.com/groups/Muusans/@else https://www.facebook.com/Muusa2013/@endif"
               role="button">
                <i class="fab fa-facebook-f fa-xl"></i>
            </a>

            <!-- Twitter -->
            <a class="btn btn-outline-light btn-floating m-1 btn-amber-hover"
               href="https://twitter.com/muusa1"
               role="button">
                <i class="fab fa-twitter fa-xl"></i>
            </a>

            <!-- YouTube -->
            <a class="btn btn-outline-light btn-floating m-1 btn-amber-hover"
               href="https://www.youtube.com/channel/UC-lNXF9IYAC-PSpvWWJkkMw" role="button">
                <i class="fab fa-youtube fa-xl"></i>
            </a>
        </section>
        <!-- Section: Social media -->

        <!-- Section: Form -->
        {{--        <section>--TODO: brochure signup}}
        {{--            <form action="">--}}
        {{--                <!--Grid row-->--}}
        {{--                <div class="row d-flex justify-content-center">--}}
        {{--                    <!--Grid column-->--}}
        {{--                    <div class="col-auto">--}}
        {{--                        <p class="pt-2">--}}
        {{--                            <strong>Sign up to receive our web brochure</strong>--}}
        {{--                        </p>--}}
        {{--                    </div>--}}
        {{--                    <!--Grid column-->--}}

        {{--                    <!--Grid column-->--}}
        {{--                    <div class="col-md-5 col-12">--}}
        {{--                        <!-- Email input -->--}}
        {{--                        <div class="form-outline form-white mb-4">--}}
        {{--                            <input type="email" id="form5Example21" class="form-control"/>--}}
        {{--                            <label class="form-label" for="form5Example21">Email address</label>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                    <!--Grid column-->--}}

        {{--                    <!--Grid column-->--}}
        {{--                    <div class="col-auto">--}}
        {{--                        <!-- Submit button -->--}}
        {{--                        <button type="submit" class="btn btn-outline-light mb-4">--}}
        {{--                            Subscribe--}}
        {{--                        </button>--}}
        {{--                    </div>--}}
        {{--                    <!--Grid column-->--}}
        {{--                </div>--}}
        {{--                <!--Grid row-->--}}
        {{--            </form>--}}
        {{--        </section>--}}
        <!-- Section: Form -->

        <!-- Section: Links -->
        <section>
            <!--Grid row-->
            <div class="row">
                <!--Grid column-->
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5>Camp Information</h5>

                    <ul class="list-unstyled mb-0">
                        @if($year->is_brochure)
                            <li><a class="text-white underlined-link" href="{{ route('brochure') }}">Web
                                    Brochure</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider"/>
                            </li>
                        @endif
                        <li><a class="text-white underlined-link" href="{{ route('housing') }}">Housing
                                Options</a></li>
                        <li><a class="text-white underlined-link"
                               href="{{ route('programs') }}">Programs</a>
                        </li>
                        <li>
                            <a class="text-white underlined-link" href="{{ route('workshops.display') }}">Workshop
                                List</a>
                        </li>
                        <li>
                            <a class="text-white underlined-link" href="{{ route('themespeaker') }}">Theme
                                Speaker</a>
                        </li>
                        <li><a class="text-white underlined-link" href="{{ route('cost') }}">Cost
                                Calculator</a>
                        </li>
                        <li>
                            <a class="text-white underlined-link"
                               href="{{ route('scholarship') }}">Scholarships</a>
                        </li>
                        <li>
                            <a class="text-white underlined-link"
                               href="{{ route('workshops.excursions') }}">Excursions</a>
                        </li>
                        <li>
                            <a class="text-white underlined-link" href="{{ route('termsofservice') }}">Terms of
                                Service</a>
                        </li>
                    </ul>
                </div>
                <!--Grid column-->


                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5>Questions?</h5>
                    <i class="far fa-mailbox"></i> Email us at <a href="mailto:muusa@muusa.org">muusa@muusa.org</a>

                    <hr class="dropdown-divider"/>

                    {{--                    <h5 class="mt-5">For Registered Campers Only</h5>--}}
                    <ul class="list-unstyled mb-0">
                        @if($year->is_calendar)
                            <li><a class="text-white underlined-link" href="#">Daily Schedule</a></li>
                        @endif
                        {{--                        <li><a class="text-white underlined-link" href="{{ route('directory') }}">Online--}}
                        {{--                                Directory</a>--}}
                        {{--                        </li>--}}
                        @if($year->is_artfair)
                            <li><a class="text-white underlined-link" href="#">Art Fair Submission</a></li>
                        @endif
                        @if($year->is_workshop_proposal)
                            <li>
                                <a class="text-white underlined-link"
                                   href="https://forms.gle/fNRBeXJ4nmTdvHjs8">
                                    Workshop Proposal</a></li>
                        @endif
                    </ul>
                </div>

                <!--Grid column-->
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    @can('register', $year)
                        <ul class="list-unstyled mb-0">
                            <li>
                                <a href="{{ route('dashboard') }}" class="btn btn-lg btn-primary">Registration</a></li>
                            <hr class="dropdown-divider"/>
                            <li><a class="text-white underlined-link"
                                   href="{{ route('camperselect.index') }}">Camper Selection</a></li>
                            <li><a class="text-white underlined-link"
                                   href="{{ route('household.index') }}">Billing Address</a></li>
                            <li>
                                <a class="text-white underlined-link" href="{{ route('camperinfo.index') }}">Camper
                                    Information</a>
                            </li>
                            <li>
                                <a class="text-white underlined-link" href="{{ route('payment.index') }}">Account
                                    Statement</a>
                            </li>
                            @if(!$year->is_brochure)
                                <hr/>
                                <h6>Opens {{ $year->brochure_date }}</h6>
                                <li>Workshop Preferences</li>
                                <li>Room Selection</li>
                                <li>Nametag Customization</li>
                                <li>Medical Response(s)</li>
                            @else
                                <li><a class="text-white underlined-link" href="{{ route('workshopchoice.index') }}">Workshop
                                        Preferences</a></li>
                                <li><a class="text-white underlined-link" href="{{ route('roomselection.index') }}">Room
                                        Selection</a></li>
                                <li><a class="text-white underlined-link"
                                       href="{{ route('nametag.index') }}">Nametags</a>
                                </li>
                                <li><a class="text-white underlined-link" href="{{ route('medicalresponse.index') }}">Medical
                                        Response(s)</a>
                                </li>
                            @endif
                        </ul>
                    @endcan
                </div>
            </div>
            <!--Grid row-->
        </section>
        <!-- Section: Links -->
    </div>
    <!-- Grid container -->

    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        {{ $year->year }} &copy; Midwest Unitarian Universalist Summer Assembly. Design by <a
            href="https://mdbootstrap.com/" target="_blank"
            class="text-white underlined-link">MDBootstrap.com</a>.
    </div>
    <!-- Copyright -->
</footer>
<!-- Footer -->
<script src="{{ mix('js/app.js') }}"></script>

@yield('script')
@stack('inlinescripts')
</body>
</html>
