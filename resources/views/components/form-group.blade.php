@if($type=='checkbox')
    <div class="mb-3 pb-1 d-flex justify-content-center">
        <div class="w-xs-90 w-lg-50">
            <div class="form-check">
                <input type="hidden" value="0" name="{{ $name }}">
                <input
                    {{ $attributes->class(['form-check-input','is-invalid' => $errors->has($name)]) }}
                    type="checkbox" id="{{ $name }}" name="{{ $name }}" value="1" @checked(old($name) || $checked) />
                <label class="form-check-label" for="{{$name}}">{{$label}}</label>
                @error($name)
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
@elseif($type=='select')
    <div class="mb-3 pb-1 d-flex justify-content-center">
        <div class="w-xs-90 w-lg-50">
            <select {{ $attributes->class(['select' => !$isDusk()]) }} id="{{ $name }}" name="{{ $name }}">
                {{ $slot }}
            </select>
            @error($name)
            <span class="select-invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @push('inlinescripts')
                <script>
                    document.getElementById('{{ $name }}').parentNode.querySelector('.select-input').classList.add("is-invalid");
                </script>
            @endpush
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
    <div class="m-3 text-end">
        <button type="submit" class="btn btn-lg btn-primary py-3 px-4">
            {{ $label }}
        </button>
    </div>
@else
    <div class="mb-3 pb-1 d-flex justify-content-center">
        <div class="form-outline w-xs-90 w-lg-50">
            @if($type=='textarea')
                <textarea id="{{ $name }}" name="{{ $name }}"
                    {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>{{ old($name) }}</textarea>
            @else
                <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
                       @if(old($name))) value="{{ old($name) }}" @endif
                       {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
                       @if(isset($readonly)) value="{{ $readonly }}" aria-label="{{ $readonly }}"
                       readonly @endif />
            @endif

            <label for="{{ $name }}" class="form-label">
                @if(isset($title))
                    <a href="#" class="p-2 float-end" data-mdb-toggle="tooltip" data-mdb-html="true"
                       title="@lang('registration.' . $title)"><i class="far fa-info"></i></a>
                @endif
                {{ $label }}
            </label>

            @error($name)
            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
            @enderror

        </div>
    </div>
@endif
