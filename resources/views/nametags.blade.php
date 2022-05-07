@extends('layouts.app')

@section('css')
    <link
        href="https://fonts.googleapis.com/css?family=Bangers|Fredericka+the+Great|Great+Vibes|Indie+Flower|Mystery+Quest"
        rel="stylesheet">
    <style>
        div.label {
            border: 2px dashed black;
            margin: auto;
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
    @include('includes.steps')
    <div class="container">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/nametag') .
                 (isset($readonly) && $readonly === false ? '/f/' . $campers->first()->family_id : '')}}">
            @include('includes.flash')

            @component('components.navtabs', ['tabs' => $campers, 'id'=> 'id', 'option' => 'firstname'])
                @foreach($campers as $camper)
                    <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $camper->id }}"
                         role="tabpanel">
                        <p>&nbsp;</p>
                        <button class="btn btn-primary btn-shadow copyAnswers float-end">
                            <i class="far fa-copy fa-3x float-left pr-3"></i> Copy preferences to<br/> all family
                            members
                        </button>
                        <div class="row mb-3 col-md-10">
                            @include('includes.nametag', ['camper' => $camper])
                        </div>

                        <div class="form-group row @error($camper->id . '-nametag-name') has-danger @enderror">
                            <label for="{{ $camper->id }}-nametag-name" class="col-md-4 control-label text-md-right">
                                Name Format
                            </label>

                            <div class="col-md-6">
                                <select class="form-control @error($camper->id . '-nametag-name') is-invalid @enderror"
                                        id="{{ $camper->id }}-nametag-name" name="{{ $camper->id }}-nametag-name">
                                    <option value="2"
                                            {{ $camper->yearattending->name == '2' ? ' selected' : '' }}
                                            data-content="{{ $camper->firstname }} {{ $camper->lastname }}||">
                                        First Last
                                    </option>
                                    <option value="1"
                                            {{ $camper->yearattending->name == '1' ? ' selected' : '' }}
                                            data-content="{{ $camper->firstname }}||{{ $camper->lastname }}">
                                        First then Last (on next line)
                                    </option>
                                    <option value="4"
                                            {{ $camper->yearattending->name == '4' ? ' selected' : '' }}
                                            data-content="{{ $camper->firstname }}||">
                                        First Only
                                    </option>
                                </select>

                                @error($camper->id . '-nametag-name'))
                                <span class="invalid-feedback">
                                        <strong>{{ $errors->first($camper->id . '-nametag-name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @include('includes.formgroup', ['type' => 'select', 'label' => 'Name Size',
                        'attribs' => ['name' => $camper->id . '-nametag-namesize'], 'formobject' => $camper->yearattending,
                        'list' => [['id' => '2', 'name' => 'Normal'], ['id' => '3', 'name' => 'Big'],
                            ['id' => '4', 'name' => 'Bigger'], ['id' => '5', 'name' => 'Bigly'],
                            ['id' => '1', 'name' => 'Small']], 'option' => 'name'])

                        @include('includes.formgroup', ['type' => 'select', 'label' => 'Pronoun',
                        'attribs' => ['name' => $camper->id . '-nametag-pronoun'], 'formobject' => $camper->yearattending,
                        'list' => [['id' => '2', 'name' => 'Displayed'], ['id' => '1', 'name' => 'Not Displayed']], 'option' => 'name'])


                        @for($i=1; $i<5; $i++)
                            <div class="form-group row @error($camper->id . '-nametag-line' . $i) has-danger @enderror">
                                <label for="{{ $camper->id }}-nametag-line{{ $i }}"
                                       class="col-md-4 control-label text-md-right">
                                    Line #{{ $i }}
                                </label>

                                <div class="col-md-6">
                                    <select
                                        class="form-control @error($camper->id . '-nametag-line' . $i) is-invalid @enderror"
                                        id="{{ $camper->id }}-nametag-line{{ $i }}"
                                        name="{{ $camper->id }}-nametag-line{{ $i }}">
                                        <option value="2"
                                                {{ $camper->yearattending["line" . $i] == '2' ? ' selected' : '' }}
                                                data-content="{{ $camper->family->city . ", " . $camper->family->province->code }}">
                                            Home (City, State)
                                        </option>
                                        @if($camper->church)
                                            <option value="1"
                                                    {{ $camper->yearattending["line" . $i] == '1' ? ' selected' : '' }}
                                                    data-content="{{ $camper->church->name }}">
                                                Congregation (Name)
                                            </option>
                                        @endif
                                        @if(count($camper->yearattending->staffpositions) > 0)
                                            <option value="3"
                                                    {{ $camper->yearattending["line" . $i] == '3' ? ' selected' : '' }}
                                                    data-content="Your PC Position">
                                                Planning Council Position
                                            </option>
                                        @endif
                                        @if($camper->yearattending->is_firsttime == '0')
                                            <option value="4"
                                                    {{ $camper->yearattending["line" . $i] == '4' ? ' selected' : '' }}
                                                    data-content="First-time Camper">
                                                First-time Camper (Status)
                                            </option>
                                        @endif
                                        <option value="5"
                                                {{ $camper->yearattending["line" . $i] == '5' ? ' selected' : '' }}
                                                data-content="">
                                            Nothing
                                        </option>
                                    </select>

                                    @error($camper->id . '-nametag-line' . $i)
                                    <span class="invalid-feedback">
                                            <strong>{{ $errors->first($camper->id . '-nametag-line' . $i) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endfor

                        @include('includes.formgroup', ['type' => 'select', 'label' => 'Font Application',
                            'attribs' => ['name' => $camper->id . '-nametag-fontapply'], 'formobject' => $camper->yearattending,
                            'list' => [['id' => '1', 'name' => 'Apply Font to Primary Name Only'],
                            ['id' => '2', 'name' => 'Apply Font to All Text']], 'option' => 'name'])

                        @include('includes.formgroup', ['type' => 'select', 'label' => 'Font', 'title' => 'fonts',
                            'attribs' => ['name' => $camper->id . '-nametag-font'], 'formobject' => $camper->yearattending,
                            'list' => [['id' => '1', 'name' => 'Lato'], ['id' => '2', 'name' => 'Indie Flower'],
                            ['id' => '3', 'name' => 'Fredericka the Great'], ['id' => '4', 'name' => 'Mystery Quest'],
                            ['id' => '5', 'name' => 'Great Vibes'], ['id' => '6', 'name' => 'Bangers'],
                            ['id' => '7', 'name' => 'Comic Sans MS']], 'option' => 'name'])

                    </div>
                @endforeach
            @endcomponent
            @if(!isset($readonly) || $readonly === false)
                @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes']])
            @endif
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        $(function () {
            @if(count($errors))
            $('.nav-tabs a[href="#' + $("span.invalid-feedback").first().parents('div.tab-pane').attr('id') + '"]').tab('show');
            @endif

            $("[data-toggle='tooltip']").tooltip({
                content: function () {
                    return this.getAttribute("title");
                }
            });

            $(".copyAnswers").on('click', function () {
                var mytab = $(this).parents(".tab-pane");
                var alltabs = mytab.parents(".tab-content").find(".tab-pane");
                var elements = mytab.find("select");
                alltabs.each(function () {
                    $(this).find("select").each(function (index) {
                        if (($(this).find("option[value=" + elements[index].value + "]").length !== 0)) {
                            $(this).val(elements[index].value);
                        } else {
                            $(this).val("5");
                        }
                    });
                    redraw($(this));
                });
                return false;
            });

            $("form select").on('change', function () {
                redraw($(this).parents(".tab-pane"));
            });

            @if(isset($readonly) && $readonly === true)
            $("input:not(#camper), select").prop("disabled", "true");
            @endif
        });

        function redraw(obj) {
            var id = obj.attr("id").split('-')[1];
            var font = $("#" + id + "-nametag-font option:selected").text();
            $("#" + id + "-nametag-pronoun").val() === '1' ? obj.find(".pronoun").hide() : obj.find(".pronoun").show();
            var names = $("#" + id + "-nametag-name option:selected").attr("data-content").split("||");
            obj.find(".name").removeClass().addClass("name").text(names[0]).attr("style", "font-size: " + (parseInt($("#" + id + "-nametag-namesize").val(), 10) + 1) + "em; font-family: '" + font + "';");
            obj.find(".surname").text(names[1]);
            for (var i = 1; i < 5; i++) {
                obj.find(".line" + i).text($("#" + id + "-nametag-line" + i + " option:selected").attr("data-content"));
            }
            obj.find(".label").removeClass().addClass("label");
            if ($("#" + id + "-nametag-fontapply").val() === '2') {
                obj.find(".label").attr("style", "font-family: '" + font + "';");
            } else {
                obj.find(".label div:not(.name)").attr("style", "font-family: 'Krub';");
            }
        }

    </script>
@endsection
