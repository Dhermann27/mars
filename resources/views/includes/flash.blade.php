@csrf

@if(Session::has('error'))
    <div class="alert alert-danger">
        {!! Session::get('error') !!}
    </div>
@elseif(Session::has('success'))
    <div class="alert alert-success">
        {!! Session::get('success') !!}
    </div>
@elseif(Session::has('warning'))
    <div class="alert alert-warning">
        {!! Session::get('warning') !!}
    </div>
@elseif(count($errors->all())>0)
    <div class="alert alert-danger">
        {{ count($errors->all()) }} error{{ count($errors->all())>1 ? 's were' : ' was' }} found, so changes were not saved.
        Please correct the errors outlined in red.
    </div>
@endif
