<?php

namespace App\Services;

use App\Enums\ContactTopic;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\GrowthPlan;
use App\Models\MonthlyMetric;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use Throwable;

/**
 * Every figure of the personal brand dashboard and the monthly report, for one period and language.
 *
 * Messages come from the database, plan goals from `growth_plans`, figures entered by hand from the
 * monthly review (`monthly_metrics`) and visits, sources, pages, resume downloads and AraguaneyCode
 * clicks from Google Analytics 4. GA4 figures are null when it is not configured or does not answer.
 * The plan is the one of the period (a whole plan, or the plan running in that month), else the current one.
 */
class BrandStatistics
{
    /**
     * GA4 event sent by every link to araguaneycode.com (see resources/js/app.js).
     */
    public const ARAGUANEYCODE_CLICK_EVENT = 'araguaneycode_click';

    /**
     * GA4 event sent by every resume download link (see resources/js/app.js), with the visitor's country.
     */
    public const RESUME_DOWNLOAD_EVENT = 'resume_download';

    /**
     * GA4 custom dimension (event scope) with the language of the downloaded resume.
     */
    public const RESUME_LOCALE_DIMENSION = 'customEvent:resume_locale';

    /**
     * GA4 custom dimension (event scope) with the site language of each hit.
     */
    public const LOCALE_DIMENSION = 'customEvent:site_locale';

    /**
     * Known traffic sources (GA4 `sessionSource`, lowercased) and the name the dashboard shows.
     */
    private const SOURCE_NAMES = [
        '(direct)' => 'Direct',
        'linkedin' => 'LinkedIn',
        'linkedin.com' => 'LinkedIn',
        'lnkd.in' => 'LinkedIn',
        'google' => 'Google',
        'instagram' => 'Instagram',
        'instagram.com' => 'Instagram',
        'l.instagram.com' => 'Instagram',
        'medium' => 'Medium',
        'medium.com' => 'Medium',
        'github' => 'GitHub',
        'github.com' => 'GitHub',
        'dev.to' => 'DEV',
        'bing' => 'Bing',
        'chatgpt.com' => 'ChatGPT',
    ];

    public readonly ?GrowthPlan $plan;

    /**
     * Whether the last GA4 request failed although it is configured.
     */
    public bool $analyticsFailed = false;

    /**
     * @param  string|null  $locale  "es", "en" or null for both languages.
     */
    public function __construct(
        public readonly StatsPeriod $period,
        public readonly ?string $locale = null,
        ?GrowthPlan $plan = null,
        private ?GoogleAnalytics $analytics = null,
    ) {
        $this->plan = $plan ?? $period->plan ?? GrowthPlan::current();
        $this->analytics ??= app(GoogleAnalytics::class);
    }

    /**
     * Whether GA4 figures can be shown.
     */
    public function hasAnalytics(): bool
    {
        return $this->analytics->isConfigured();
    }

    /**
     * Visits (GA4 sessions) in the period.
     */
    public function visits(): ?int
    {
        $rows = $this->report(['metrics' => [['name' => 'sessions']]]);

        return $rows === null ? null : (int) ($rows[0]['metrics'][0] ?? 0);
    }

    /**
     * A goal of the plan, or null when there is no plan.
     */
    public function goal(string $name): ?int
    {
        return $this->plan?->goal($name);
    }

    /**
     * The visits goal for the period: the monthly goal, scaled when the period is not one month.
     */
    public function visitsGoal(): ?int
    {
        $monthlyGoal = $this->goal('visits_per_month');

        if ($monthlyGoal === null || $this->period->isCalendarMonth()) {
            return $monthlyGoal;
        }

        return (int) round($monthlyGoal * $this->period->days() / 30);
    }

    /**
     * Clicks on links to AraguaneyCode in the period.
     */
    public function araguaneyCodeClicks(): ?int
    {
        $rows = $this->report([
            'metrics' => [['name' => 'eventCount']],
            'dimensionFilter' => $this->eventFilter(self::ARAGUANEYCODE_CLICK_EVENT),
        ]);

        return $rows === null ? null : (int) ($rows[0]['metrics'][0] ?? 0);
    }

    /**
     * Where visits come from, biggest first, with their share of all visits.
     *
     * @return list<array{source: string, visits: int, share: int}>|null
     */
    public function sources(int $limit = 6): ?array
    {
        $rows = $this->report([
            'dimensions' => [['name' => 'sessionSource']],
            'metrics' => [['name' => 'sessions']],
            'orderBys' => [['metric' => ['metricName' => 'sessions'], 'desc' => true]],
            'limit' => 100,
        ]);

        if ($rows === null) {
            return null;
        }

        $visitsBySource = [];

        foreach ($rows as $row) {
            $source = $this->sourceName($row['dimensions'][0]);
            $visitsBySource[$source] = ($visitsBySource[$source] ?? 0) + (int) $row['metrics'][0];
        }

        arsort($visitsBySource);
        $total = array_sum($visitsBySource);

        return array_values(collect($visitsBySource)
            ->take($limit)
            ->map(fn (int $visits, string $source): array => [
                'source' => $source,
                'visits' => $visits,
                'share' => $total > 0 ? (int) round($visits * 100 / $total) : 0,
            ])
            ->all());
    }

    /**
     * The most viewed pages in the period.
     *
     * @return list<array{path: string, views: int}>|null
     */
    public function topPages(int $limit = 6): ?array
    {
        $rows = $this->report([
            'dimensions' => [['name' => 'pagePath']],
            'metrics' => [['name' => 'screenPageViews']],
            'orderBys' => [['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]],
            'limit' => $limit,
        ]);

        return $rows === null ? null : array_map(fn (array $row): array => [
            'path' => $row['dimensions'][0],
            'views' => (int) $row['metrics'][0],
        ], $rows);
    }

    /**
     * Visits per week (weeks start on Monday) for the last given weeks, ending with the current one.
     * It does not depend on the period.
     *
     * @return list<array{week: CarbonImmutable, visits: int}>|null
     */
    public function weeklyVisits(int $weeks = 4): ?array
    {
        $firstWeek = Date::now()->startOfWeek(CarbonImmutable::MONDAY)->subWeeks($weeks - 1);

        $rows = $this->report([
            'dimensions' => [['name' => 'date']],
            'metrics' => [['name' => 'sessions']],
        ], $firstWeek, Date::now());

        if ($rows === null) {
            return null;
        }

        $visitsByWeek = [];

        for ($week = 0; $week < $weeks; $week++) {
            $visitsByWeek[$firstWeek->addWeeks($week)->toDateString()] = 0;
        }

        foreach ($rows as $row) {
            $week = CarbonImmutable::createFromFormat('!Ymd', $row['dimensions'][0])->startOfWeek(CarbonImmutable::MONDAY)->toDateString();

            if (isset($visitsByWeek[$week])) {
                $visitsByWeek[$week] += (int) $row['metrics'][0];
            }
        }

        return array_values(collect($visitsByWeek)
            ->map(fn (int $visits, string $week): array => ['week' => CarbonImmutable::parse($week), 'visits' => $visits])
            ->all());
    }

    /**
     * Visits per day in the period, for the CSV export.
     *
     * @return list<array{date: CarbonImmutable, visits: int, views: int}>|null
     */
    public function dailyVisits(): ?array
    {
        $rows = $this->report([
            'dimensions' => [['name' => 'date']],
            'metrics' => [['name' => 'sessions'], ['name' => 'screenPageViews']],
            'orderBys' => [['dimension' => ['dimensionName' => 'date']]],
        ]);

        return $rows === null ? null : array_map(fn (array $row): array => [
            'date' => CarbonImmutable::createFromFormat('!Ymd', $row['dimensions'][0]),
            'visits' => (int) $row['metrics'][0],
            'views' => (int) $row['metrics'][1],
        ], $rows);
    }

    /**
     * How each published blog article performs: views, average engagement time and clicks to AraguaneyCode.
     *
     * @return list<array{article: Article, views: int|null, seconds: int|null, clicks: int|null}>
     */
    public function contentPerformance(): array
    {
        $articles = Article::query()->published()->latestPublished()->get();

        if ($articles->isEmpty()) {
            return [];
        }

        $paths = $articles->mapWithKeys(fn (Article $article): array => [route('blog.show', $article, false) => $article]);
        $pathFilter = ['filter' => ['fieldName' => 'pagePath', 'inListFilter' => ['values' => $paths->keys()->all()]]];

        $views = $this->report([
            'dimensions' => [['name' => 'pagePath']],
            'metrics' => [['name' => 'screenPageViews'], ['name' => 'userEngagementDuration'], ['name' => 'activeUsers']],
            'dimensionFilter' => $pathFilter,
        ]);
        $clicks = $views === null ? null : $this->report([
            'dimensions' => [['name' => 'pagePath']],
            'metrics' => [['name' => 'eventCount']],
            'dimensionFilter' => ['andGroup' => ['expressions' => [$pathFilter, $this->eventFilter(self::ARAGUANEYCODE_CLICK_EVENT)]]],
        ]);

        $viewsByPath = collect($views ?? [])->keyBy(fn (array $row): string => $row['dimensions'][0]);
        $clicksByPath = collect($clicks ?? [])->mapWithKeys(fn (array $row): array => [$row['dimensions'][0] => (int) $row['metrics'][0]]);

        return array_values($paths
            ->map(function (Article $article, string $path) use ($views, $viewsByPath, $clicksByPath): array {
                $metrics = $viewsByPath->get($path)['metrics'] ?? [0, 0, 0];

                return [
                    'article' => $article,
                    'views' => $views === null ? null : (int) $metrics[0],
                    'seconds' => $views === null ? null : ($metrics[2] > 0 ? (int) round($metrics[1] / $metrics[2]) : 0),
                    'clicks' => $views === null ? null : ($clicksByPath[$path] ?? 0),
                ];
            })
            ->sortByDesc(fn (array $row): int => $row['views'] ?? 0)
            ->all());
    }

    /**
     * Contact messages received in the period, oldest first.
     *
     * @return Collection<int, ContactMessage>
     */
    public function messages(): Collection
    {
        return $this->messagesQuery()
            ->whereBetween('created_at', [$this->period->start, $this->period->end])
            ->oldest('id')
            ->get();
    }

    /**
     * Summary of the messages received in the period.
     *
     * @return array{total: int, handled_on_time: int, forwarded: int, by_topic: array<string, int>, by_locale: array<string, int>, average_response_hours: int|null}
     */
    public function messageSummary(): array
    {
        $messages = $this->messages();
        $responseHours = $messages->map(fn (ContactMessage $message): ?int => $message->responseHours())->filter(fn (?int $hours): bool => $hours !== null);

        return [
            'total' => $messages->count(),
            'handled_on_time' => $messages->filter(fn (ContactMessage $message): bool => $message->wasHandledOnTime())->count(),
            'forwarded' => $messages->filter(fn (ContactMessage $message): bool => $message->isForwardedToAraguaneyCode())->count(),
            'by_topic' => collect(ContactTopic::cases())
                ->mapWithKeys(fn (ContactTopic $topic): array => [$topic->value => $messages->where('topic', $topic)->count()])
                ->sortDesc()
                ->all(),
            'by_locale' => collect(config()->array('site.locales'))
                ->mapWithKeys(fn (string $locale): array => [$locale => $messages->where('locale', $locale)->count()])
                ->all(),
            'average_response_hours' => $responseHours->isEmpty() ? null : (int) round($responseHours->avg()),
        ];
    }

    /**
     * Business conversations: project inquiries, which go to AraguaneyCode.
     */
    public function businessConversations(): int
    {
        return $this->messagesQuery()
            ->whereBetween('created_at', [$this->period->start, $this->period->end])
            ->where('topic', ContactTopic::Project)
            ->count();
    }

    /**
     * The latest messages, whatever the period.
     *
     * @return Collection<int, ContactMessage>
     */
    public function latestMessages(int $limit = 5): Collection
    {
        return $this->messagesQuery()->latest('id')->limit($limit)->get();
    }

    /**
     * Resume downloads in the period from GA4, in total and per resume language.
     * Visitors that block Google Analytics are not counted.
     *
     * @return array{total: int, by_locale: array<string, int>}|null
     */
    public function resumeDownloads(): ?array
    {
        $rows = $this->report([
            'dimensions' => [['name' => self::RESUME_LOCALE_DIMENSION]],
            'metrics' => [['name' => 'eventCount']],
            'dimensionFilter' => $this->eventFilter(self::RESUME_DOWNLOAD_EVENT),
        ]);

        if ($rows === null) {
            return null;
        }

        $byLocale = array_fill_keys(config()->array('site.locales'), 0);

        foreach ($rows as $row) {
            if (array_key_exists($row['dimensions'][0], $byLocale)) {
                $byLocale[$row['dimensions'][0]] += (int) $row['metrics'][0];
            }
        }

        return ['total' => array_sum($byLocale), 'by_locale' => $byLocale];
    }

    /**
     * Countries the resume is downloaded from (GA4 geolocation), most downloads first.
     * Visitors that block Google Analytics are not counted.
     *
     * @return list<array{country: string, downloads: int}>|null
     */
    public function resumeDownloadsByCountry(int $limit = 5): ?array
    {
        $rows = $this->report([
            'dimensions' => [['name' => 'country']],
            'metrics' => [['name' => 'eventCount']],
            'dimensionFilter' => $this->eventFilter(self::RESUME_DOWNLOAD_EVENT),
            'orderBys' => [['metric' => ['metricName' => 'eventCount'], 'desc' => true]],
            'limit' => $limit,
        ]);

        return $rows === null ? null : array_map(fn (array $row): array => [
            'country' => in_array($row['dimensions'][0], ['', '(not set)'], true) ? __('Unknown country') : $row['dimensions'][0],
            'downloads' => (int) $row['metrics'][0],
        ], $rows);
    }

    /**
     * Plan figures entered by hand, added up over the whole plan, with their goals (empty without a plan).
     *
     * @return array<string, array{value: int, goal: int}>
     */
    public function manualGoals(): array
    {
        if ($this->plan === null) {
            return [];
        }

        $metrics = $this->plan->monthlyMetrics()->get();

        return collect(MonthlyMetric::FIGURES)
            ->mapWithKeys(fn (string $figure): array => [$figure => [
                'value' => (int) $metrics->sum($figure),
                'goal' => $this->plan->goal($figure),
            ]])
            ->all();
    }

    /**
     * A duration as the dashboard shows it, e.g. "4 min 05 s".
     */
    public static function formatDuration(int $seconds): string
    {
        return intdiv($seconds, 60).' min '.str_pad((string) ($seconds % 60), 2, '0', STR_PAD_LEFT).' s';
    }

    /**
     * Contact messages, limited to the chosen language.
     *
     * @return Builder<ContactMessage>
     */
    private function messagesQuery(): Builder
    {
        return ContactMessage::query()->when($this->locale, fn (Builder $query, string $locale) => $query->where('locale', $locale));
    }

    /**
     * Run a GA4 report for the period (or the given dates) and the chosen language.
     *
     * @param  array<string, mixed>  $request
     * @return list<array{dimensions: list<string>, metrics: list<float>}>|null
     */
    private function report(array $request, ?CarbonImmutable $from = null, ?CarbonImmutable $to = null): ?array
    {
        if (! $this->hasAnalytics()) {
            return null;
        }

        $from ??= $this->period->start;
        $to ??= $this->period->lastDayWithData();

        if ($from->gt($to)) {
            return [];
        }

        if ($this->locale !== null) {
            $localeFilter = ['filter' => ['fieldName' => self::LOCALE_DIMENSION, 'stringFilter' => ['value' => $this->locale]]];
            $request['dimensionFilter'] = isset($request['dimensionFilter'])
                ? ['andGroup' => ['expressions' => [$request['dimensionFilter'], $localeFilter]]]
                : $localeFilter;
        }

        $request['dateRanges'] = [['startDate' => $from->toDateString(), 'endDate' => $to->toDateString()]];

        try {
            return $this->analytics->runReport($request);
        } catch (Throwable $exception) {
            report($exception);
            $this->analyticsFailed = true;

            return null;
        }
    }

    /**
     * GA4 filter for one event name.
     *
     * @return array<string, mixed>
     */
    private function eventFilter(string $eventName): array
    {
        return ['filter' => ['fieldName' => 'eventName', 'stringFilter' => ['value' => $eventName]]];
    }

    /**
     * Readable name of a GA4 source ("linkedin.com" → "LinkedIn"); unknown sources stay as they are.
     */
    private function sourceName(string $source): string
    {
        $name = self::SOURCE_NAMES[strtolower($source)] ?? $source;

        return $name === 'Direct' ? __('Direct') : $name;
    }
}
