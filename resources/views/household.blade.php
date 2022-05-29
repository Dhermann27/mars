@extends('layouts.app')

@section('title')
    Billing Information
@endsection

@section('heading')
    This page will show all communal information relating to your entire family.
@endsection

@section('content')
    <div class="d-flex bg-light mb-3">
        <div class="col-md-3 border-right d-none d-md-flex">
            <x-steps :stepdata="$stepdata" :is-large="false"/>
        </div>
        <div class="offset-md-1 col-md-6 p-3">
            <div class="display-6 mt-3 border-bottom text-end">Who is attending in {{ $year->year }}?</div>
            <form id="household" class="form-horizontal" role="form" method="POST"
                  action="{{ route('household.store', ['id' => session()->has('camper') ? session()->get('camper')->id : null]) }}">
                @include('includes.flash')

                <x-form-group label="Address Line #1" name="address1"/>

                <x-form-group label="Address Line #2" name="address2"/>

                <x-form-group label="City" name="city"/>

                <x-form-group label="State" name="province_id">
                    <option value="0">Choose a state</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}" @selected(old('province_id'))>{{ $province->name }}</option>
                    @endforeach
                </x-form-group>

                <x-form-group label="Postal Code" name="zipcd"/>

                <x-form-group label="Country" name="country"/>

                @can('is-council')
                    <x-form-group type="checkbox" name="is_address_current" label="Check if the address is current"/>
                @endif

                <x-form-group type="checkbox" name="is_ecomm"
                              label="Check if you would like to receive a paper brochure in the mail"/>

                <x-form-group type="checkbox" name="is_scholar"
                              label="Check if you are applying for a scholarship this year"/>

                @cannot('readonly')
                    <x-form-group type="submit" label="Save Changes"/>
                @endif
            </form>
        </div>
    </div>
@endsection
