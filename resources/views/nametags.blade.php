@extends('layouts.app')

@section('css')
    <link
        href="https://fonts.googleapis.com/css?family=Bangers|Fredericka+the+Great|Great+Vibes|Indie+Flower|Mystery+Quest"
        rel="stylesheet">
    <style>
        div.label {
            border: 2px dashed black;
        }
    </style>
@endsection

@section('title')
    Customize Nametags
@endsection

@section('heading')
    You'll be issued a nametag at the start of the week so you can introduce yourself to others quickly: alter it to your preference.
@endsection

@section('content')
    <x-layouts.register :stepdata="$stepdata" step="7" previous="workshopchoice" next="medicalresponses">
        <div class="display-6 mt-3 border-bottom text-end">Choose a look</div>
        <form id="nametagform" class="form-horizontal" role="form" method="POST" action="{{ url('/nametag') .
                 (isset($readonly) && $readonly === false ? '/f/' . $campers->first()->family_id : '')}}">
            @include('includes.flash')

            <x-navtabs :tabs="$campers" option="firstname">
                @foreach($campers as $camper)
                    <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $camper->id }}"
                         role="tabpanel">
                        <p>&nbsp;</p>
                        {{--                        <button id="copyAnswers-{{ $camper->id }}" class="btn btn-primary btn-shadow float-end"--}}
                        {{--                                type="button" onclick="return copyAnswers(event);">--}}
                        {{--                            <i class="fas fa-copy fa-2x fa-pull-left"></i> Copy these preferences to<br/> all family--}}
                        {{--                            members--}}
                        {{--                        </button>--}}
                        <div class="row mb-3 col-md-6 offset-md-3">
                            @include('includes.nametag', ['camper' => $camper])
                        </div>

                        <x-form-group type="select" name="name-{{ $camper->id }}" label="Name Format"
                                      :formobject="$camper->yearattending">
                            <option value="2"
                                    @selected(old('name-' . $camper->id, $camper->yearattending->name) == '2')
                                    data-content="{{ $camper->firstname }} {{ $camper->lastname }}||">
                                First Last
                            </option>
                            <option value="1"
                                    @selected(old('name-' . $camper->id, $camper->yearattending->name) == '1')
                                    data-content="{{ $camper->firstname }}||{{ $camper->lastname }}">
                                First then Last (on next line)
                            </option>
                            <option value="4"
                                    @selected(old('name-' . $camper->id, $camper->yearattending->name) == '4')
                                    data-content="{{ $camper->firstname }}||">
                                First Only
                            </option>
                        </x-form-group>

                        <div class="row align-self-center my-3">
                            <div class="container-md col-lg-6">
                                <label class="form-label" for="namesize-{{ $camper->id }}">Name Size</label>
                                @if(config('app.name') == 'MUUSADusk')
                                    <input
                                        value="{{ old('namesize-' . $camper->id, $camper->yearattending->namesize) }}"
                                        id="namesize-{{ $camper->id }}" name="namesize-{{ $camper->id }}"/>
                                @else
                                    <div class="range">
                                        <input type="range" class="form-range" min="1" max="5" step="1"
                                               value="{{ old('namesize-' . $camper->id, $camper->yearattending->namesize) }}"
                                               id="namesize-{{ $camper->id }}" name="namesize-{{ $camper->id }}"/>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row align-self-center mb-3">
                            <div class="container-md col-lg-6">
                                <div class="form-check">
                                    <input type="hidden" value="1" name="pronoun-{{ $camper->id }}">
                                    <input
                                        class="form-check-input {{ $errors->has('pronoun-' . $camper->id) ? 'is-invalid' : '' }}"
                                        type="checkbox" id="pronoun-{{ $camper->id }}" name="pronoun-{{ $camper->id }}"
                                        value="2" @checked(old('pronoun-' . $camper->id, $camper->yearattending->pronoun) == "2") />
                                    <label class="form-check-label" for="pronoun-{{ $camper->id }}">
                                        Check this box to display pronoun(s) on the nametag
                                    </label>
                                    @error('pronoun-' . $camper->id)
                                    <span class="muusa-invalid-feedback"
                                          role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @for($i=1; $i<5; $i++)
                            <x-form-group type="select" :name="'line' . $i . '-' . $camper->id"
                                          label="Line #{{ $i }}" :formobject="$camper->yearattending">
                                <option value="5"
                                        @selected(old('line' . $i . '-' . $camper->id, $camper->yearattending["line" . $i]) == '5')
                                        data-content="">
                                    Nothing
                                </option>
                                <option value="2"
                                        @selected(old('line' . $i . '-' . $camper->id, $camper->yearattending["line" . $i]) == '2')
                                        data-content="{{ $camper->family->city . ", " . $camper->family->province->code }}">
                                    Home (City, State)
                                </option>
                                @if($camper->church)
                                    <option value="1"
                                            @selected(old('line' . $i . '-' . $camper->id, $camper->yearattending["line" . $i]) == '1')
                                            data-content="{{ $camper->church->name }}">
                                        Congregation Name
                                    </option>
                                @endif
                                @if(count($camper->yearattending->staffpositions) > 0)
                                    <option value="3"
                                            @selected(old('line' . $i . '-' . $camper->id, $camper->yearattending["line" . $i]) == '3')
                                            data-content="Your PC Position">
                                        Planning Council Position
                                    </option>
                                @endif
                                @if($camper->yearattending->firsttime)
                                    <option value="4"
                                            @selected(old('line' . $i . '-' . $camper->id, $camper->yearattending["line" . $i]) == '4')
                                            data-content="First-time Camper">
                                        First-time Camper (Status)
                                    </option>
                                @endif
                            </x-form-group>
                        @endfor

                        <x-form-group type="select" name="font-{{ $camper->id }}" label="Font" class="fonts"
                                      :formobject="$camper->yearattending">
                            <option
                                value="1" @selected(old('font-' . $camper->id, $camper->yearattending->font) == 1)>
                                Normal
                            </option>
                            <option
                                value="2" @selected(old('font-' . $camper->id, $camper->yearattending->font) == 2)>
                                Indie Flower
                            </option>
                            <option
                                value="3" @selected(old('font-' . $camper->id, $camper->yearattending->font) == 3)>
                                Fredericka the Great
                            </option>
                            <option
                                value="4" @selected(old('font-' . $camper->id, $camper->yearattending->font) == 4)>
                                Mystery Quest
                            </option>
                            <option
                                value="5" @selected(old('font-' . $camper->id, $camper->yearattending->font) == 5)>
                                Great Vibes
                            </option>
                            <option
                                value="6" @selected(old('font-' . $camper->id, $camper->yearattending->font) == 6)>
                                Bangers
                            </option>
                            <option
                                value="7" @selected(old('font-' . $camper->id, $camper->yearattending->font) == 7)>
                                Comic Sans MS
                            </option>
                        </x-form-group>


                        <div class="row align-self-center mb-3">
                            <div class="container-md col-lg-6">
                                <div class="form-check">
                                    <input type="hidden" value="2" name="fontapply-{{ $camper->id }}">
                                    <input
                                        class="form-check-input {{ $errors->has('fontapply-' . $camper->id) ? 'is-invalid' : '' }}"
                                        type="checkbox" id="fontapply-{{ $camper->id }}"
                                        name="fontapply-{{ $camper->id }}"
                                        value="1" @checked(old('fontapply-' . $camper->id, $camper->yearattending->fontapply) == "1") />
                                    <label class="form-check-label" for="fontapply-{{ $camper->id }}">
                                        Check this box only apply the font to the name
                                    </label>
                                    @error('fontapply-' . $camper->id)
                                    <span class="muusa-invalid-feedback"
                                          role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </x-navtabs>
            @if($stepdata["amountDueNow"] <= 0)
                @cannot('readonly')
                    <x-form-group type="submit" label="Save Changes"/>
                @endif
            @endif
        </form>
    </x-layouts.register>
@endsection

@section('script')
    <script type="text/javascript">
        const redraw = function (e, tab = null) {
            const id = tab != null ? tab.id.split('-')[1] : e.target.id.split('-')[1];
            const pane = document.getElementById('tab-' + id);
            const font = document.getElementById('font-' + id);
            const fontFam = font.selectedIndex > 0 ? font[font.selectedIndex].text.trim() : 'Jost';
            if (document.getElementById("pronoun-" + id).checked) {
                window.removeClass(pane.querySelector('.pronoun'), 'd-none');
            } else {
                window.addClass(pane.querySelector('.pronoun'), 'd-none');
            }
            const nametagName = document.getElementById('name-' + id);
            const names = nametagName[nametagName.selectedIndex].getAttribute("data-content").split("||");
            const firstname = pane.querySelector(".name");
            firstname.innerText = names[0];
            firstname.style.fontSize = (parseInt(document.getElementById("namesize-" + id).value, 10) * .5 + .8) + "em";
            firstname.style.fontFamily = fontFam;
            pane.querySelector(".surname").innerText = names[1];
            for (let i = 1; i < 5; i++) {
                const nametagLine = document.getElementById('line' + i + "-" + id);
                pane.querySelector(".line" + i).innerText = nametagLine[nametagLine.selectedIndex].getAttribute("data-content");
            }
            if (document.getElementById("fontapply-" + id).checked) {
                pane.querySelector(".label").style.fontFamily = 'Jost';
            } else {
                pane.querySelector(".label").style.fontFamily = fontFam;
            }
            return true;
        }

        const copyAnswers = function (e) {
            e.preventDefault();
            const id = e.target.id.split('-')[1];
            const references = document.getElementById('tab-' + id).querySelectorAll('select, input[type=checkbox]');
            const allpanes = document.querySelectorAll('div.tab-pane:not(#tab-' + id + ')');
            for (let i = 0; i < allpanes.length; i++) {
                const elements = allpanes[i].querySelectorAll('select, input');
                for (let j = 0; j < elements.length; j++) {
                    if (elements[j].nodeName === "SELECT") {
                        window.setSelect('#' + elements[j].id, references[j].value);
                    } else {
                        elements[j].checked = references[j].checked;
                    }
                }
                redraw(e, allpanes[i]);
            }
            return false;
        }

        const inputs = document.querySelectorAll('#nametagform input, #nametagform select');
        for (let i = 0; i < inputs.length; i++) {
            window.addEvent(inputs[i], 'change', redraw);
        }


    </script>
@endsection
