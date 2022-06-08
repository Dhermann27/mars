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
    <x-layouts.register :stepdata="$stepdata" step="2" previous="household" next="payment">
        <div class="display-6 mt-3 border-bottom text-end">Update Details for each Camper</div>
        <form id="camperinfo" class="form-horizontal" role="form" method="POST"
              action="{{ route('camperinfo.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
            @include('includes.flash')

            <x-navtabs :tabs="$campers" option="firstname" active-tab="{{ Session::get('activeTab') ?? 0}}">
                @foreach($campers as $camper)
                    <x-camper :camper="$camper" :looper="$loop->index" :pronouns="$pronouns" :programs="$programs"
                              :foodoptions="$foodoptions" active-tab="{{ Session::get('activeTab') ?? 0}}"/>
                @endforeach
            </x-navtabs>

            @cannot('readonly')
                <x-form-group type="submit" label="Save Changes"/>
            @endif
        </form>
    </x-layouts.register>
@endsection

@section('script')
    <script type="text/javascript">
        let firstnameChange = (event) => {
            tab = document.getElementById('tablink-' + event.target.getAttribute('tabid'));
            tab.value = event.target.value;
            tab.innerText = event.target.value;
            tab.ariaControls = event.target.value;
        };
        let firstnames = document.getElementsByName('firstname[]');
        for(i=0; i<firstnames.length; i++) {
            window.addEvent(firstnames[i], 'change', firstnameChange);
        }

        // function templateReschurch(data) {
        //     if (!data.id) return data.text;
        //     return replaceMarkup(data.name, data.term) + ' (' + replaceMarkup(data.city, data.term) + ', ' + data.province.code + ')';
        // }
        //
        // function templateSelchurch(data) {
        //     if (!data.name) return data.text;
        //     return data.name + ' (' + data.city + ', ' + data.province.code + ')';
        // }
        //
        // function bind(obj) {
        //     obj.find("select.churchlist").select2({
        //         ajax: {
        //             url: '/data/churchlist',
        //             dataType: 'json',
        //             quietMillis: 250,
        //             processResults: function (data) {
        //                 return {
        //                     results: data
        //                 };
        //             }
        //         },
        //         escapeMarkup: function (markup) {
        //             return markup;
        //         },
        //         minimumInputLength: 3,
        //         placeholder: 'Click here to search...',
        //         templateResult: templateReschurch,
        //         templateSelection: templateSelchurch,
        //         theme: 'bootstrap4'
        //     });
        // }
    </script>
@endsection
