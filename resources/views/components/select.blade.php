@props([
    'label',
    'name',
    'options' => [],
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div class="flex flex-col gap-2">
    <label for="{{ $id }}" class="text-sm font-medium text-white">{{ $label }}</label>

    <select
        name="{{ $name }}"
        id="{{ $id }}"
        {{ $attributes->except('id')->class('min-h-12 rounded-btn border border-sea bg-navy px-3.5 text-mist focus:border-ice focus:outline-none focus:ring-2 focus:ring-ice/40') }}
    >
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}" @selected(old($name) === $value)>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @error($name)
        <p class="text-sm text-ice">{{ $message }}</p>
    @enderror
</div>
