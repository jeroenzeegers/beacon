<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Models\ScheduledReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public ?int $reportId = null;
    public string $name = '';
    public string $type = 'weekly_summary';
    public string $frequency = 'weekly';
    public string $day_of_week = 'monday';
    public int $day_of_month = 1;
    public string $time = '09:00';
    public string $timezone = 'UTC';
    public string $recipients = '';
    public bool $is_active = true;

    protected array $timezones = [
        'UTC', 'Europe/Amsterdam', 'Europe/London', 'Europe/Paris', 'Europe/Berlin',
        'America/New_York', 'America/Chicago', 'America/Los_Angeles',
        'Asia/Tokyo', 'Asia/Shanghai', 'Australia/Sydney',
    ];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $report = ScheduledReport::where('team_id', Auth::user()->current_team_id)
                ->findOrFail($id);

            $this->reportId = $report->id;
            $this->name = $report->name;
            $this->type = $report->type;
            $this->frequency = $report->frequency;
            $this->day_of_week = $report->day_of_week ?? 'monday';
            $this->day_of_month = $report->day_of_month ?? 1;
            $this->time = $report->time;
            $this->timezone = $report->timezone;
            $this->recipients = implode(', ', $report->recipients);
            $this->is_active = $report->is_active;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:daily_summary,weekly_summary,monthly_summary,sla_report',
            'frequency' => 'required|in:daily,weekly,monthly',
            'day_of_week' => 'required_if:frequency,weekly',
            'day_of_month' => 'required_if:frequency,monthly|integer|min:1|max:28',
            'time' => 'required|date_format:H:i',
            'timezone' => 'required|string',
            'recipients' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $recipientsList = array_map('trim', explode(',', $this->recipients));
        $recipientsList = array_filter($recipientsList, fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL));

        if (empty($recipientsList)) {
            $this->addError('recipients', 'Please provide at least one valid email address.');
            return;
        }

        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'frequency' => $validated['frequency'],
            'day_of_week' => $validated['frequency'] === 'weekly' ? $validated['day_of_week'] : null,
            'day_of_month' => $validated['frequency'] === 'monthly' ? $validated['day_of_month'] : null,
            'time' => $validated['time'],
            'timezone' => $validated['timezone'],
            'recipients' => $recipientsList,
            'is_active' => $validated['is_active'],
        ];

        if ($this->reportId) {
            $report = ScheduledReport::where('team_id', Auth::user()->current_team_id)
                ->findOrFail($this->reportId);
            $report->update($data);
        } else {
            $report = ScheduledReport::create([
                ...$data,
                'team_id' => Auth::user()->current_team_id,
            ]);
        }

        $report->calculateNextSendAt();

        session()->flash('success', $this->reportId ? 'Report updated.' : 'Report scheduled.');
        $this->redirect(route('reports.index'));
    }

    public function render()
    {
        return view('livewire.reports.create', [
            'timezones' => $this->timezones,
            'isEdit' => (bool) $this->reportId,
            'reportTypes' => ScheduledReport::getAvailableTypes(),
        ])->layout('layouts.app', ['title' => $this->reportId ? 'Edit Report' : 'Schedule Report']);
    }
}
