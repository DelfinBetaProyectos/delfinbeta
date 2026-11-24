@props([
    'value' => '',
    'model' => 'form.published_on',
])

{{--
    Dashboard field for a publication day (Maracay time): empty is a draft, today or earlier is published,
    a future day is scheduled. "Today" and "Back to draft" fill the Livewire property directly.
--}}
<flux:field>
    <flux:label badge="{{ __('Optional') }}">{{ __('Publication date') }}</flux:label>
    <flux:description>{{ __('Empty keeps it as a draft. Today or earlier publishes it; a future date publishes it that day (Maracay time).') }}</flux:description>

    <div class="flex flex-wrap items-center gap-3">
        <div class="w-48">
            <flux:input wire:model="{{ $model }}" type="date" :aria-label="__('Publication date')" />
        </div>
        <flux:button size="sm" variant="ghost" wire:click="$set('{{ $model }}', '{{ now()->toDateString() }}')">{{ __('Today') }}</flux:button>
        @if (filled($value))
            <flux:button size="sm" variant="ghost" wire:click="$set('{{ $model }}', '')">{{ __('Back to draft') }}</flux:button>
        @endif
    </div>

    <flux:error :name="$model" />
</flux:field>
