<h3>MUUSA Youth Medical Release Form</h3>
<p>By submitting this form, I affirm that I am the parent or legal guardian
    of {{ $camper->firstname }} {{ $camper->lastname }}. I give my consent for my child to attend the youth programming
    at Midwest Unitarian Universalist Summer Assembly. I give my consent and authority for the program staff or
    designated adult to take action at its discretion to help insure the safety, health and welfare of my child. I also
    request and empower the MUUSA program staff to authorize medical personnel and hospitals to provide all medical
    care, including but not limited to hospital tests, emergency surgical care, pathology, radiology and anesthesia,
    surgery and prescriptive drugs for the health of my child. I agree to assume responsibility for the medical payments
    for sickness or accident. I release Midwest Unitarian Universalist Summer Assembly and its staff from all liability
    for injury to the minor listed below and assumes the risk of such injury myself. I do this by entering my name and
    date below.</p>

@if(!$first)
    <button class="btn btn-info copyanswers float-right mb-2 d-print-none">Copy Answers from Above</button>
@endif

<h4>Emergency Contact</h4>

@include('includes.formgroup', ['label' => 'Parent/Legal Guardian Name', 'formobject' => $camper->medicalresponse,
    'attribs' => ['name' => $camper->yearattending_id . '-parent_name']])

@include('includes.formgroup', ['label' => 'Youth Sponsor Name (if other than above)',
    'formobject' => $camper->medicalresponse, 'attribs' => ['name' => $camper->yearattending_id . '-youth_sponsor']])

@include('includes.formgroup', ['label' => 'Emergency Mobile Number', 'formobject' => $camper->medicalresponse,
    'attribs' => ['name' => $camper->yearattending_id . '-mobile_phone']])

@include('includes.formgroup', ['type' => 'text', 'label' => 'Please share any medical, health, or behavioral concerns of which we should be aware. Please describe any known allergies. If your child should carry an asthma inhaler, epi-pen, or any other medical equipment or device, please make arrangements with MUUSA staff.',
    'formobject' => $camper->medicalresponse, 'attribs' => ['name' => $camper->yearattending_id . '-concerns']])

<h4>Healthcare Information</h4>

@include('includes.formgroup', ['label' => 'Name of Doctor', 'formobject' => $camper->medicalresponse,
    'attribs' => ['name' => $camper->yearattending_id . '-doctor_name']])

@include('includes.formgroup', ['label' => 'Phone Number of Doctor', 'formobject' => $camper->medicalresponse,
    'attribs' => ['name' => $camper->yearattending_id . '-doctor_nbr']])

@include('includes.question', ['type' => 'select', 'name' => $camper->yearattending_id . '-is_insured',
    'formobject' => $camper->medicalresponse, 'label' => 'Please indicate if you carry health insurance for your child.',
    'list' => [['id' => '0', 'option' => 'No'], ['id' => '1', 'option' => 'Yes']]])

<div class="insurance {{ !isset($camper->medicalresponse) || $camper->medicalresponse->is_insured == '0' ? 'd-none d-print-block' : '' }}">
    @include('includes.formgroup', ['label' => 'Policy Holder Name', 'formobject' => $camper->medicalresponse,
        'attribs' => ['name' => $camper->yearattending_id . '-holder_name']])

    <div class="form-group row">
        <label for="{{ $camper->yearattending_id }}-holder_birthday" class="col-md-4 control-label">Policy Holder
            Birthdate (yyyy-mm-dd)</label>
        <div class="col-md-6">
            <div class="input-group date" data-provide="datepicker"
                 data-date-format="yyyy-mm-dd" data-date-autoclose="true">
                <input id="{{ $camper->yearattending_id }}-holder_birthday" type="text" class="form-control"
                       name="{{ $camper->yearattending_id }}-holder_birthday"
                       value="{{ old($camper->yearattending_id . '-holder_birthday', $camper->medicalresponse ? $camper->medicalresponse->holder_birthday : '') }}"/>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                </div>
                <div class="input-group-addon">
                </div>
            </div>
        </div>
    </div>

    @include('includes.formgroup', ['label' => 'Health Insurance Carrier Name', 'formobject' => $camper->medicalresponse,
        'attribs' => ['name' => $camper->yearattending_id . '-carrier']])

    @include('includes.formgroup', ['label' => 'Health Insurance Carrier Phone Number',
        'formobject' => $camper->medicalresponse, 'attribs' => ['name' => $camper->yearattending_id . '-carrier_nbr']])

    @include('includes.formgroup', ['label' => 'Health Insurance Member ID', 'formobject' => $camper->medicalresponse,
        'attribs' => ['name' => $camper->yearattending_id . '-carrier_id']])

    @include('includes.formgroup', ['label' => 'Health Insurance Group Number', 'formobject' => $camper->medicalresponse,
        'attribs' => ['name' => $camper->yearattending_id . '-carrier_group']])

    <div class="form-group row">
        <label class="col-md-4 control-label">Is your child under the care of a physician for:</label>
        <div class="col-md-6 btn-group" data-toggle="buttons">
            <label class="btn btn-primary{{ $camper->medicalresponse && $camper->medicalresponse->is_epilepsy ? ' active' : '' }}">
                <input type="checkbox" name="{{ $camper->yearattending_id }}-is_epilepsy" autocomplete="off"
                       @if($camper->medicalresponse && $camper->medicalresponse->is_epilepsy)
                           checked
                    @endif
                /> Epilepsy
            </label>
            <label class="btn btn-primary{{ $camper->medicalresponse && $camper->medicalresponse->is_diabetes ? ' active' : '' }}">
                <input type="checkbox" name="{{ $camper->yearattending_id }}-is_diabetes" autocomplete="off"
                       @if($camper->medicalresponse && $camper->medicalresponse->is_diabetes)
                           checked
                    @endif
                /> Diabetes
            </label>
            <label class="btn btn-primary{{ $camper->medicalresponse && $camper->medicalresponse->is_add ? ' active' : '' }}">
                <input type="checkbox" name="{{ $camper->yearattending_id }}-is_add" autocomplete="off"
                       @if($camper->medicalresponse && $camper->medicalresponse->is_add)
                           checked
                    @endif
                /> Attention Deficit Disorder
            </label>
            <label class="btn btn-primary{{ $camper->medicalresponse && $camper->medicalresponse->is_adhd ? ' active' : '' }}">
                <input type="checkbox" name="{{ $camper->yearattending_id }}-is_adhd" autocomplete="off"
                       @if($camper->medicalresponse && $camper->medicalresponse->is_adhd)
                           checked
                    @endif
                /> Attention Deficit Hyperactivity Disorder
            </label>
        </div>
    </div>

</div>
