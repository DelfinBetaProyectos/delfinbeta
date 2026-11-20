@props([
    'label' => __('nav.download_cv'),
    'align' => 'right',
    'triggerClass' => '',
])

{{-- Download CV button that opens the resume options, one per site language. --}}
<details data-dropdown {{ $attributes->class('group/resume relative') }}>
    <summary @class([
        'inline-flex cursor-pointer list-none items-center gap-2 rounded-btn border border-ice font-medium text-ice transition-colors hover:bg-ice/10 hover:text-mist focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice [&::-webkit-details-marker]:hidden',
        $triggerClass,
    ])>
        {{ $label }}
        <svg class="size-4 transition-transform group-open/resume:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
        </svg>
    </summary>

    <ul @class([
        'absolute z-50 mt-2 flex w-48 flex-col rounded-card border border-line-dark bg-navy p-2 shadow-xl',
        'right-0' => $align === 'right',
        'left-0' => $align === 'left',
    ])>
        @foreach (config('site.locales') as $resumeLocale)
            <li>
                <a href="{{ Storage::disk('public')->url(config("site.resumes.{$resumeLocale}")) }}" download hreflang="{{ $resumeLocale }}" class="flex items-center gap-3 rounded-btn px-4 py-3 text-[15px] font-medium text-mist hover:bg-deep hover:text-ice">
                    <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3v12" /><path d="M7 10l5 5 5-5" /><path d="M5 21h14" />
                    </svg>
                    <span lang="{{ $resumeLocale }}">{{ config("site.locale_names.{$resumeLocale}") }}</span>
                    <span class="ml-auto font-mono text-xs text-mist/70 uppercase">PDF</span>
                </a>
            </li>
        @endforeach
    </ul>
</details>
