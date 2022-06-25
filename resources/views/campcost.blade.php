@extends('layouts.app')

@section('title')
    Camp Cost Calculator
@endsection

@section('heading')
    Use this tool to easily estimate the cost of your fees for {{ $year->year }}.
@endsection

@section('image')
    url('/images/calculator.jpg')
@endsection

@section('content')
    <div id="campcost" class="m-5">
        <div class="note note-warning text-black m-3">
            Warning: this calculator only provides an estimate of your camp cost and your actual fees
            may vary.
        </div>
        <div class="form-group row pb-lg-3">
            <label for="adults" class="col-md-3">Adults Attending</label>

            <div class="col-md-3">
                <x-numberspinner id="adults"/>
            </div>

            <div class="col-md-4 text-center">
                <div class="btn-group">
                    <input type="radio" checked="checked" class="btn-check" name="adults-housing" id="adults-housing1"
                           autocomplete="off" value="1"/>
                    <label for="adults-housing1" class="btn btn-outline-primary" data-mdb-toggle="tooltip"
                           title="Guestroom, Cabin, or Loft Housing"><i class="fa-solid fa-hotel"></i></label>
                    <input type="radio" class="btn-check" name="adults-housing" id="adults-housing2" value="2"
                           autocomplete="off"/>
                    <label for="adults-housing2" class="btn btn-outline-primary" data-mdb-toggle="tooltip"
                           title="Camp Lakewood Cabin (dorm style) Housing"><i
                            class="fa-solid fa-people-roof"></i></label>
                    <input type="radio" class="btn-check" name="adults-housing" id="adults-housing3" value="3"
                           autocomplete="off"/>
                    <label for="adults-housing3" class="btn btn-outline-primary" data-mdb-toggle="tooltip"
                           title="Tent Camping"><i class="fa-solid fa-campground"></i></label>
                </div>
            </div>
            <div class="col-md-2 text-end" id="adults-fee">$0.00</div>
        </div>
        <div id="single-alert" class="row alert alert-warning" style="display: none;">
            If you plan to have a roommate, but have not yet selected or been assigned a roommate, please note that your
            fees will be half the amount shown in the calculator.
        </div>
        <div class="form-group row pb-lg-3">
            <label for="yas" class="col-md-3 control-label">Young Adults (18-20) Attending</label>

            <div class="col-md-3">
                <x-numberspinner id="yas"/>
            </div>

            <div class="col-md-4 text-center">
                <div class="btn-group">
                    <input type="radio" class="btn-check" name="yas-housing" id="yas-housing2" checked="checked"
                           value="2" autocomplete="off"/>
                    <label for="yas-housing2" class="btn btn-outline-primary" data-mdb-toggle="tooltip"
                           title="Camp Lakewood Cabin (dorm style) Housing"><i
                            class="fa-solid fa-people-roof"></i></label>
                    <input type="radio" class="btn-check" name="yas-housing" id="yas-housing3"
                           value="3" autocomplete="off"/>
                    <label for="yas-housing3" class="btn btn-outline-primary" data-mdb-toggle="tooltip"
                           title="Tent Camping"><i class="fa-solid fa-campground"></i></label>
                </div>
            </div>
            <div class="col-md-2 text-end" id="yas-fee">$0.00</div>
        </div>
        <div class="form-group row pb-lg-3">
            <label for="jrsrs" class="col-md-3 control-label">Jr./Sr. High Schoolers Attending</label>

            <div class="col-md-3">
                <x-numberspinner id="jrsrs"/>
            </div>

            <div class="col-md-4">Burt/Meyer Community Cabins
            </div>
            <div class="col-md-2 text-end" id="jrsrs-fee">$0.00</div>
        </div>
        <div class="form-group row pb-lg-3">
            <label for="children" class="col-md-3 control-label">Children (6 years old or older) Attending</label>

            <div class="col-md-3">
                <x-numberspinner id="children"/>
            </div>

            <div class="col-md-4">Must room with parents</div>
            <div class="col-md-2 text-end" id="children-fee">$0.00</div>
        </div>
        <div class="form-group row pb-lg-3">
            <label for="babies" class="col-md-3 control-label">Children (Up to 5 years old) Attending</label>

            <div class="col-md-3">
                <x-numberspinner id="babies"/>
            </div>

            <div class="col-md-4">Must room with parents</div>
            <div class="col-md-2 text-end" id="babies-fee">$0.00</div>
        </div>
        <div class="row">
            <div class="col-md-12 text-end">
                <p>Amount Due Upon Registration: <span id="deposit">$0.00</span><br/>
                    Amount Due Upon Arrival: <span id="arrival">$0.00</span><br/>
                    <strong>Total Camp Cost</strong>: <span id="total">$0.00</span></p>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Adult (1-4), Burt, Cratty, Lumens, Meyer, YA, YA 18-20
        var guestsuite = [{{ $lodge->implode('rate', ',') }}];
        var tentcamp = [{{ $tent->implode('rate', ',') }}];
        var lakewood = [{{ $lakewood->implode('rate', ',') }}];
        var buttons = document.querySelectorAll('div.number-spinner button');
        for (var i = 0; i < buttons.length; i++) {
            addEvent(buttons[i], 'click', spinnerClick);
        }
        var radios = document.querySelectorAll('#campcost input[type=radio], #campcost input[type=text]');
        for (var i = 0; i < radios.length; i++) {
            addEvent(radios[i], 'change', calcluateCampCost);
        }
    </script>
@endsection
