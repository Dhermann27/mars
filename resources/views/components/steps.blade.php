<ul {{ $attributes->class(['stepper', 'stepper-vertical' => $isVertical, 'stepper-large' => $isLarge]) }}>
    <li class="stepper-step mt-3">
        <x-steps-header url="camperselect" :stepdata="$stepdata['campersSelected']" operation="gt"
                        comparator="0" icon="users" :is-large="$isLarge">
            Camper Selection
        </x-steps-header>
        <div class="stepper-content">
          <span>
                Enter your names and select which campers are attending this year.
          </span>
        </div>
    </li>
    <li class="stepper-step">
        <x-steps-header url="household" :stepdata="$stepdata['isAddressCurrent']" icon="home" :is-large="$isLarge">
            Billing Address
        </x-steps-header>
        <div class="stepper-content">
          <span>
                Enter your mailing address and let us know if you want to receive e-mail communication or apply for a scholarship.
          </span>
        </div>
    </li>
    <li class="stepper-step">
        <x-steps-header url="campers" :stepdata="$stepdata['isCamperDetail']" icon="user-gear" :is-large="$isLarge">
            Camper Information
        </x-steps-header>
        <div class="stepper-content">
          <span>
                Enter details about the camper(s) in your family.
          </span>
        </div>
    </li>
    <li class="stepper-step">
        <x-steps-header url="payment" :stepdata="$stepdata['amountDueNow']" operation="lte"
                        comparator="0" icon="usd-square" :is-large="$isLarge">
            Account Statement
        </x-steps-header>
        <div class="stepper-content">
          <span>
            See a list of your charges and post a payment via PayPal.
          </span>
        </div>
    </li>
    @if(!$year->is_live)
        <li>
            <hr class="dropdown-divider"/>
        </li>
        <h6 class="dropdown-header">
            Opens {{ $year->brochure_date }}
        </h6>
    @endif
    <li class="stepper-step">
        <x-steps-header url="roomselection" :stepdata="$stepdata['isRoomsSelected']" icon="bed" :is-large="$isLarge">
            Room Selection
        </x-steps-header>
        <div class="stepper-content">
          <span>
              Select a room where you will stay and see who your neighbors might be.
          </span>
        </div>
    </li>
    <li class="stepper-step">
        <x-steps-header url="workshopchoice" :stepdata="$stepdata['workshopsSelected']" operation="gt"
                        comparator="0" icon="rocket" :is-large="$isLarge" is-required="false">
            Workshop Preferences
        </x-steps-header>
        <div class="stepper-content">
          <span>
              Choose which workshops you would like to attend during the week. <strong>Optional.</strong>
          </span>
        </div>
    </li>
    <li class="stepper-step">
        <x-steps-header url="nametag" :stepdata="$stepdata['nametagsCustomized']" operation="gt"
                        comparator="0" icon="id-card" :is-large="$isLarge" is-required="false">
            Nametag Customization
        </x-steps-header>
        <div class="stepper-content">
          <span>
              Customize the nametags that you'll receive during check-in. <strong>Optional.</strong>
          </span>
        </div>
    </li>
    <li class="stepper-step">
        <x-steps-header url="medicalresponse" :stepdata="$stepdata['medicalResponsesNeeded']" operation="eq"
                        comparator="0" icon="envelope" :is-large="$isLarge">
            Medical Responses
        </x-steps-header>
        <div class="stepper-content">
          <span>
              All people under 18 years of age must have their parent or guardian fill out medical forms.
          </span>
        </div>
    </li>
</ul>
