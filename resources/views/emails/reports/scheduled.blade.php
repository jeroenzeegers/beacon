<x-mail::message>
# {{ $data['type'] ?? 'Report' }}

**Team:** {{ $data['team'] }}
**Period:** {{ $data['period']['start'] }} - {{ $data['period']['end'] }}

---

@if(isset($data['summary']))
## Summary

| Metric | Value |
|:-------|------:|
| Total Monitors | {{ $data['summary']['total_monitors'] }} |
| Monitors Up | {{ $data['summary']['monitors_up'] }} |
| Monitors Down | {{ $data['summary']['monitors_down'] }} |
| Total Checks | {{ number_format($data['summary']['total_checks']) }} |
| Overall Uptime | {{ $data['summary']['overall_uptime'] }}% |
| Avg Response Time | {{ $data['summary']['avg_response_time'] }}ms |
| Incidents | {{ $data['summary']['incidents_count'] }} |
@endif

@if(isset($data['sla_target']))
## SLA Summary

**Target SLA:** {{ $data['sla_target'] }}%
**Overall Uptime:** {{ $data['overall_uptime'] }}%
**SLA Met:** {{ $data['overall_sla_met'] ? '✅ Yes' : '❌ No' }}
@endif

---

## Monitor Details

@if(!empty($data['monitors']))
| Monitor | Type | Status/Uptime | Avg Response |
|:--------|:-----|:--------------|-------------:|
@foreach($data['monitors'] as $monitor)
| {{ $monitor['name'] }} | {{ strtoupper($monitor['type']) }} | {{ $monitor['uptime'] ?? $monitor['uptime_percentage'] }}% | {{ $monitor['avg_response_time'] }}ms |
@endforeach
@else
No monitors configured.
@endif

---

<x-mail::button :url="route('dashboard')">
View Dashboard
</x-mail::button>

<small>Generated at {{ $data['generated_at'] }}</small>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
