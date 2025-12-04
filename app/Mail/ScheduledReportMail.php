<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ScheduledReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduledReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ScheduledReport $report,
        public array $data
    ) {}

    public function envelope(): Envelope
    {
        $typeName = match ($this->report->type) {
            ScheduledReport::TYPE_DAILY_SUMMARY => 'Daily Summary',
            ScheduledReport::TYPE_WEEKLY_SUMMARY => 'Weekly Summary',
            ScheduledReport::TYPE_MONTHLY_SUMMARY => 'Monthly Summary',
            ScheduledReport::TYPE_SLA_REPORT => 'SLA Report',
            default => 'Report',
        };

        return new Envelope(
            subject: "[Beacon] {$typeName} - {$this->report->team->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reports.scheduled',
            with: [
                'report' => $this->report,
                'data' => $this->data,
            ],
        );
    }
}
