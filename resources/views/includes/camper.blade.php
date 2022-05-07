<div role="tabpanel" class="tab-pane fade{{ $looper == 0 ? ' active show' : '' }}"
     aria-expanded="{{ $looper == 0 ? 'true' : 'false' }}" id="tab-{{ $camper->id }}">
    <p>&nbsp;</p>
    <input type="hidden" id="id-{{ $looper }}" name="id[]" value="{{ $camper->id }}"/>
    <div class="form-group shadow p-3 mb-5 bg-white rounded row @error('days.' . $looper) has-danger @enderror">
        <label for="days-{{ $looper }}" class="col-md-4 control-label">
            @cannot('readonly')
                <button id="quickme" type="button" class="float-end" data-toggle="tooltip"
                        title="@lang('registration.quickcopy')">
                    <i class="far fa-copy"></i></button>
            @else
                <a href="#" class="p-2 float-end" data-toggle="tooltip" data-html="true"
                   title="@lang('registration.attending')"><i class="far fa-info"></i></a>
            @endif
            Attending in {{ $year->year }}?
        </label>

        <div class="col-md-6">
            @can('is-super')
                <select class="form-control days @error('days.' . $looper) has-danger @enderror"
                        id="days-{{ $looper }}" name="days[]">
                    @for($i=6; $i>0; $i--)
                        <option value="{{ $i }}"
                            @selected($i == old('days.' . $looper, $camper->currentdays))>
                            {{ $i }} nights
                        </option>
                    @endfor
                    <option value="0"{{ $camper->currentdays == 0 ? ' selected' : '' }}>
                        Not Attending
                    </option>
                </select>
            @else
                <select
                    class="form-control days @error('days.' . $looper) has-danger @enderror @can('readonly') disabled @endif"
                    id="days-{{ $looper }}" name="days[]">
                    <option value="{{ $camper->currentdays > 0 ? $camper->currentdays : '6' }}">
                        Yes
                    </option>
                    <option value="0"{{ $camper->currentdays <= 0 ? ' selected' : '' }}>
                        No
                    </option>
                </select>
            @endif

            @error('days.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('pronoun_id.' . $looper) has-danger @enderror">
        <label for="pronoun_id-{{ $looper }}" class="col-md-4 control-label">
            <a href="#" class="p-2 float-end" data-toggle="tooltip" data-html="true"
               title="@lang('registration.pronoun')"><i class="far fa-info"></i></a>
            Gender Pronoun(s)
        </label>

        <div class="col-md-6">
            <select
                class="form-control @error('pronoun_id.' . $looper) has-danger @enderror @can('readonly') disabled @endif"
                id="pronoun_id-{{ $looper }}" name="pronoun_id[]">
                <option value="0">Choose pronoun(s)</option>
                @foreach($pronouns as $pronoun)
                    <option value="{{ $pronoun->id }}"
                        {{ $pronoun->id == old('pronoun_id.' . $looper, $camper->pronoun_id) ? ' selected' : '' }}>
                        {{ $pronoun->name }}
                    </option>
                @endforeach
            </select>

            @error('pronoun_id.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('firstname.' . $looper) has-danger @enderror">
        <label for="firstname-{{ $looper }}" class="col-md-4 control-label">First
            Name</label>
        <div class="col-md-6">
            <input id="firstname-{{ $looper }}"
                   class="form-control @error('firstname.' . $looper) has-danger @enderror"
                   name="firstname[]" value="{{ old('firstname.' . $looper, $camper->firstname) }}">

            @error('firstname.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('lastname.' . $looper) has-danger @enderror">
        <label for="lastname-{{ $looper }}" class="col-md-4 control-label">Last
            Name</label>
        <div class="col-md-6">
            <input id="lastname-{{ $looper }}"
                   class="form-control @error('lastname.' . $looper) has-danger @enderror"
                   name="lastname[]" value="{{ old('lastname.' . $looper, $camper->lastname) }}">

            @error('lastname.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('email.' . $looper) has-danger @enderror">
        <label for="email-{{ $looper }}" class="col-md-4 control-label">Email</label>
        <div class="col-md-6">
            <div class="input-group">
                <input id="email-{{ $looper }}"
                       class="form-control @error('email.' . $looper) has-danger @enderror"
                       name="email[]" value="{{ old('email.' . $looper, $camper->email) }}"
                       aria-describedby="email-addon-{{ $looper }}">
                <div class="input-group-append"><span class="input-group-text">@</span></div>
            </div>
            @if($camper->email == Auth::user()->email)
                <span class="alert alert-warning p-0 m-0">
                    Changing this value will also change your muusa.org login.
                </span>
            @endif
            @error('email.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('phonenbr.' . $looper) has-danger @enderror">
        <label for="phonenbr-{{ $looper }}" class="col-md-4 control-label">Phone
            Number</label>
        <div class="col-md-6">
            <div class="input-group">
                <input id="phonenbr-{{ $looper }}" name="phonenbr[]"
                       class="form-control @error('phonenbr.' . $looper) has-danger @enderror"
                       value="{{ old('phonenbr.' . $looper, $camper->phone) }}">
                <div class="input-group-append"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
            </div>

            @error('phonenbr.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('birthdate.' . $looper) has-danger @enderror">
        <label for="birthdate-{{ $looper }}" class="col-md-4 control-label">Birthdate
            (yyyy-mm-dd)</label>
        <div class="col-md-6">
            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"
                 data-date-start-view="decades" data-date-autoclose="true">
                <input id="birthdate-{{ $looper }}" type="text" class="form-control" name="birthdate[]"
                       value="{{ old('birthdate.' . $looper, $camper->birthdate) }}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="far fa-calendar"></i></span>
                </div>
                <div class="input-group-addon">
                </div>
            </div>
            @error('birthdate.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('program_id.' . $looper) has-danger @enderror">
        <label for="program_id-{{ $looper }}" class="col-md-4 control-label">
            Program
        </label>

        <div class="col-md-6">
            <select
                class="form-control select-program @error('program_id.' . $looper) has-danger @enderror @can('readonly') disabled @endif"
                id="program_id-{{ $looper }}" name="program_id[]">
                <option value="0">Choose a program</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}"
                        {{ $program->id == old('program_id.' . $looper,  $camper->program_id) || ($program->id == \App\Enums\Programname::YoungAdult && \App\Enums\Programname::YoungAdultUnderAge == old('program_id.' . $looper,  $camper->program_id)) ? ' selected' : '' }}>
                        {{ str_replace("YEAR", $year->year, $program->title) }}
                    </option>
                @endforeach
            </select>
            <span class="alert alert-warning p-0 m-0 d-none">
                    Changing this value will only save if the camper is attending this year.
            </span>

            @error('program_id.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('roommate.' . $looper) has-danger @enderror">
        <label for="roommate-{{ $looper }}" class="col-md-4 control-label">
            <a href="#" class="p-2 float-end" data-toggle="tooltip" data-html="true"
               title="@lang('registration.roommate')"><i class="far fa-info"></i></a>
            Roommate Preference
        </label>

        <div class="col-md-6">
            <input id="roommate-{{ $looper }}" type="text"
                   class="form-control  @error('roommate.' . $looper) has-danger @enderror"
                   name="roommate[]" autocomplete="off"
                   value="{{ old('roommate.' . $looper, $camper->roommate) }}"
                   placeholder="First and last name of the camper who has agreed to be your roommate."/>

            @error('roommate.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('sponsor.' . $looper) has-danger @enderror">
        <label for="sponsor-{{ $looper }}" class="col-md-4 control-label">
            <a href="#" class="p-2 float-end" data-toggle="tooltip" data-html="true"
               title="@lang('registration.sponsor')"><i class="far fa-info"></i></a>
            Sponsor (if necessary)
        </label>

        <div class="col-md-6">
            <input id="sponsor-{{ $looper }}" type="text"
                   class="form-control  @error('sponsor.' . $looper) has-danger @enderror"
                   name="sponsor[]" autocomplete="off" value="{{ old('sponsor.' . $looper, $camper->sponsor) }}"
                   placeholder="First and last name of the camper who has agreed to be your sponsor.">

            @error('sponsor.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('church_id.' . $looper) has-danger @enderror">
        <label for="church_id-{{ $looper }}" class="col-md-4 control-label">Church Affiliation</label>
        <div class="col-md-6">
            <select id="church_id-{{ $looper }}" name="church_id[]"
                    class="form-control churchlist @error('church_id.' . $looper) has-danger @enderror @can('readonly') disabled @endif">
                @if(isset($camper->church_id))
                    <option value="{{ old('church_id.' . $looper, $camper->church_id) }}" selected="selected">
                        {{ $camper->church->name}} ({{ $camper->church->city }}, {{ $camper->church->province->code }})
                    </option>
                @endif
            </select>

            @error('church_id.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="form-group row @error('is_handicap.' . $looper) has-danger @enderror">
        <label for="is_handicap-{{ $looper }}" class="col-md-8 control-label">Do you require assistance or any
            needs of which the Registrar should be aware?
            <a href="#" class="p-2 float-end" data-toggle="tooltip" data-html="true"
               title="@lang('registration.specialneeds')"><i class="far fa-info"></i></a>
        </label>
        <div class="col-md-2">
            <select
                class="form-control @error('is_handicap.' . $looper) has-danger @enderror @can('readonly') disabled @endif"
                id="is_handicap-{{ $looper }}" name="is_handicap[]">
                <option value="0">No</option>
                <option value="1"{{ $camper->is_handicap == 1 ? ' selected' : '' }}>
                    Yes
                </option>
            </select>

            @error('is_handicap.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div
        class="form-group row @error('foodoption_id.' . $looper) has-danger @enderror @can('readonly') disabled @endif">
        <label for="foodoption_id-{{ $looper }}" class="col-md-8 control-label">What
            option best describes your food restrictions?</label>
        <div class="col-md-2">
            <select class="form-control @error('foodoption_id.' . $looper) has-danger @enderror"
                    id="foodoption_id-{{ $looper }}" name="foodoption_id[]">
                @foreach($foodoptions as $foodoption)
                    <option value="{{ $foodoption->id }}"
                        {{ $foodoption->id == old('foodoption_id.' . $looper, $camper->foodoption_id) ? ' selected' : '' }}>
                        {{ $foodoption->name }}
                    </option>
                @endforeach
            </select>

            @error('foodoption_id.' . $looper)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
</div>
