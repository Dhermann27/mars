<ul {{ $attributes->class(['stepper', 'stepper-vertical' => $isVertical, 'stepper-large' => $isLarge]) }}>
    @if(request()->route()->hasParameter('id'))
        <li class="text-bold mt-2 text-md-center"><i class="far fa-user-alt"></i> {!! $stepdata['username'] !!}</li>
    @endif
    <li class="stepper-step mt-3">
        <x-steps.header url="camperselect" :stepdata="$stepdata['campersSelected']" operation="gt"
                        comparator="0" icon="users" :is-large="$isLarge" :tooltip="__('registration.completeabove')">
            Camper Selection
        </x-steps.header>
        <div class="stepper-content">
            @if($isLarge)
                <span>Enter your names and select which campers are attending this year.</span>
            @else
                <span class="small">
                  {{ $stepdata['campersSelected'] . ' camper' . ($stepdata['campersSelected']!=1 ? 's' : '') }}
                    attending in {{$year->year}}
                </span>
            @endif
        </div>
    </li>
    <li class="stepper-step">
        <x-steps.header url="household" :stepdata="$stepdata['isAddressCurrent']" icon="home" :is-large="$isLarge"
                        :tooltip="__('registration.completeabove')">
            Billing Address
        </x-steps.header>
        <div class="stepper-content">
            @if($isLarge)
                <span>
                    Enter your mailing address and let us know if you want to receive e-mail communication or
                    apply for a scholarship.
                </span>
            @endif
        </div>
    </li>
    <li class="stepper-step">
        <x-steps.header url="camperinfo" :stepdata="$stepdata['isCamperDetail']" icon="user-gear" :is-large="$isLarge"
                        :tooltip="__('registration.completeabove')">
            Camper Information
        </x-steps.header>
        <div class="stepper-content">
            @if($isLarge)
                <span>Enter details about the camper(s) in your family.</span>
            @endif
        </div>
    </li>
    <li class="stepper-step">
        <x-steps.header url="payment" :stepdata="$stepdata['amountDueNow']" operation="lte"
                        comparator="0" icon="usd-square" :is-large="$isLarge"
                        :tooltip="__('registration.completeabove')">
            Account Statement
        </x-steps.header>
        <div class="stepper-content">
            @if($isLarge)
                <span>See a list of your charges and post a payment via PayPal.</span>
            @elseif($stepdata['amountDueNow'] > 0)
                <span class="small">Amount Due Now: ${{ number_format($stepdata['amountDueNow'], 2) }}</span>
            @elseif(array_key_exists('amountDueArrival', $stepdata))
                <span class="small">
                    Amount Due Now: $0.00<br/>
                    @if($year->is_brochure && $stepdata['isRoomsSelected'])
                        Amount Due Upon Arrival: ${{ number_format($stepdata['amountDueArrival'], 2) }}
                    @endif
                </span>
            @endif
        </div>
    </li>
    @if(!$year->is_brochure)
        <li>
            <hr class="dropdown-divider"/>
        </li>
        @if($isLarge)
            <h3>Opens {{ $year->brochure_date }}</h3>
        @else
            <h6 class="dropdown-header"><strong>Opens {{ $year->brochure_date }}</strong></h6>
        @endif
    @endif
{{--    <li class="stepper-step">--}}
{{--        <x-steps.header url="roomselection" :stepdata="$stepdata['isRoomsSelected']" icon="bed" :is-large="$isLarge"--}}
{{--                        :tooltip="$year->is_brochure ? __('registration.completeabove') : __('registration.notopenyet')">--}}
{{--            Room Selection--}}
{{--        </x-steps.header>--}}
{{--        <div class="stepper-content">--}}
{{--            @if($isLarge)--}}
{{--                <span>Select a room where you will stay and see who your neighbors might be.</span>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </li>--}}
    <li class="stepper-step">
        <x-steps.header url="workshopchoice" :stepdata="$stepdata['workshopsSelected']" operation="gt"
                        comparator="0" icon="rocket" :is-large="$isLarge" is-required="false"
                        :tooltip="$year->is_brochure ? __('registration.completeabove') : __('registration.notopenyet')">
            Workshop Preferences
        </x-steps.header>
        <div class="stepper-content">
            @if($isLarge)
                <span>
                    Choose which workshops you would like to attend during the week. <strong>Optional.</strong>
                </span>
            @elseif($stepdata['workshopsSelected'] != null)
                <span class="small">
                  {{ $stepdata['workshopsSelected'] . ' workshop' . ($stepdata['workshopsSelected']!=1 ? 's' : '') }}
                    chosen
                </span>
            @endif
        </div>
    </li>
{{--    <li class="stepper-step">--}}
{{--        <x-steps.header url="nametag" :stepdata="$stepdata['nametagsCustomized']" operation="gt"--}}
{{--                        comparator="0" icon="id-card" :is-large="$isLarge" is-required="false"--}}
{{--                        :tooltip="$year->is_brochure == 1 ? __('registration.completeabove') : __('registration.notopenyet')">--}}
{{--            Nametag Customization--}}
{{--        </x-steps.header>--}}
{{--        <div class="stepper-content">--}}
{{--            @if($isLarge)--}}
{{--                <span>--}}
{{--                    Customize the nametags that you'll receive during check-in. <strong>Optional.</strong>--}}
{{--                </span>--}}
{{--            @elseif($stepdata['nametagsCustomized'] != null)--}}
{{--                <span class="small">--}}
{{--                  {{ $stepdata['nametagsCustomized'] . ' nametags' . ($stepdata['nametagsCustomized']!=1 ? 's' : '') }}--}}
{{--                    decorated--}}
{{--                </span>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </li>--}}
{{--    <li class="stepper-step">--}}
{{--        <x-steps.header url="medicalresponse" :stepdata="$stepdata['medicalResponsesNeeded']" operation="eq"--}}
{{--                        comparator="0" icon="clipboard-medical" :is-large="$isLarge"--}}
{{--                        :tooltip="$year->is_brochure ? __('registration.completeabove') : __('registration.notopenyet')">--}}
{{--            Medical Responses--}}
{{--        </x-steps.header>--}}
{{--        <div class="stepper-content">--}}
{{--            @if($isLarge)--}}
{{--                <span>--}}
{{--              All people under 18 years of age must have their parent or guardian fill out medical forms.--}}
{{--          </span>--}}
{{--            @elseif($stepdata['medicalResponsesNeeded'] != null)--}}
{{--                <span class="small">--}}
{{--                  {{ $stepdata['medicalResponsesNeeded'] . ' response' . ($stepdata['medicalResponsesNeeded']!=1 ? 's' : '') }}--}}
{{--                    needed--}}
{{--                </span>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </li>--}}
</ul>
