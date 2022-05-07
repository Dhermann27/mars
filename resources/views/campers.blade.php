@extends('layouts.app')

@section('css')
    <style>
        div.tooltip-inner {
            max-width: 500px !important;
            text-align: left;
        }
    </style>
@endsection

@section('title')
    Camper Information
@endsection

@section('heading')
    This page can show you all individual information about the campers in your family, both attending this year
    and returning soon.
@endsection

@section('content')
    @include('includes.steps')
    <div class="container">
        <form id="camperinfo" class="form-horizontal" role="form" method="POST" action="{{ route('campers.store') }}">
            @include('includes.flash')

            <ul class="nav nav-tabs" role="tablist">
                @foreach($campers as $camper)
                    <li role="presentation" class="nav-item ml-2">
                        <a href="#tab-{{ $camper->id }}" aria-controls="{{ $camper->id }}" role="tab"
                           class="nav-link{!! $loop->first ? ' active' : '' !!}" data-toggle="tab">
                            {{ old('firstname.' . $loop->index, $camper->firstname) }}
                        </a>
                    </li>
                @endforeach
                @cannot('readonly')
                    <li>
                        <a id="newcamper" class="nav-link btn-secondary ml-2" href="#" role="tab">Create New Camper
                            <i class="far fa-plus"></i></a>
                    </li>
                @endif
            </ul>

            <fieldset @can('readonly') disabled @endif>
                <div class="tab-content">
                    @foreach($campers as $camper)
                        @include('includes.camper', ['camper' => $camper, 'looper' => $loop->index])
                    @endforeach
                </div>
                @cannot('readonly')
                    @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes <i class="fal fa-save ml-2"></i>']])
                @endif
            </fieldset>
        </form>
    </div>
    <div id="empty" class="d-none">
        @include('includes.camper', ['camper' => $empty, 'looper' => 999])
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        $(function () {

            bind($("form#camperinfo"));

            $("#newcamper").on('click', function (e) {
                e.preventDefault();
                var camperCount = $(".tab-content div.tab-pane").length;
                $(this).closest('li').before('<li role="presentation" class="nav-item ml-2"><a href="#tab-' + camperCount + '" class="nav-link" data-toggle="tab">New Camper</a></li>');
                var emptycamper = $("div#empty .tab-pane");
                var empty = emptycamper.clone(false).attr("id", "tab-" + camperCount);
                empty.find("input[name='id[]']").val(camperCount);
                empty.find("input, select").each(function () {
                    $(this).attr("id", $(this).attr("id").replace(/\d+$/, camperCount));
                });
                empty.find("label").each(function () {
                    $(this).attr("for", $(this).attr("for").replace(/\d+$/, camperCount));
                });
                $("form#camperinfo .tab-content").append(empty);
                $('.nav-tabs a[href="#tab-' + camperCount + '"]').tab('show');
                bind(empty);
            });

            $('button:submit').on('click', function (e) {
                e.preventDefault();
                var form = $("#camperinfo");
                $(this).val("Saving").removeClass("btn-primary btn-danger").prop("disabled", true)
                    .find("[data-fa-i2svg]").toggleClass('fa-save')
                    .toggleClass('fa-spinner-third').toggleClass('fa-spin');
                $(".has-danger").removeClass("has-danger");
                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").remove();
                $("div.alert").remove();
                var count = form.find('select.days option[value!="0"]:selected').length;
                if (!confirm("You have indicated that " + count + " camper" + (count > 1 ? "s are" : " is") + " attending in {{ $year->year }}. Is this correct?")) {
                    $(this).val("Resubmit").addClass("btn-danger").prop("disabled", false);
                    return false;
                }
                $.ajax({
                    url: form.attr("action"),
                    type: 'post',
                    data: form.serialize(),
                    success: function (data) {
                        $(".nav-tabs").before("<a href='/payment'><div class='alert alert-success' style='animation: shadow-pulse 1s infinite;'>" + data + "<i class='fa fa-chevron-right fa-2x float-end pb-1'></i></div></a>");
                        $("button:submit").val("Saved").addClass("btn-success").prop("disabled", false)
                            .find("[data-fa-i2svg]").toggleClass('fa-spinner-third').toggleClass('fa-spin')
                            .toggleClass('fa-check');
                        $('html,body').animate({
                            scrollTop: 0
                        }, 700);
                    },
                    error: function (data) {
                        if (data.status === 500) {
                            $(".nav-tabs").before("<div class='alert alert-danger'>Unknown error occurred. Please use the Contact Us form to ask for assistance and include the approximate time you received this message.</div>");
                        } else {
                            var errorCount = data !== undefined ? Object.keys(data.responseJSON.errors).length : '';
                            $.each(data.responseJSON.errors, function (k, v) {
                                var group = $("#" + k.replace(".", "-")).parents(".form-group").addClass("has-danger");
                                group.find("select,input").addClass('is-invalid');
                                group.find("div:first").append("<span class=\"invalid-feedback\"><strong>" + this[0] + "</strong></span>");
                            });
                            $("span.invalid-feedback").show();
                            $(".nav-tabs").before("<div class='alert alert-danger'>You have " + errorCount + " error(s) in your form. Please adjust your entries and resubmit.</div>");
                            $('.nav-tabs a[href="#' + $("span.invalid-feedback:first").parents('div.tab-pane').attr('id') + '"]').tab('show');
                        }
                        $("button:submit").val("Resubmit").addClass("btn-danger").prop("disabled", false)
                            .find("[data-fa-i2svg]").toggleClass('fa-spinner-third').toggleClass('fa-spin')
                            .toggleClass('fa-save');
                        $('html,body').animate({
                            scrollTop: 0
                        }, 700);
                    }
                });
            });
        });

        function replaceMarkup(data, term) {
            return data.replace(new RegExp(term, "i"), "<strong>$&</strong>");
        }

        function templateReschurch(data) {
            if (!data.id) return data.text;
            return replaceMarkup(data.name, data.term) + ' (' + replaceMarkup(data.city, data.term) + ', ' + data.province.code + ')';
        }

        function templateSelchurch(data) {
            if (!data.name) return data.text;
            return data.name + ' (' + data.city + ', ' + data.province.code + ')';
        }

        function bind(obj) {
            obj.find("button#quickme").on("click", function (e) {
                e.preventDefault();
                $("select.days").val('6');
            });
            obj.find('[data-toggle="tooltip"]').tooltip();
            obj.find("input[name='firstname[]']").change(function () {
                $('a[href="#' + $(this).parents('div.tab-pane').attr('id') + '"]').text($(this).val());
            });
            obj.find(".select-program").change(function () {
                var tab = $(this).parents('div.tab-pane');
                var days = tab.find("select.days");
                if (days.val() === "0") {
                    $(this).next(".alert").removeClass("d-none");
                } else {
                    $(this).next(".alert").addClass("d-none");
                }
            });
            obj.find("select.churchlist").select2({
                ajax: {
                    url: '/data/churchlist',
                    dataType: 'json',
                    quietMillis: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                minimumInputLength: 3,
                placeholder: 'Click here to search...',
                templateResult: templateReschurch,
                templateSelection: templateSelchurch,
                theme: 'bootstrap4'
            });
        }
    </script>
@endsection
