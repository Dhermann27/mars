@php
    if (!isset($formobject)) {
        $old = old($name);
    } else {
        $names = explode('-', $name);
        $old = old($name, $formobject[$names[count($names)-1]]);
    }
@endphp
<div class="form-group row">
    <label for="{{ $name }}" class="control-label col-md-8">{{ $label }}</label>

    <div class="col-md-2">
        <select id="{{ $name }}" name="{{ $name }}" class="form-control @can('readonly') disabled @endif">
            @foreach($list as $item)
                <option value="{{ $item["id"] }}" @selected($old == $item["id"])>
                    {{ $item["option"] }}
                </option>
            @endforeach
        </select>
    </div>
</div>
