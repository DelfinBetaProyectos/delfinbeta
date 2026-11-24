<?php

namespace App\Console\Commands;

use App\Mail\MonthlyReportMail;
use App\Services\MonthlyReport;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('reports:send-monthly {--month= : Month to report as Y-m (defaults to the previous month)}')]
#[Description('Email the monthly personal brand report (PDF) to the site address')]
class SendMonthlyReport extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $month = $this->option('month');

        if ($month !== null && ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $this->error('The month must look like 2026-11.');

            return self::FAILURE;
        }

        $report = $month === null ? MonthlyReport::forPreviousMonth() : new MonthlyReport($month);

        Mail::send(new MonthlyReportMail($report));

        $this->info("Report for {$report->month} sent to ".config('site.email').'.');

        return self::SUCCESS;
    }
}
