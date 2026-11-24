<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * The monthly personal brand report: the dashboard figures of one month, as a PDF.
 */
class MonthlyReport
{
    public readonly BrandStatistics $statistics;

    /**
     * @param  string  $month  The month as "Y-m".
     */
    public function __construct(public readonly string $month)
    {
        $this->statistics = new BrandStatistics(StatsPeriod::month(CarbonImmutable::createFromFormat('!Y-m', $month)));
    }

    /**
     * The report for the month before the current one, the one sent at each monthly review.
     */
    public static function forPreviousMonth(): self
    {
        return new self(now()->subMonthNoOverflow()->format('Y-m'));
    }

    /**
     * When the next report is sent (the monthly review): the first Sunday of this month if it has
     * not passed, else next month's.
     */
    public static function nextSendDate(): CarbonImmutable
    {
        $today = now()->startOfDay();
        $firstSunday = $today->firstOfMonth(CarbonInterface::SUNDAY);

        return $firstSunday->gte($today) ? $firstSunday : $today->addMonthNoOverflow()->firstOfMonth(CarbonInterface::SUNDAY);
    }

    /**
     * The PDF file contents.
     */
    public function pdf(): string
    {
        return Pdf::loadView('reports.monthly', ['report' => $this, 'statistics' => $this->statistics])
            ->setPaper('a4')
            ->output();
    }

    /**
     * File name of the PDF, e.g. "informe-delfinbeta-2026-11.pdf".
     */
    public function fileName(): string
    {
        return "informe-delfinbeta-{$this->month}.pdf";
    }

    /**
     * The month name, e.g. "noviembre de 2026", in the current locale.
     */
    public function monthName(): string
    {
        return $this->statistics->period->start->settings(['locale' => app()->getLocale()])->isoFormat(__('MMMM YYYY'));
    }
}
