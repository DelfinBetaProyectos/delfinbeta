<?php

use App\Models\ContactMessage;
use App\Models\GrowthPlan;
use App\Services\MonthlyReport;
use App\Services\BrandStatistics;
use App\Services\StatsPeriod;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Statistics')] class extends Component {
    /**
     * Week ranges offered for the weekly visits.
     */
    public const WEEK_OPTIONS = [4, 8, 12];

    /**
     * "this_month", "last_month" or "plan-{id}".
     */
    #[Url(except: 'this_month')]
    public string $period = 'this_month';

    /**
     * "es", "en" or "" for both languages.
     */
    #[Url(except: '')]
    public string $locale = '';

    public int $weeks = 4;

    public bool $showChart = false;

    /**
     * Keep the filters within the allowed values, whatever the URL or the request says.
     */
    public function updated(string $property): void
    {
        if (! StatsPeriod::isValidKey($this->period)) {
            $this->period = 'this_month';
        }

        if (! in_array($this->locale, config('site.locales'), true)) {
            $this->locale = '';
        }

        if (! in_array($this->weeks, self::WEEK_OPTIONS, true)) {
            $this->weeks = 4;
        }

        unset($this->statistics);
    }

    /**
     * Fix filters that came from the URL.
     */
    public function mount(): void
    {
        $this->updated('period');
    }

    /**
     * The figures for the chosen period and language.
     */
    #[Computed]
    public function statistics(): BrandStatistics
    {
        return new BrandStatistics(StatsPeriod::fromKey($this->period), $this->locale !== '' ? $this->locale : null);
    }

    /**
     * Every plan, newest first, for the period selector.
     *
     * @return Illuminate\Database\Eloquent\Collection<int, GrowthPlan>
     */
    #[Computed]
    public function plans(): Illuminate\Database\Eloquent\Collection
    {
        return GrowthPlan::query()->orderByDesc('starts_on')->get();
    }

    /**
     * Query string the exports share with the dashboard filters.
     *
     * @return array{period: string, locale: string}
     */
    public function exportFilters(): array
    {
        return ['period' => $this->period, 'locale' => $this->locale];
    }

    /**
     * When a message arrived: "Hoy, 10:12", "Ayer, 18:40" or "20 nov".
     */
    public function receivedAt(ContactMessage $contactMessage): string
    {
        $createdAt = $contactMessage->created_at->locale(app()->getLocale());

        return match (true) {
            $createdAt->isToday() => __('Today').', '.$createdAt->format('H:i'),
            $createdAt->isYesterday() => __('Yesterday').', '.$createdAt->format('H:i'),
            default => $createdAt->isoFormat('D MMM'),
        };
    }
}; ?>

<section class="w-full space-y-6">
    @php
        $statistics = $this->statistics;
        $plan = $statistics->plan;

        $visits = $statistics->visits();
        $clicks = $statistics->araguaneyCodeClicks();
        $sources = $statistics->sources();
        $topPages = $statistics->topPages();
        $weeklyVisits = $statistics->weeklyVisits($weeks);
        $content = $statistics->contentPerformance();
        $conversations = $statistics->businessConversations();
        $messageSummary = $statistics->messageSummary();
        $downloads = $statistics->resumeDownloads();
    $downloadCountries = $statistics->resumeDownloadsByCountry(3);
        $manualGoals = $statistics->manualGoals();
        $latestMessages = $statistics->latestMessages();

        $percent = fn (?int $value, int $goal): int => $goal > 0 ? (int) round(($value ?? 0) * 100 / $goal) : 0;
        $topicTotal = max(1, max($messageSummary['by_topic'] ?: [0]));
        $weeklyMax = max(1, ...(array_column($weeklyVisits ?? [], 'visits') ?: [1]));
        $card = 'rounded-xl border border-line-light bg-white p-5 dark:border-white/10 dark:bg-white/5';
    @endphp

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="font-mono text-xs text-deep dark:text-ice">{{ __('Dashboard') }} · /dashboard</p>
            <flux:heading size="xl" level="1" class="mt-1 font-display">{{ __('Personal brand statistics') }}</flux:heading>
            <flux:text class="mt-1 flex flex-wrap items-center gap-2">
                @if ($plan === null)
                    <span>{{ __('No plan yet.') }} <flux:link :href="route('dashboard.goals.index')" wire:navigate>{{ __('Create a plan') }}</flux:link></span>
                @else
                    <span>{{ $plan->name }} · {{ match ($plan->status()) {
                        'upcoming' => __('starts on :date', ['date' => $plan->starts_on->settings(['locale' => app()->getLocale()])->isoFormat('D MMM YYYY')]),
                        'finished' => __('ended on :date', ['date' => $plan->endsOn()->settings(['locale' => app()->getLocale()])->isoFormat('D MMM YYYY')]),
                        default => __('Day :day of :days', ['day' => $plan->currentDay(), 'days' => $plan->days]),
                    } }}</span>
                @endif
                <span aria-hidden="true">·</span>
                <span>{{ __('Next monthly review: :date', ['date' => MonthlyReport::nextSendDate()->settings(['locale' => app()->getLocale()])->isoFormat(__('dddd, MMMM D'))]) }}</span>

                @if (! $statistics->hasAnalytics())
                    <flux:badge size="sm" color="zinc">{{ __('Google Analytics not connected') }}</flux:badge>
                @elseif ($statistics->analyticsFailed)
                    <flux:badge size="sm" color="red">{{ __('Google Analytics did not respond') }}</flux:badge>
                @endif
            </flux:text>
        </div>

        <div class="flex flex-wrap gap-3">
            <div class="w-40">
                <flux:select wire:model.live="locale" :label="__('Language')">
                    <flux:select.option value="">{{ __('Both languages') }}</flux:select.option>
                    @foreach (config('site.locales') as $localeOption)
                        <flux:select.option :value="$localeOption">{{ __('Only :locale', ['locale' => strtoupper($localeOption)]) }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="w-72">
                <flux:select wire:model.live="period" :label="__('Period of the figures')">
                    @foreach (StatsPeriod::PRESETS as $periodOption)
                        <flux:select.option :value="$periodOption">{{ StatsPeriod::fromKey($periodOption)->label() }}</flux:select.option>
                    @endforeach
                    @foreach ($this->plans as $planOption)
                        <flux:select.option :value="'plan-'.$planOption->id">{{ $planOption->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Key figures --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5" wire:loading.class="opacity-60">
        <div class="{{ $card }}">
            <flux:text size="sm" class="font-medium">{{ __('Visits') }}</flux:text>
            <p class="mt-2 font-display text-3xl font-bold text-navy dark:text-white">{{ $visits ?? '—' }}</p>
            @if (($visitsGoal = $statistics->visitsGoal()) !== null)
                <x-dashboard.meter class="mt-3" :value="$visits" :max="$visitsGoal" />
                <flux:text size="sm" class="mt-2">
                    {{ $statistics->period->isCalendarMonth() ? __('Goal :goal/month', ['goal' => $visitsGoal]) : __('Goal :goal', ['goal' => $visitsGoal]) }}
                    @if ($visits !== null) · {{ $percent($visits, $visitsGoal) }} % @endif
                </flux:text>
            @endif
        </div>

        <div class="{{ $card }}">
            <flux:text size="sm" class="font-medium">{{ __('Clicks to AraguaneyCode') }}</flux:text>
            <p class="mt-2 font-display text-3xl font-bold text-navy dark:text-white">{{ $clicks ?? '—' }}</p>
            @if ($plan !== null)
                <x-dashboard.meter class="mt-3" :value="$clicks" :max="$plan->goal('araguaneycode_clicks')" />
                <flux:text size="sm" class="mt-2">
                    {{ __('Goal :goal by day :days', ['goal' => $plan->goal('araguaneycode_clicks'), 'days' => $plan->days]) }}
                    @if ($clicks !== null) · {{ $percent($clicks, $plan->goal('araguaneycode_clicks')) }} % @endif
                </flux:text>
            @endif
        </div>

        <div class="{{ $card }}">
            <flux:text size="sm" class="font-medium">{{ __('Business conversations') }}</flux:text>
            <p class="mt-2 font-display text-3xl font-bold text-navy dark:text-white">{{ $conversations }}</p>
            @if ($plan !== null)
                <x-dashboard.meter class="mt-3" :value="$conversations" :max="$plan->goal('business_conversations')" />
                <flux:text size="sm" class="mt-2">{{ __('Goal :goal by day :days', ['goal' => $plan->goal('business_conversations'), 'days' => $plan->days]) }} · {{ $percent($conversations, $plan->goal('business_conversations')) }} %</flux:text>
            @endif
        </div>

        <div class="{{ $card }}">
            <flux:text size="sm" class="font-medium">{{ __('Form messages') }}</flux:text>
            <p class="mt-2 font-display text-3xl font-bold text-navy dark:text-white">{{ $messageSummary['total'] }}</p>
            <x-dashboard.meter class="mt-3" :value="$messageSummary['handled_on_time']" :max="max(1, $messageSummary['total'])" />
            <flux:text size="sm" class="mt-2">{{ __(':handled of :total replied in under :hours h', ['handled' => $messageSummary['handled_on_time'], 'total' => $messageSummary['total'], 'hours' => config('site.reply_within_hours')]) }}</flux:text>
        </div>

        <div class="{{ $card }}">
            <flux:text size="sm" class="font-medium">{{ __('Resume downloads') }}</flux:text>
            <p class="mt-2 font-display text-3xl font-bold text-navy dark:text-white">{{ $downloads['total'] ?? '—' }}</p>
            @if ($downloads !== null)
                <flux:text size="sm" class="mt-3">{{ __(':es in Spanish · :en in English', ['es' => $downloads['by_locale']['es'] ?? 0, 'en' => $downloads['by_locale']['en'] ?? 0]) }}</flux:text>
            @endif
            @if ($downloadCountries)
                <flux:text size="sm" class="mt-1">
                    {{ collect($downloadCountries)->map(fn (array $country): string => $country['country'].' '.$country['downloads'])->implode(' · ') }}
                </flux:text>
            @endif
        </div>
    </div>

    {{-- Weekly visits --}}
    <div class="{{ $card }}">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <flux:heading size="lg" level="2">{{ __('Visits per week') }}</flux:heading>
                @if ($weeklyVisits)
                    @php($weeklyValues = array_column($weeklyVisits, 'visits'))
                    <flux:text class="mt-1">{{ __(':total visits in :weeks weeks · from :min to :max per week', ['total' => array_sum($weeklyValues), 'weeks' => $weeks, 'min' => min($weeklyValues), 'max' => max($weeklyValues)]) }}</flux:text>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach ($this::WEEK_OPTIONS as $weekOption)
                    <flux:button size="sm" wire:key="weeks-{{ $weekOption }}" wire:click="$set('weeks', {{ $weekOption }})" :variant="$weeks === $weekOption ? 'primary' : 'outline'" :aria-pressed="$weeks === $weekOption ? 'true' : 'false'">
                        {{ __(':count weeks', ['count' => $weekOption]) }}
                    </flux:button>
                @endforeach
                <flux:button size="sm" variant="outline" wire:click="$toggle('showChart')">{{ $showChart ? __('View table') : __('View chart') }}</flux:button>
            </div>
        </div>

        @if ($weeklyVisits === null)
            <flux:text class="mt-4">{{ __('Connect Google Analytics to see the visits.') }}</flux:text>
        @elseif ($showChart)
            <div class="mt-6 flex h-48 items-end gap-2" role="img" aria-label="{{ __('Visits per week') }}">
                @foreach ($weeklyVisits as $week)
                    <div wire:key="chart-{{ $week['week']->toDateString() }}" class="flex h-full flex-1 flex-col items-center justify-end gap-1">
                        <span class="text-xs font-medium text-navy dark:text-white">{{ $week['visits'] }}</span>
                        <div class="w-full max-w-12 rounded-t bg-deep dark:bg-ice" style="height: {{ max(2, round($week['visits'] * 100 / $weeklyMax)) }}%"></div>
                        <span class="text-xs whitespace-nowrap text-ink dark:text-zinc-400">{{ $week['week']->locale(app()->getLocale())->isoFormat('D MMM') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <flux:table class="mt-4">
                <flux:table.columns>
                    <flux:table.column>{{ __('Week starting') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Visits') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($weeklyVisits as $week)
                        <flux:table.row :key="'week-'.$week['week']->toDateString()">
                            <flux:table.cell>{{ $week['week']->locale(app()->getLocale())->isoFormat('D MMM') }}</flux:table.cell>
                            <flux:table.cell align="end">{{ $week['visits'] }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Sources --}}
        <div class="{{ $card }}">
            <flux:heading size="lg" level="2">{{ __('Where visits come from') }}</flux:heading>
            <flux:text class="mt-1">{{ $statistics->period->label() }}@if ($visits !== null) · {{ trans_choice(':count visit|:count visits', $visits) }}@endif</flux:text>

            @if ($sources === null)
                <flux:text class="mt-4">{{ __('Connect Google Analytics to see the sources.') }}</flux:text>
            @elseif ($sources === [])
                <flux:text class="mt-4">{{ __('No visits in this period.') }}</flux:text>
            @else
                <dl class="mt-4 space-y-3">
                    @foreach ($sources as $source)
                        <div wire:key="source-{{ $source['source'] }}" class="grid grid-cols-[6rem_1fr_auto] items-center gap-3 text-sm">
                            <dt class="truncate text-navy dark:text-white">{{ $source['source'] }}</dt>
                            <x-dashboard.meter :value="$source['share']" :max="100" class="h-2.5" />
                            <dd class="text-right whitespace-nowrap text-ink dark:text-zinc-300">{{ $source['visits'] }} · {{ $source['share'] }} %</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>

        {{-- Messages by topic --}}
        <div class="{{ $card }}">
            <flux:heading size="lg" level="2">{{ __('Messages by topic') }}</flux:heading>
            <flux:text class="mt-1">{{ $statistics->period->label() }} · {{ trans_choice(':count message|:count messages', $messageSummary['total']) }} · {{ trans_choice(':count sent to AraguaneyCode|:count sent to AraguaneyCode', $messageSummary['forwarded']) }}</flux:text>

            <dl class="mt-4 space-y-3">
                @foreach ($messageSummary['by_topic'] as $topicValue => $count)
                    <div wire:key="topic-{{ $topicValue }}" class="grid grid-cols-[6rem_1fr_auto] items-center gap-3 text-sm">
                        <dt class="truncate text-navy dark:text-white">{{ App\Enums\ContactTopic::from($topicValue)->shortLabel() }}</dt>
                        <x-dashboard.meter :value="$count" :max="$topicTotal" class="h-2.5" />
                        <dd class="w-6 text-right text-ink dark:text-zinc-300">{{ $count }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-6 grid grid-cols-2 gap-4 border-t border-line-light pt-4 text-sm dark:border-white/10">
                <div>
                    <flux:text size="sm">{{ __('Form language') }}</flux:text>
                    <p class="font-medium text-navy dark:text-white">{{ __(':es in Spanish · :en in English', ['es' => $messageSummary['by_locale']['es'] ?? 0, 'en' => $messageSummary['by_locale']['en'] ?? 0]) }}</p>
                </div>
                <div>
                    <flux:text size="sm">{{ __('Average response time') }}</flux:text>
                    <p class="font-medium text-navy dark:text-white">{{ $messageSummary['average_response_hours'] === null ? '—' : trans_choice(':count hour|:count hours', $messageSummary['average_response_hours']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Goals entered by hand --}}
    <div class="{{ $card }}">
        @if ($plan === null)
            <flux:heading size="lg" level="2">{{ __('Plan goals entered by hand') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Create a plan with its goals to follow them here.') }}</flux:text>
            <flux:button class="mt-4" variant="outline" :href="route('dashboard.goals.index')" wire:navigate>{{ __('Create a plan') }}</flux:button>
        @else
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <flux:heading size="lg" level="2">{{ __('Plan goals entered by hand') }} · {{ $plan->name }}</flux:heading>
                    <flux:text class="mt-1">
                        {{ __('The site does not measure these figures: they are entered at the monthly review.') }}
                        @if ($plan->status() === 'active')
                            {{ __('The line marks where you should be today (day :day: :percent %).', ['day' => $plan->currentDay(), 'percent' => (int) round($plan->expectedProgress() * 100)]) }}
                        @endif
                    </flux:text>
                </div>
                <flux:button variant="outline" :href="route('dashboard.goals.index', ['plan' => $plan->id])" wire:navigate>{{ __('Update figures') }}</flux:button>
            </div>

            <div class="mt-5 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($manualGoals as $figure => $goal)
                    <div wire:key="goal-{{ $figure }}">
                        <div class="flex items-start justify-between gap-2 text-sm">
                            <span class="font-medium text-navy dark:text-white">{{ __("goals.{$figure}") }}</span>
                            <span class="whitespace-nowrap text-ink dark:text-zinc-300">{{ $goal['value'] }} / {{ $goal['goal'] }}</span>
                        </div>
                        <x-dashboard.meter class="mt-2" :value="$goal['value']" :max="$goal['goal']" :expected="$plan->status() === 'active' ? $plan->expectedProgress() : null" />
                        <flux:text size="sm" class="mt-2">{{ __("goals.hints.{$figure}") }}</flux:text>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-[3fr_2fr]">
        {{-- Latest messages --}}
        <div class="{{ $card }}">
            <div class="flex items-center justify-between gap-4">
                <flux:heading size="lg" level="2">{{ __('Latest messages') }}</flux:heading>
                <flux:link :href="route('dashboard.messages.index')" wire:navigate class="text-sm">{{ __('View all') }} →</flux:link>
            </div>

            @if ($latestMessages->isEmpty())
                <flux:text class="mt-4">{{ __('There are no messages yet.') }}</flux:text>
            @else
                <flux:table class="mt-2">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Sender') }}</flux:table.column>
                        <flux:table.column>{{ __('Topic') }}</flux:table.column>
                        <flux:table.column>{{ __('Received') }}</flux:table.column>
                        <flux:table.column>{{ __('Status') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($latestMessages as $latestMessage)
                            <flux:table.row :key="'message-'.$latestMessage->id">
                                <flux:table.cell>
                                    <div class="font-medium text-navy dark:text-white">{{ $latestMessage->name }}</div>
                                    <div class="text-xs">{{ ucfirst(__("emails.received.locales.{$latestMessage->locale}")) }}</div>
                                </flux:table.cell>
                                <flux:table.cell>{{ $latestMessage->topic->shortLabel() }}</flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap">{{ $this->receivedAt($latestMessage) }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($latestMessage->isForwardedToAraguaneyCode())
                                        <flux:badge size="sm" color="zinc" icon="check">{{ __('Sent to AraguaneyCode') }}</flux:badge>
                                    @elseif ($latestMessage->isResponded())
                                        <flux:badge size="sm" color="zinc" icon="check">{{ __('Replied in :hours h', ['hours' => $latestMessage->responseHours()]) }}</flux:badge>
                                    @elseif ($latestMessage->replyHoursLeft() > 0)
                                        <flux:badge size="sm" color="sky" variant="solid">{{ __('New · :hours h left', ['hours' => $latestMessage->replyHoursLeft()]) }}</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="red">{{ __('Reply overdue') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>

        {{-- Top pages --}}
        <div class="{{ $card }}">
            <flux:heading size="lg" level="2">{{ __('Most viewed pages') }}</flux:heading>

            @if ($topPages === null)
                <flux:text class="mt-4">{{ __('Connect Google Analytics to see the pages.') }}</flux:text>
            @elseif ($topPages === [])
                <flux:text class="mt-4">{{ __('No visits in this period.') }}</flux:text>
            @else
                <flux:table class="mt-2">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Page') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Visits') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($topPages as $page)
                            <flux:table.row :key="'page-'.$page['path']">
                                <flux:table.cell class="max-w-56 truncate font-mono text-xs">{{ $page['path'] }}</flux:table.cell>
                                <flux:table.cell align="end">{{ $page['views'] }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>
    </div>

    {{-- Content performance --}}
    <div class="{{ $card }}">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <flux:heading size="lg" level="2">{{ __('Content performance') }}</flux:heading>
            <flux:text size="sm">{{ __('Own blog · Medium is measured separately') }}</flux:text>
        </div>

        @if ($content === [])
            <flux:text class="mt-4">{{ __('There are no published articles yet.') }}</flux:text>
        @else
            <flux:table class="mt-2">
                <flux:table.columns>
                    <flux:table.column>{{ __('Article') }}</flux:table.column>
                    <flux:table.column>{{ __('Pillar') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Visits') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Average reading') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Clicks to AraguaneyCode') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($content as $row)
                        <flux:table.row :key="'article-'.$row['article']->id">
                            <flux:table.cell class="font-medium text-navy dark:text-white">{{ $row['article']->title }}</flux:table.cell>
                            <flux:table.cell>{{ $row['article']->category->label() }}</flux:table.cell>
                            <flux:table.cell align="end">{{ $row['views'] ?? '—' }}</flux:table.cell>
                            <flux:table.cell align="end" class="whitespace-nowrap">{{ $row['seconds'] === null ? '—' : BrandStatistics::formatDuration($row['seconds']) }}</flux:table.cell>
                            <flux:table.cell align="end">{{ $row['clicks'] ?? '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </div>

    {{-- Reports --}}
    <div class="rounded-xl bg-navy p-6 text-white">
        <h2 class="font-display text-lg font-bold">{{ __('Reports') }}</h2>
        <p class="mt-1 max-w-2xl text-sm text-mist">{{ __('The monthly report reaches :email on the first Sunday of each month, with these figures against the plan goals.', ['email' => config('site.email')]) }}</p>

        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('dashboard.reports.monthly', $statistics->period->reportMonth()) }}" class="inline-flex items-center rounded-btn bg-ice px-4 py-2 text-sm font-medium text-navy hover:bg-mist">{{ __('Download monthly report (PDF)') }}</a>
            <a href="{{ route('dashboard.reports.messages', $this->exportFilters()) }}" class="inline-flex items-center rounded-btn border border-ice px-4 py-2 text-sm font-medium text-ice hover:bg-ice/10">{{ __('Export messages (CSV)') }}</a>
            @if ($statistics->hasAnalytics())
                <a href="{{ route('dashboard.reports.visits', $this->exportFilters()) }}" class="inline-flex items-center rounded-btn border border-ice px-4 py-2 text-sm font-medium text-ice hover:bg-ice/10">{{ __('Export visits (CSV)') }}</a>
            @endif
        </div>
    </div>
</section>
