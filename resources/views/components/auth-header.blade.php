@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-2">
    <h1 class="font-display text-[32px] leading-tight font-bold tracking-[-0.02em] text-zinc-900 dark:text-white">{{ $title }}</h1>
    <p class="text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
</div>
