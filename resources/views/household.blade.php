@extends('layouts.app')

@section('title')
    Billing Information
@endsection

@section('heading')
    This page will show all communal information relating to your entire family.
@endsection

@section('content')
    @include('includes.steps')
    <div class="container">
        <form id="household" class="form-horizontal" role="form" method="POST"
              action="{{ route('household.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
            @include('includes.flash')

            <fieldset @can('readonly') disabled @endif>
                @include('includes.formgroup', ['label' => 'Address Line #1', 'attribs' => ['name' => 'address1']])

                @include('includes.formgroup', ['label' => 'Address Line #2', 'attribs' => ['name' => 'address2']])

                @include('includes.formgroup', ['label' => 'City', 'attribs' => ['name' => 'city']])

                @include('includes.formgroup', ['type' => 'select', 'label' => 'State',
                    'attribs' => ['name' => 'province_id'], 'default' => 'Choose a state', 'list' => $provinces,
                    'option' => 'name'])

                @include('includes.formgroup', ['label' => 'Postcal Code', 'attribs' => ['name' => 'zipcd',
                        'placeholder' => 'Postal Code']])

                @include('includes.formgroup', ['label' => 'Country', 'attribs' => ['name' => 'country',
                    'placeholder' => 'USA']])

                @can('is-council')
                    @include('includes.question', ['name' => 'is_address_current',
                        'label' => 'Please indicate if the address we have is current.',
                        'list' => [['id' => '1', 'option' => 'Yes, mail to this address'],
                                ['id' => '0', 'option' => 'No, do not use this address until it is updated']]])
                @endif

                @include('includes.question', ['name' => 'is_ecomm',
                    'label' => 'Please indicate if you would like to receive a paper brochure in the mail.',
                    'list' => [['id' => '0', 'option' => 'Yes, please mail me a brochure'],
                        ['id' => '1', 'option' => 'No, do not mail me anything']]])

                @include('includes.question', ['name' => 'is_scholar',
                    'label' => 'Please indicate if you will be applying for a scholarship this year.',
                    'list' => [['id' => '0', 'option' => 'No'],
                            ['id' => '1', 'option' => 'Yes, I will be completing the separate process']]])
            </fieldset>

            @cannot('readonly')
                @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes']])
            @endif
        </form>
    </div>
@endsection
