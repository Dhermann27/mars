@extends('layouts.app')

@section('title')
    Billing Information
@endsection

@section('heading')
    This page will show all communal information relating to your entire family.
@endsection

@section('content')
    <x-layouts.register :stepdata="$stepdata" step="2" previous="camperselect" next="camperinfo">
        <div class="display-6 mt-3 border-bottom text-end mb-3">Mailing Address</div>
        <form id="household" class="form-horizontal" role="form" method="POST"
              action="{{ route('household.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
            @include('includes.flash')
            <x-form-group label="Address Line #1" name="address1" :formobject="$family"/>

            <x-form-group label="Address Line #2" name="address2" :formobject="$family"/>

            <x-form-group label="City" name="city" :formobject="$family"/>

            <x-form-group label="State" name="province_id" type="select" :formobject="$family">
                <option value="0">Choose a state</option>
                @foreach($provinces as $province)
                    <option value="{{ $province->id }}"
                        @selected($province->id == old('province->id', $family->province_id))>
                        {{ $province->name }}
                    </option>
                @endforeach
            </x-form-group>

            <x-form-group label="Postal Code" name="zipcd" :formobject="$family"/>

            <x-form-group label="Country" name="country" :formobject="$family"/>

            @can('is-council')
                <x-form-group label="Check if the address is current" name="is_address_current"
                              type="checkbox" :formobject="$family" is-adminonly="true"/>
            @endif

            <x-form-group label="Check if you would like to receive only emails from us, uncheck to receive a paper brochure in the mail"
                          name="is_ecomm" type="checkbox" :formobject="$family"/>

            <x-form-group label="Check if you are applying for a scholarship this year"
                          name="is_scholar" type="checkbox" :formobject="$family"/>

            @cannot('readonly')
                <x-form-group type="submit" label="Save Changes"/>
        @endif
    </x-layouts.register>
@endsection
