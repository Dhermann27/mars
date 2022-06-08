<div role="tabpanel" {{ $attributes->class(['tab-pane', 'fade', 'active' => $looper == $activeTab, 'show' => $looper == $activeTab,
 'not-attending' => count($camper->yearsattending) == 0]) }}
aria-expanded="{{ $looper == 0 ? 'true' : 'false' }}" id="tab-{{ $camper->id }}">
    <x-form-group type="hidden" name="id[]" errorKey="id.{{ $looper }}" value="{{ $camper->id }}"/>

    @can('is-super')
        <x-form-group label="Days Attending" name="days[]" errorKey="days.{{ $looper }}" is-adminonly="true"
                      class="days-mask" value="{{ $camper->yearsattending[0]->days ?? 0}}"/>
    @endcan

    <x-form-group label="Pronouns" name="pronoun_id[]" errorKey="pronoun_id.{{ $looper }}" type="select">
        <x-slot:tooltip>{!! __('registration.pronoun') !!}</x-slot:tooltip>
        <option value="0">Choose pronouns</option>
        @foreach($pronouns as $pronoun)
            <option value="{{ $pronoun->id }}"
                @selected($pronoun->id == old('pronoun_id.' . $looper, $camper->pronoun_id))>
                {{ $pronoun->name }}
            </option>
        @endforeach
    </x-form-group>

    <x-form-group label="First Name" name="firstname[]" errorKey="firstname.{{ $looper }}" :formobject="$camper"
                  :tabId="$camper->id"/>

    <x-form-group label="Last Name" name="lastname[]" errorKey="lastname.{{ $looper }}" :formobject="$camper"/>

    <x-form-group label="Email" name="email[]" errorKey="email.{{ $looper }}" :formobject="$camper">
        <span class="alert alert-warning p-0 m-0 small">
            Changing this value will also change your muusa.org login.
        </span>
    </x-form-group>

    <x-form-group label="Phone Number <i class='fa-duotone fa-flag-usa'></i>"
                  name="phonenbr[]" errorKey="phonenbr.{{ $looper }}" class="phone-mask" :formobject="$camper"/>

    <div class="row align-self-center mb-3">
        <div class="container-md col-lg-6">
            <div class="form-outline datepicker" data-mdb-inline="true" data-mdb-format="yyyy-mm-dd">
                <input id="birthdate-{{ $looper }}" name="birthdate[]" errorKey="birthdate.{{ $looper }}"
                       data-mdb-toggle="datepicker"
                       class="form-control @error('birthdate.' . $looper) is-invalid @enderror"
                       value="{{ old('birthdate.' . $looper, $camper->birthdate) }}" placeholder="yyyy-mm-dd"
                       @can('readonly') aria-label="Birthdate" readonly @endif />
                <label for="birthdate-{{ $looper }}" class="form-label">Birthdate</label>
                <button class="datepicker-toggle-button" data-mdb-toggle="datepicker">
                    <i class="fas fa-calendar datepicker-toggle-icon me-1"></i>
                </button>
                @error('birthdate.' . $looper)
                <span class="muusa-invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    @if(count($camper->yearsattending) == 1)
        <x-form-group label="Program" name="program_id[]" errorKey="program_id.{{ $looper }}" type="select">
            <option value="0">Choose a program</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}"
                        @selected($program->id == old('program_id.' . $looper,  $camper->yearsattending[0]->program_id) || ($program->id == \App\Enums\Programname::YoungAdult && \App\Enums\Programname::YoungAdultUnderAge == old('program_id.' . $looper,  ($camper->yearsattending[0]->program_id ?? '0'))))
                        @isset($program->subtitle) data-mdb-secondary-text="{{ str_replace("YEAR", $year->year, $program->subtitle) }}" @endisset>
                    {{ $program->title }}
                </option>
            @endforeach
        </x-form-group>
    @else
        <x-form-group type="hidden" name="program_id[]" value="{{ \App\Enums\Programname::Adult }}"/>
    @endif

    <x-form-group label=" Roommate Name" name="roommate[]" errorKey="roommate.{{ $looper }}"
                  placeholder="First and last name" :formobject="$camper">
        <x-slot:tooltip>{{ __('registration.roommate') }}</x-slot:tooltip>
    </x-form-group>

    <x-form-group label="Sponsor Name" name="sponsor[]" errorKey="sponsor.{{ $looper }}"
                  placeholder="First and last name" :formobject="$camper">
        <x-slot:tooltip>{{ __('registration.sponsor') }}</x-slot:tooltip>
    </x-form-group>

    <div class="row align-self-center mb-3">
        <div class="container-md col-lg-6">
            <div class="form-outline autocomplete">
                <input id="churchname-{{ $looper }}" name="churchname[]" type="text"
                       class="form-control church-search @error('churchid.' . $looper) is-invalid @enderror"
                       value="{{ old('churchname.' . $looper, $camper->getChurchName()) }}"
                       placeholder="Begin typing your UU church name or city"
                       @can('readonly') aria-label="Church Affilation" readonly @endif />
                <label for="churchname-{{ $looper }}" class="form-label">Church Affilation</label>
                <input id="churchid-{{ $looper }}" name="churchid[]" type="hidden"
                       class="autocomplete-custom-content"
                       value="{{ old('churchid.' . $looper, $camper->church_id ) }}"/>
                @error('churchid.' . $looper)
                <span class="muusa-invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    <x-form-group label="Do you require assistance or have any needs of which the Registrar should be aware?"
                  name="is_handicap[]" errorKey="is_handicap.{{ $looper }}" type="checkbox" :formobject="$camper"/>

    <x-form-group label="Food Restriction" name="foodoption_id[]" errorKey="foodoption_id.{{ $looper }}" type="select">
        @foreach($foodoptions as $foodoption)
            <option value="{{ $foodoption->id }}"
                @selected($foodoption->id == old('foodoption_id.' . $looper, $camper->foodoption_id))>
                {{ $foodoption->name }}
            </option>
        @endforeach
    </x-form-group>
</div>
