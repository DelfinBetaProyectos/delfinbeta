<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\BrandStatistics;
use App\Services\MonthlyReport;
use App\Services\StatsPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Downloads of the dashboard: the monthly PDF report and the CSV exports.
 */
class ReportController extends Controller
{
    /**
     * The monthly report of the given month ("Y-m") as a PDF.
     */
    public function monthly(string $month): Response
    {
        $report = new MonthlyReport($month);

        return response($report->pdf(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$report->fileName().'"',
        ]);
    }

    /**
     * Contact messages of the period (and language) as CSV.
     */
    public function messages(Request $request): StreamedResponse
    {
        $statistics = $this->statistics($request);

        return $this->csv("mensajes-{$statistics->period->slug()}.csv", [__('Date'), __('Name'), __('Email'), __('Topic'), __('Language'), __('Status'), __('Response time (hours)')],
            array_values($statistics->messages()->map(fn (ContactMessage $message): array => [
                $message->created_at->toDateTimeString(),
                $message->name,
                $message->email,
                $message->topic->label(),
                $message->locale,
                $this->messageStatus($message),
                $message->responseHours() ?? '',
            ])->all()));
    }

    /**
     * Visits per day of the period (and language) from Google Analytics, as CSV.
     */
    public function visits(Request $request): StreamedResponse
    {
        $statistics = $this->statistics($request);
        $days = $statistics->dailyVisits();

        abort_if($days === null, 503, __('Google Analytics is not available.'));

        return $this->csv("visitas-{$statistics->period->slug()}.csv", [__('Date'), __('Visits'), __('Page views')],
            array_map(fn (array $day): array => [$day['date']->toDateString(), $day['visits'], $day['views']], $days));
    }

    /**
     * Statistics for the `period` and `locale` query parameters (the dashboard filters).
     */
    private function statistics(Request $request): BrandStatistics
    {
        $locale = $request->string('locale')->toString();

        return new BrandStatistics(
            StatsPeriod::fromKey($request->string('period')->toString()),
            in_array($locale, config()->array('site.locales'), true) ? $locale : null,
        );
    }

    /**
     * Where a message stands, as written in the CSV.
     */
    private function messageStatus(ContactMessage $message): string
    {
        return match (true) {
            $message->isForwardedToAraguaneyCode() => __('Sent to AraguaneyCode'),
            $message->isResponded() => __('Replied'),
            default => __('Pending reply'),
        };
    }

    /**
     * Stream rows as a UTF-8 CSV file (with BOM so spreadsheets read the accents).
     *
     * @param  list<string>  $header
     * @param  list<list<string|int>>  $rows
     */
    private function csv(string $fileName, array $header, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\u{FEFF}");
            fputcsv($output, $header, escape: '');

            foreach ($rows as $row) {
                fputcsv($output, $row, escape: '');
            }

            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
