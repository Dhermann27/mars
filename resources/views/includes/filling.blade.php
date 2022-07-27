@if($year->is_brochure)
    @if($workshop->capacity == 999)
        <span class="alert alert-success badge float-end">Unlimited Enrollment</span>
    @elseif($workshop->enrolled >= $workshop->capacity)
        <span class="alert alert-danger badge float-end">Waitlist Available</span>
    @elseif($workshop->enrolled >= ($workshop->capacity * .75))
        <span class="alert alert-warning badge float-end">Filling Fast!</span>
    @else
        <span class="alert alert-info badge float-end">Open For Enrollment</span>
    @endif
@endif
