<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\ScheduledReportMail;
use App\Models\ScheduledReport;
use App\Services\ReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendScheduledReport implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ScheduledReport $report
    ) {
        $this->onQueue('reports');
    }

    public function handle(ReportService $reportService): void
    {
        $team = $this->report->team;

        // Generate report data based on type
        $data = match ($this->report->type) {
            ScheduledReport::TYPE_DAILY_SUMMARY => $reportService->generateDailySummary($team, $this->report->config),
            ScheduledReport::TYPE_WEEKLY_SUMMARY => $reportService->generateWeeklySummary($team, $this->report->config),
            ScheduledReport::TYPE_MONTHLY_SUMMARY => $reportService->generateMonthlySummary($team, $this->report->config),
            ScheduledReport::TYPE_SLA_REPORT => $reportService->generateSlaReport($team, $this->report->config),
            default => [],
        };

        $recipients = $this->report->recipients;
        $sentCount = 0;
        $errors = [];

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new ScheduledReportMail($this->report, $data));
                $sentCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to send to {$email}: {$e->getMessage()}";
                Log::error('Failed to send scheduled report', [
                    'report_id' => $this->report->id,
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Log the report send
        $this->report->logs()->create([
            'status' => empty($errors) ? 'sent' : 'failed',
            'recipients_count' => $sentCount,
            'error_message' => ! empty($errors) ? implode("\n", $errors) : null,
            'sent_at' => now(),
        ]);

        // Update last sent and calculate next send
        $this->report->update(['last_sent_at' => now()]);
        $this->report->calculateNextSendAt();

        Log::info('Scheduled report processed', [
            'report_id' => $this->report->id,
            'type' => $this->report->type,
            'sent_count' => $sentCount,
            'total_recipients' => count($recipients),
        ]);
    }
}
