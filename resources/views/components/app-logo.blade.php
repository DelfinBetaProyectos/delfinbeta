@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand {{ $attributes }}>
        <x-slot name="logo" class="h-8 gap-2 overflow-visible">
            <x-app-logo-icon class="h-8 w-10" />
            <span class="flex in-data-flux-sidebar-collapsed-desktop:hidden">
                <x-brand-mark type="logotipo" :label="config('app.name')" class="h-8 w-[120px]" />
            </span>
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand {{ $attributes }}>
        <x-slot name="logo" class="h-8 gap-2 overflow-visible">
            <x-app-logo-icon class="h-8 w-10" />
            <x-brand-mark type="logotipo" :label="config('app.name')" class="h-8 w-[120px]" />
        </x-slot>
    </flux:brand>
@endif
