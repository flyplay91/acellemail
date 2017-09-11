@if (isset($unlimited_check))
    <div class="row">
        <div class="col-md-6">
@endif
            <input
                {{ isset($disabled) && $disabled == true ? ' disabled="disabled"' : "" }}
                id="{{ $name }}" placeholder="{{ isset($placeholder) ? $placeholder : "" }}"
                value="{{ isset($value) ? $value : "" }}"
                type="text"
                name="{{ $name }}"
                class="form-control{{ $classes }}  {{ isset($class) ? $class : "" }}"
                {!! isset($default_value) ? 'default-value="'.$default_value.'"' : '' !!}
            >
@if (isset($unlimited_check))
        </div>
        <div class="col-md-6">
            <div class="checkbox inline unlimited-check text-semibold">
                <label>
                    <input{{ $value  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
                    {{ trans('messages.unlimited') }}
                </label>
            </div>
        </div>
    </div>
@endif
