@props([
    'label',
    'name',
    'type' => 'text',
])

<div class="flex flex-col gap-2">
    <label for="{{ $attributes->get('id', $name) }}" class="text-sm font-medium text-white">{{ $label }}</label>

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $attributes->get('id', $name) }}"
        {{ $attributes->except('id')->class('min-h-12 rounded-btn border border-sea bg-transparent px-4 text-mist placeholder:text-muted/70 focus:border-ice focus:outline-none focus:ring-2 focus:ring-ice/40') }}
    />

    @error($name)
        <p class="text-sm text-ice">{{ $message }}</p>
    @enderror
</div>
