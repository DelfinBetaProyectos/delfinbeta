@props([
    'label',
    'name',
    'type' => 'text',
])

@php
    $id = $attributes->get('id', $name);
    $controlClasses = 'rounded-btn border border-sea bg-navy px-3.5 text-mist placeholder:text-muted/70 focus:border-ice focus:outline-none focus:ring-2 focus:ring-ice/40';
@endphp

<div class="flex flex-col gap-2">
    <label for="{{ $id }}" class="text-sm font-medium text-white">{{ $label }}</label>

    @if ($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $id }}"
            {{ $attributes->except('id')->merge(['rows' => 4])->class("{$controlClasses} resize-y py-3") }}
        >{{ old($name) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ old($name) }}"
            {{ $attributes->except('id')->class("{$controlClasses} min-h-12") }}
        />
    @endif

    @error($name)
        <p class="text-sm text-ice">{{ $message }}</p>
    @enderror
</div>
