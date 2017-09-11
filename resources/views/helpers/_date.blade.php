<div class="input-icon-right">											
    <input {{ isset($disabled) && $disabled == true ? ' disabled="disabled"' : "" }} id="{{ $name }}" placeholder="{{ isset($placeholder) ? $placeholder : "" }}" value="{{ isset($value) ? $value : "" }}" type="text" name="{{ $name }}" class="control-with-mask pickadate-control form-control{{ $classes }} pickadate{{ isset($class) ? $class : "" }}">
    <span class="mask-control date-mask-control"></span>
    <span class=""><i class="icon-calendar22"></i></span>
</div>
