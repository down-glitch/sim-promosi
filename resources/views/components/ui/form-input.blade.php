@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => old($name),
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'helperText' => null,
    'errorKey' => $name,
    'options' => [], // Untuk select
    'multiple' => false, // Untuk select
    'cols' => 4, // Untuk textarea
    'rows' => 4, // Untuk textarea
])

@php
    $isRequired = $required ? 'required' : '';
    $isReadonly = $readonly ? 'readonly' : '';
    $isDisabled = $disabled ? 'disabled' : '';
    $hasError = $errors->has($errorKey) ? 'is-invalid' : '';
    $inputId = $name . '_' . uniqid();
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label fw-semibold">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    @if($type === 'select')
        <select 
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            id="{{ $inputId }}"
            {{ $isRequired }}
            {{ $isReadonly }}
            {{ $isDisabled }}
            {{ $multiple ? 'multiple' : '' }}
            {{ $attributes->merge(['class' => "form-select $hasError"]) }}
        >
            @if(!$multiple)
                <option value="">Pilih {{ $label ?: $name }}</option>
            @endif
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" {{ (is_array($value) ? in_array($optionValue, $value) : $value == $optionValue) ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea 
            name="{{ $name }}"
            id="{{ $inputId }}"
            cols="{{ $cols }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            {{ $isRequired }}
            {{ $isReadonly }}
            {{ $isDisabled }}
            {{ $attributes->merge(['class' => "form-control $hasError"]) }}
        >{{ $value }}</textarea>
    @elseif($type === 'checkbox')
        <div class="form-check">
            <input 
                type="checkbox"
                name="{{ $name }}"
                id="{{ $inputId }}"
                value="1"
                {{ $value ? 'checked' : '' }}
                {{ $isRequired }}
                {{ $isReadonly }}
                {{ $isDisabled }}
                {{ $attributes->merge(['class' => "form-check-input $hasError"]) }}
            >
            <label class="form-check-label" for="{{ $inputId }}">
                {{ $label }}
            </label>
        </div>
    @else
        <input 
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $isRequired }}
            {{ $isReadonly }}
            {{ $isDisabled }}
            {{ $attributes->merge(['class' => "form-control $hasError"]) }}
        >
    @endif
    
    @if($helperText)
        <div class="form-text">{{ $helperText }}</div>
    @endif
    
    @if($errors->has($errorKey))
        <div class="invalid-feedback">
            {{ $errors->first($errorKey) }}
        </div>
    @endif
</div>