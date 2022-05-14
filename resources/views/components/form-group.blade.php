@if($type=='select')
    <div class="mb-3 pb-1">
        <div class="col-md-6 offset-md-3">
            <select {{ $attributes->class(['select' => !$isDusk()]) }} id="{{ $name }}" name="{{ $name }}">
                {{ $slot }}
            </select>
            @error($name)
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror

            <label for="{{ $name }}" class="form-label select-label">
                @if(isset($title))
                    <a href="#" class="p-2 float-end" data-mdb-toggle="tooltip" data-mdb-html="true"
                       title="@lang('registration.' . $title)"><i class="far fa-info"></i></a>
                @endif
                {{ $label }}
            </label>
        </div>
    </div>
@elseif($type=='submit')
    <div class="col-md-6 offset-md-3 my-md-3 text-end">
        <button type="submit" class="btn btn-lg btn-primary py-3 px-4">
            {{ $label }}
        </button>
    </div>
@else
    <div class="mb-3 pb-1">
        <div class="form-outline col-md-6 offset-md-3">
            @if($type=='textarea')
                <textarea id="{{ $name }}" name="{{ $name }}"
                    {{ $attributes->class(['form-control','is-invalid' => $errors->has($name)]) }}>{{ old($name) }}</textarea>
            @else
                <input id="{{ $name }}" name="{{ $name }}" type="text" @if(old($name))) value="{{ old($name) }}" @endif
                {{ $attributes->class(['form-control','is-invalid' => $errors->has($name)]) }}
                @if(isset($readonly)) placeholder="{{ $readonly }}" aria-label="{{ $readonly }}" readonly @endif />
            @endif

            <label for="{{ $name }}" class="form-label">
                {{--            @if(isset($title))--}}
                {{--                <a href="#" class="p-2 float-end" data-mdb-toggle="tooltip" data-mdb-html="true"--}}
                {{--                   title="@lang('registration.' . $title)"><i class="far fa-info"></i></a>--}}
                {{--            @endif--}}
                {{ $label }}
            </label>

            @error($name)
            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
            @enderror

        </div>
    </div>
@endif
