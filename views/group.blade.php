<div class="{{ $group_class }}">
    <label for="{{ $name }}" class="{{ $base_class }}__label">{{ $label }}</label>
    <div class="{{ $base_class }}__input ">
        {{ $field }}
        @if (! empty($legend))
            <span class="{{ $base_class }}__legend">{{ $legend }}</span>
        @endif
        @if (! empty($error))
            <span class="{{ $base_class }}__error">{{ $error }}</span>
        @endif
    </div>
</div>
