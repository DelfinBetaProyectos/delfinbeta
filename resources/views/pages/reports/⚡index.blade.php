<?php

use App\Mail\MonthlyReportMail;
use App\Services\GoogleAnalytics;
use App\Models\GrowthPlan;
use App\Services\MonthlyReport;
use Carbon\CarbonImmutable;
use Flux\Flux;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Reports')] class extends Component {
    /**
     * Months with a report, newest first: from the month the first plan started (or this month) until now.
     *
     * @return list<CarbonImmutable>
     */
    #[Computed]
    public function months(): array
    {
        $currentMonth = now()->startOfMonth();
        $firstPlanStart = GrowthPlan::query()->min('starts_on');
        $months = [];

        for ($month = $firstPlanStart ? CarbonImmutable::parse($firstPlanStart)->startOfMonth()->min($currentMonth) : $currentMonth; $month->lte($currentMonth); $month = $month->addMonth()) {
            array_unshift($months, $month);
        }

        return $months;
    }

    /**
     * Whether the visits export is available.
     */
    #[Computed]
    public function hasAnalytics(): bool
    {
        return app(GoogleAnalytics::class)->isConfigured();
    }

    /**
     * Email the report of a month now, without waiting for the first Sunday.
     */
    public function send(string $month): void
    {
        $validMonths = array_map(fn (CarbonImmutable $date): string => $date->format('Y-m'), $this->months);

        abort_unless(in_array($month, $validMonths, true), 404);

        Mail::send(new MonthlyReportMail(new MonthlyReport($month)));

        Flux::toast(variant: 'success', text: __('Report sent to :email.', ['email' => config('site.email')]));
    }
}; ?>

<section class="w-full">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Reports') }}</flux:heading>
        <flux:subheading size="lg">{{ __('The monthly report reaches :email on the first Sunday of each month, with these figures against the plan goals.', ['email' => config('site.email')]) }}</flux:subheading>
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Month') }}</flux:table.column>
            <flux:table.column><span class="sr-only">{{ __('Actions') }}</span></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->months as $month)
                @php($monthKey = $month->format('Y-m'))
                <flux:table.row :key="'report-'.$monthKey">
                    <flux:table.cell class="font-medium text-zinc-800 capitalize dark:text-white">{{ $month->locale(app()->getLocale())->isoFormat('MMMM YYYY') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex flex-wrap justify-end gap-2">
                            <flux:button size="sm" variant="primary" icon="arrow-down-tray" :href="route('dashboard.reports.monthly', $monthKey)">{{ __('PDF') }}</flux:button>
                            <flux:button size="sm" variant="outline" :href="route('dashboard.reports.messages', ['period' => $monthKey])">{{ __('Messages (CSV)') }}</flux:button>
                            @if ($this->hasAnalytics)
                                <flux:button size="sm" variant="outline" :href="route('dashboard.reports.visits', ['period' => $monthKey])">{{ __('Visits (CSV)') }}</flux:button>
                            @endif
                            <flux:button size="sm" variant="ghost" icon="paper-airplane" wire:click="send('{{ $monthKey }}')">{{ __('Send now') }}</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</section>
