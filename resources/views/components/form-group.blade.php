@if($type=='checkbox')
    <div {{ $attributes->class(['row', 'align-self-center', 'mb-3', 'admin-only' => $isAdminonly]) }}>
        <div class="container-md col-lg-6">
            <div class="form-check">
                <input type="hidden" value="0" name="{{ $name }}">
                <input
                    {{ $attributes->class(['form-check-input','is-invalid' => $errors->has($errorKey)]) }}
                    type="checkbox" id="{{ $name }}" name="{{ $name }}" value="1" @checked($getSafeDefault()) />
                <label class="form-check-label" for="{{$name}}">{{$label}}</label>
                @error($errorKey)
                <span class="muusa-invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
@elseif($type=='select')
    <div {{ $attributes->class(['row', 'align-self-center', 'mb-3', 'admin-only' => $isAdminonly]) }}>
        <div class="container-md col-lg-6 position-relative">
            <select {{ $attributes->class(['select' => !$isDusk()]) }} id="{{ $name }}" name="{{ $name }}">
                {{ $slot }}
            </select>
            @error($errorKey)
            <span class="muusa-invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @push('inlinescripts')
                <script>
                    document.getElementById('{{ $name }}').parentNode.querySelector('.select-input').classList.add("is-invalid");
                </script>
            @endpush
            @enderror

            <label for="{{ $name }}" class="form-label select-label">{!! $label !!}</label>

            @if(isset($tooltip))
                <button class="btn btn-info select-helpbtn p-1" data-mdb-toggle="tooltip" data-mdb-html="true"
                        title="{{ $tooltip }}">
                    <i class="far fa-circle-question fa-xl"></i>
                </button>
            @endif
        </div>
    </div>
@elseif($type=='submit')
    <div class="m-3 text-end">
        <button type="submit" class="btn btn-lg btn-primary py-3 px-4" name="{{ $label }}">{{ $label }}</button>
    </div>
@elseif($type=='hidden')
    <input id="{{ $name }}" name="{{ $name }}" type="hidden" value="{{ $value }}"/>
@else
    <div {{ $attributes->class(['row', 'align-self-center', 'mb-3', 'admin-only' => $isAdminonly]) }}>
        <div class="container-md col-lg-6 position-relative">
            <div class="form-outline">
                @if($type=='textarea')
                    <textarea id="{{ $name }}" name="{{ $name }}"
                    {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>{{ $getSafeDefault() }}</textarea>
                @else
                    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $getSafeDefault() }}"
                           {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($errorKey)]) }}
                           @can('readonly') aria-label="{{ $getSafeDefault() }}" readonly @endif />
                @endif

                @error($errorKey)
                <span class="muusa-invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror

                <label for="{{ $name }}" class="form-label">{!! $label !!}</label>

                @if(isset($tooltip))
                    <button class="btn btn-info register-helpbtn p-1" data-mdb-toggle="tooltip" data-mdb-html="true"
                            title="{{ $tooltip }}">
                        <i class="far fa-circle-question fa-xl"></i>
                    </button>
                @endif
            </div>
            {{ $slot }}
        </div>
    </div>
@endif
