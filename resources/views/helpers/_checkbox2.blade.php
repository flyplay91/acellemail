<input type="hidden" name="{{ $name }}" value="{{ $options[0] }}" />

<div class="checkbox inline unlimited-check text-semibold">
    <label>
        <input {{ $value == $options[1] ? " checked" : "" }}
            {{ isset($disabled) && $disabled == true ? ' disabled="disabled"' : "" }}
            id="{{ $name }}" name="{{ $name }}" value="{{ $options[1] }}"
            class="styled {{ $classes }}  {{ isset($class) ? $class : "" }}"
            type="checkbox" class="styled">
        {{ $label }}
    </label>
</div>
