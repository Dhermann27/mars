@extends('layouts.app')

@section('title')
    Terms of Service
@endsection

@section('content')
    <div class="container px-3 py-5 px-lg-10 py-lg-6 bg-grey mb-5">
        <x-layouts.blog title="MUUSA {{ $year->year }} Camper Registration Agreement">
            <p>
                For purposes of this Agreement, &quot;Campers&quot; includes you and all other adults and minors
                named in your registration for MUUSA {{ $year->year }}. &quot;Campers&quot; also includes all other parents or
                legal guardians of any minor Campers.</p>
            <p>
                &quot;MUUSA&quot; includes the Midwest Unitarian Universalist Summer Assembly, Unitarian
                Universalist, Inc., and all of its officers, planning council members, members, staff, and
                volunteers. The &quot;Host Sites&quot; include the owners and operators of any locations used for
                MUUSA {{ $year->year }}, including but not limited to the Bloomington Unitarian Universalist Church and any
                hotels or other sites rented or leased by MUUSA.</p>
            <p>
                By submitting your registration for MUUSA {{ $year->year }}, you agree to the following terms on behalf of
                all Campers. You represent that you are over the age of 18 and legally competent and
                authorized to enter into this Agreement on behalf yourself and all Campers.</p>
            <p>
                Permission and Participation: You give any minor Campers permission to participate in
                MUUSA {{ $year->year }} activities for their applicable age group(s). You authorize MUUSA {{ $year->year }}
                program staff and volunteers to supervise your minor Campers while they are participating in
                MUUSA {{ $year->year }}.</p>
            <p>
                Assumption of Risk: Campers understand and acknowledge that there are known and
                unknown risks, dangers, and hazards that may be encountered in the Program and that
                accidents and injuries may occur without fault. All Campers fully assume the risk of any and
                all injuries, including death, and any damages or losses that any Camper may sustain in
                connection with participating in MUUSA {{ $year->year }}, including but not limited to any travel to or
                from any activity or location related to MUUSA {{ $year->year }}.</p>
            <p>
                Indemnification: Campers agree to indemnify, protect and hold harmless MUUSA and the
                Host Sites from any claim, loss, or liability whatsoever, including, but not limited to,
                personal injury, property damage, attorneys&apos; fees, court costs, and interest, arising in whole
                or in part out of any Camper&apos;s participation in MUUSA {{ $year->year }} or any breach of the covenants
                set forth in this Agreement.</p>
            <p>
                Waiver, Release of Liability, and Covenant Not to Sue: In addition, Campers fully release and
                discharge MUUSA and the Host Sites from any and all claims arising from injuries, including
                death, damages, support, or losses which may arise in whole or in part out of any Camper&apos;s
                participation in MUUSA {{ $year->year }}. Campers will not bring any claim or suit arising from or related in
                any way to MUUSA {{ $year->year }} against MUUSA or the Host Sites, either on their own behalf or on
                behalf of any minor child or youth.</p>
            <p>
                Fees and Expenses: Campers are responsible for paying any fees or expenses associated
                with their participation in MUUSA {{ $year->year }} in accordance with the policies and deadlines
                established by the MUUSA Planning Council. Should a Camper default on any payment,
                the Camper (or their parent / guardian, in the case of a minor camper) agrees to pay
                allowable interest and costs of collection, including, but not limited to, collection agency
                fees, court costs, and attorneys&apos; fees.</p>
            <p>
                Expectations: While participating in MUUSA {{ $year->year }}, all Campers are expected to follow the
                instructions of MUUSA and the Host Sites and to comply with all MUUSA and Host Site
                policies and rules. Any Camper&apos;s failure to do so may result in consequences up to and
                including removal from MUUSA {{ $year->year }} and/or being barred from registering for camp in
                future years.</p>
            <p>
                Media Release: Unless a Camper opts out (as provided below), each Camper grants MUUSA
                the perpetual right and license to use the Camper&apos;s name and likeness, including but not
                limited to any photo, video, or audio recording, published in any manner including but not
                limited to advertisements, brochures, news releases, newsletters, video, websites, email, and
                social media, for purposes of promoting MUUSA and its programs, internal camp
                communications, fundraising, and any other noncommercial purpose consistent with MUUSA&apos;s
                mission. Any Camper may opt out of this media release by sending a written notice of their
                desire to opt out via e-mail to the MUUSA registrar (registrar@MUUSA.org), with the subject
                line &quot;Media Release Opt-Out&quot; and the names of all Campers opting out of the media release.</p>
            <p>
                Emergency Treatment: In the event of an emergency, each Camper authorizes MUUSA
                and/or the Host Sites to secure from any hospital and/or physician, health care provider or
                emergency services personnel any treatment deemed necessary for the immediate care of
                any Camper, and agrees to be financially responsible for any and all medical services
                rendered to themselves and/or their minor child.</p>
            <p>
                The provisions of this Agreement are severable and the invalidity or unenforceability of any
                provision shall not affect the validity and enforceability of the other provisions.
                By registering for MUUSA {{ $year->year }}, I acknowledge that I have read and fully understand the
                contents of this Agreement and accept it of my own free will and without any reservation
                whatsoever, in consideration for my Camper(s) being permitted to participate in the
                Program.
            </p>
            <div class="clearfix"></div>
        </x-layouts.blog>
    </div>
@endsection
