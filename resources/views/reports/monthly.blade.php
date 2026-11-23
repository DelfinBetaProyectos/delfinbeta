{{-- Monthly personal brand report, rendered to PDF by dompdf (inline styles, tables only). --}}
@php
    $plan = $statistics->plan;
    $visits = $statistics->visits();
    $clicks = $statistics->araguaneyCodeClicks();
    $messages = $statistics->messageSummary();
    $downloads = $statistics->resumeDownloads();
    $downloadCountries = $statistics->resumeDownloadsByCountry();
    $sources = $statistics->sources();
    $pages = $statistics->topPages();
    $content = $statistics->contentPerformance();
    $percent = fn (?int $value, ?int $goal): string => $value === null || ! $goal ? '—' : round($value * 100 / $goal).' %';
    $muted = 'color: #35506A;';
    $cell = 'padding: 6px 8px; border-bottom: 1px solid #C9DAE6;';
    $head = 'padding: 6px 8px; border-bottom: 1px solid #001B48; text-align: left; font-size: 10px; text-transform: uppercase; color: #35506A;';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Monthly report') }} · {{ $report->monthName() }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #001B48; margin: 0; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        h2 { font-size: 14px; margin: 22px 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        .num { text-align: right; }
        .kpi td { width: 20%; padding: 10px; border: 1px solid #C9DAE6; vertical-align: top; }
        .kpi strong { display: block; font-size: 20px; margin: 4px 0; }
    </style>
</head>
<body>
    <p style="margin: 0 0 6px; font-size: 10px; {{ $muted }}">delfinbeta.tech · {{ __('Monthly report') }}</p>
    <h1>{{ __('Personal brand statistics') }} · {{ $report->monthName() }}</h1>
    <p style="margin: 0; {{ $muted }}">
        {{ $plan ? $plan->label() : __('No plan this month') }} ·
        {{ __('Generated on :date', ['date' => now()->settings(['locale' => app()->getLocale()])->isoFormat('D MMM YYYY, h:mm a')]) }} (UTC-4)
    </p>

    @unless ($statistics->hasAnalytics())
        <p style="margin: 12px 0 0; padding: 8px; background: #DDE8F0;">{{ __('Google Analytics is not connected: visits, sources, pages and clicks are missing from this report.') }}</p>
    @endunless

    <h2>{{ __('Summary') }}</h2>
    <table class="kpi">
        <tr>
            <td>{{ __('Visits') }}<strong>{{ $visits ?? '—' }}</strong>@if ($plan)<span style="{{ $muted }}">{{ __('Goal :goal', ['goal' => $statistics->visitsGoal()]) }} · {{ $percent($visits, $statistics->visitsGoal()) }}</span>@endif</td>
            <td>{{ __('Clicks to AraguaneyCode') }}<strong>{{ $clicks ?? '—' }}</strong>@if ($plan)<span style="{{ $muted }}">{{ __('Plan goal :goal', ['goal' => $plan->goal('araguaneycode_clicks')]) }}</span>@endif</td>
            <td>{{ __('Business conversations') }}<strong>{{ $statistics->businessConversations() }}</strong>@if ($plan)<span style="{{ $muted }}">{{ __('Plan goal :goal', ['goal' => $plan->goal('business_conversations')]) }}</span>@endif</td>
            <td>{{ __('Form messages') }}<strong>{{ $messages['total'] }}</strong><span style="{{ $muted }}">{{ __(':handled of :total replied in under :hours h', ['handled' => $messages['handled_on_time'], 'total' => $messages['total'], 'hours' => config('site.reply_within_hours')]) }}</span></td>
            <td>{{ __('Resume downloads') }}<strong>{{ $downloads['total'] ?? '—' }}</strong>@if ($downloads !== null)<span style="{{ $muted }}">ES {{ $downloads['by_locale']['es'] ?? 0 }} · EN {{ $downloads['by_locale']['en'] ?? 0 }}</span>@endif</td>
        </tr>
    </table>

    @if ($plan)
    <h2>{{ __('Plan goals entered by hand') }} · {{ $plan->name }}</h2>
    <table>
        <tr><th style="{{ $head }}">{{ __('Goal') }}</th><th class="num" style="{{ $head }} text-align: right;">{{ __('So far') }}</th><th class="num" style="{{ $head }} text-align: right;">{{ __('Target') }}</th></tr>
        @foreach ($statistics->manualGoals() as $figure => $goal)
            <tr><td style="{{ $cell }}">{{ __("goals.{$figure}") }}</td><td class="num" style="{{ $cell }}">{{ $goal['value'] }}</td><td class="num" style="{{ $cell }}">{{ $goal['goal'] }}</td></tr>
        @endforeach
    </table>
    @endif

    <h2>{{ __('Messages by topic') }}</h2>
    <table>
        @foreach ($messages['by_topic'] as $topic => $count)
            <tr><td style="{{ $cell }}">{{ App\Enums\ContactTopic::from($topic)->shortLabel() }}</td><td class="num" style="{{ $cell }}">{{ $count }}</td></tr>
        @endforeach
        <tr><td style="{{ $cell }} {{ $muted }}">{{ __('Average response time') }}</td><td class="num" style="{{ $cell }}">{{ $messages['average_response_hours'] === null ? '—' : trans_choice(':count hour|:count hours', $messages['average_response_hours']) }}</td></tr>
    </table>

    @if ($sources !== null)
        <h2>{{ __('Where visits come from') }}</h2>
        <table>
            @forelse ($sources as $source)
                <tr><td style="{{ $cell }}">{{ $source['source'] }}</td><td class="num" style="{{ $cell }}">{{ $source['visits'] }} · {{ $source['share'] }} %</td></tr>
            @empty
                <tr><td style="{{ $cell }} {{ $muted }}">{{ __('No visits in this period.') }}</td></tr>
            @endforelse
        </table>
    @endif

    @if ($downloadCountries)
        <h2>{{ __('Resume downloads by country') }}</h2>
        <table>
            @foreach ($downloadCountries as $country)
                <tr><td style="{{ $cell }}">{{ $country['country'] }}</td><td class="num" style="{{ $cell }}">{{ $country['downloads'] }}</td></tr>
            @endforeach
        </table>
    @endif

    @if ($pages !== null)
        <h2>{{ __('Most viewed pages') }}</h2>
        <table>
            @forelse ($pages as $page)
                <tr><td style="{{ $cell }} font-family: 'DejaVu Sans Mono', monospace;">{{ $page['path'] }}</td><td class="num" style="{{ $cell }}">{{ $page['views'] }}</td></tr>
            @empty
                <tr><td style="{{ $cell }} {{ $muted }}">{{ __('No visits in this period.') }}</td></tr>
            @endforelse
        </table>
    @endif

    @if ($content !== [])
        <h2>{{ __('Content performance') }}</h2>
        <table>
            <tr>
                <th style="{{ $head }}">{{ __('Article') }}</th>
                <th style="{{ $head }} text-align: right;">{{ __('Visits') }}</th>
                <th style="{{ $head }} text-align: right;">{{ __('Average reading') }}</th>
                <th style="{{ $head }} text-align: right;">{{ __('Clicks to AraguaneyCode') }}</th>
            </tr>
            @foreach ($content as $row)
                <tr>
                    <td style="{{ $cell }}">{{ $row['article']->title }}</td>
                    <td class="num" style="{{ $cell }}">{{ $row['views'] ?? '—' }}</td>
                    <td class="num" style="{{ $cell }}">{{ $row['seconds'] === null ? '—' : App\Services\BrandStatistics::formatDuration($row['seconds']) }}</td>
                    <td class="num" style="{{ $cell }}">{{ $row['clicks'] ?? '—' }}</td>
                </tr>
            @endforeach
        </table>
    @endif
</body>
</html>
