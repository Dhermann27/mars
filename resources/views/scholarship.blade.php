@extends('layouts.app')

@section('title')
    Scholarship Process
@endsection

@section('heading')
    This page can teach you all you need to know about how to apply for a scholarship to receive financial assistance for MUUSA.
@endsection

@section('image')
    url('/images/scholarship.jpg')
@endsection

@section('content')
    @if($year->is_scholarship_full == '1')
        <div class="alert alert-warning"> Unfortunately, all {{ $year->year }} scholarship funds have been awarded.
            Please check back in {{ $year->year+1 }}.
        </div>
    @endif
    <div class="row px-lg-5">
        <ul id="nav-tab" class="nav nav-tabs pt-lg-3" role="tablist">
            <li class="nav-item pl-5">
                <button class="nav-link active" data-mdb-toggle="tab" data-mdb-target="#tab-1" type="button"
                        role="tab" aria-controls="maininfo" aria-selected="true">
                    Main Information
                </button>
            </li>
            <li class="nav-item ml-2">
                <button class="nav-link" data-mdb-toggle="tab" data-mdb-target="#tab-2" type="button" role="tab"
                        aria-controls="app" aria-selected="false">
                    Appendix
                </button>
            </li>
        </ul>
        <div class="tab-content" id="sch-content">
            <div class="tab-pane fade active show py-3" id="tab-1" role="tabpanel">
                <p class="mt-3">Our goal at MUUSA is to make camp affordable for those who wish to attend. We
                    strive to accomplish this goal by providing lower-cost housing options, inviting
                    campers to apply for staff positions that offer a credit toward camp expenses, and
                    by offering scholarship funds to offset costs.</p>
                <p>This document lays out the scholarship application process, details the sources of scholarship
                    funds that are available, and explains the scholarship award process.</p>
                <h3 class="pb-2">How to Apply For a Scholarship</h3>
                <h5>Begin an application form, available <a
                        href="https://docs.google.com/forms/d/1UL5tRegpPlaIuSMLHhy9R5rPLOdpkGlKe2vHGHZuJyc/edit">here</a>.
                </h5>
                <p>If you are unable to use the online form, you may request a hard copy application form by
                    contacting the MUUSA Scholarship Coordinator. If you mail in a hard copy form, your form must be
                    RECEIVED (not postmarked) by May 10 in order to be considered. </p>
                <p><strong>Scholarships are limited and cannot be guaranteed. Campers should not make any
                        travel or financial plans on the assumption that they will receive a scholarship or
                        any specific scholarship amount until they have received written (via email or
                        letter) confirmation of their scholarship award.</strong></p>
                <h3 class="pb-2">Timing</h3>
                <p>The Scholarship process will open on February 20. Campers seeking financial assistance
                    must submit scholarship applications no later than May 10. The timeline for the
                    process is as follows:</p>
                <table>
                    <tbody>
                    <tr>
                        <td class="pb-3"><strong>February 20</strong></td>
                        <td>Scholarship process opens; applications can be submitted</td>
                    </tr>
                    <tr>
                        <td class="pb-3"><strong>May 10</strong></td>
                        <td>Applications due, including receipt of all financial information and
                            supplemental form.
                        </td>
                    </tr>
                    <tr>
                        <td class="pb-3"><strong>May 20</strong></td>
                        <td>Scholarship determinations made and campers contacted.</td>
                    </tr>
                    </tbody>
                </table>
                <p>Applicants who submit their applications by the May 10 deadline will be notified of
                    their award by May 20, barring unforeseen delays. Balances of camper invoices on the
                    MUUSA website will be updated as soon as feasible after scholarships are awarded.
                <h3 class="pb-2">Details</h3>
                <h4 class="pb-2">Review and Award of Scholarships</h4>
                <p>The scholarship process will be administered by the MUUSA Scholarship Committee, which
                    includes: Nate Warner, John Sandman, Duncan Metcalfe, Cheryl Heinz, and Karen Seymour-Ells for
                    MUUSA {{ $year->year }}. Nate Warner will serve as the Scholarship Committee Coordinator and
                    the primary contact for the Scholarship Committee.</p>
                <p>The Committee will review all
                    applications and independently determine how available scholarship funds will be
                    allocated.</p>
                <p>Scholarships will be awarded based upon the following criteria:</p>
                <ul>
                    <li>Financial need</li>
                    <li>Welcoming new campers who have not previously had an opportunity to attend MUUSA
                    </li>
                    <li>Interest in attending MUUSA and being part of the MUUSA community</li>
                    <li>Availability of scholarship funds</li>
                </ul>
                <p>To make the process as fair as possible, members of the Scholarship Committee will recuse
                    themselves from any decision involving a scholarship application by a family member or
                    close friend.</p>
                <p>The Scholarship Committee has sole discretion to determine whether to award a scholarship
                    and in what amount. The Committee's decisions are final.</p>
                <h4 class="pb-2">Scholarship Amounts</h4>
                <p>For {{ $year->year }}, the total scholarship amounts awarded will be capped on
                    a per-camper (not per-family) basis. The maximum scholarship awards will be as
                    follows:</p>
                <p>Adults (including YAs): $400 per person<br/> Children and Youth age 6 and up: $250 per
                    person<br/> Children under age 6: $0</p>
                <p>Please note that these are maximum awards. In many cases a lower scholarship amount may
                    be awarded so as to share available funds among a broader group of applicants.</p>
                <p>Additionally, scholarships awarded will not exceed total fees after all additions and
                    reductions (including staff honoraria) less $100 for each adult camper (including YAs)
                    and $50 for each child in . In other words, all adults receiving MUUSA scholarship funds
                    will pay at least $100 or $50 (depending on age) for their week at camp, regardless
                    of honoraria.</p>
                <p>Scholarship amounts do not apply toward additional MUUSA workshop fees or excursions.</p>
                <h4 class="pb-2">Why Would I Take on a Staff Role at MUUSA if I am Applying for a
                    Scholarship?</h4>
                <p>Choosing to serve in a MUUSA staff role allows you to give back to the MUUSA community in
                    a way that best reflects your skills and talents. Whether you choose to lead a workshop,
                    assist in the Children's Program, or fill another crucial role, you are providing a
                    valuable contribution to the MUUSA community. Furthermore, funding for staff honoraria
                    is included in the MUUSA budget whereas scholarships (and their amounts) are subject to
                    availability.</p>
                <p><strong>Thank you for your interest in applying for a scholarship for
                        MUUSA {{ $year->year }}. We hope this document has helped clarify the scholarship
                        process. If you have further questions or concerns please contact the Scholarship
                        Committee Coordinator, Nate Warner using the Contact Us (select "Scholarship Coordinator") link
                        above.</strong>
                </p>
            </div>

            <div class="tab-pane fade py-3" id="tab-2" role="tabpanel">
                <h3>Appendix</h3>
                <h4>Where Do Scholarship Funds Come From?</h4>
                <p>MUUSA scholarships provide a discount off of the fees that MUUSA would otherwise charge a camper for
                    their stay. Because MUUSA has to cover its financial obligations, the money that we are not
                    collecting from campers who receive scholarships must be made up from some other source. Scholarship
                    funds may come from one primary source: MUUSA's own scholarship fund.</p>
                <p>Campers pay MUUSA for their week at camp. The revenue we receive from campers covers our fees to
                    Indiana University and UUCB (for food, lodging, staff, etc.) and MUUSA expenses (for program staff,
                    supplies, insurance, etc.).</p>
                <p>MUUSA's scholarship funds come mainly from voluntary donations from MUUSA campers and through
                    fundraising. MUUSA's main scholarship fundraising sources have historically included donations paid
                    as part of the registration process, a portion of MUUSA bookstore profits (if any), and art show
                    sales.</p>
                <p>Each year, the Treasurer and Registrar will review the balance in the Scholarship Fund as well as
                    MUUSA's projected enrollment and revenue. They will then recommend an amount to be included in the
                    budget for scholarship awards. The amount available for distribution will be included in the budget
                    approved by the Planning Council, and will be finalized before scholarship awards are communicated
                    to campers.
                </p>
            </div>
        </div>
    </div>
@endsection
