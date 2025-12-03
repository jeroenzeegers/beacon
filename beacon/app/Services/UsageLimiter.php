<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Team;
use App\Models\UsageRecord;
use App\Support\PlanLimits;

class UsageLimiter
{
    /**
     * Check if team can create a new monitor.
     */
    public function canCreateMonitor(Team $team): bool
    {
        $limits = $team->getPlanLimits();
        $current = $team->monitors()->count();

        return $limits->isUnlimited('monitors') || $current < $limits->monitors;
    }

    /**
     * Check if team can create a new project.
     */
    public function canCreateProject(Team $team): bool
    {
        $limits = $team->getPlanLimits();
        $current = $team->projects()->count();

        return $limits->isUnlimited('projects') || $current < $limits->projects;
    }

    /**
     * Check if team can add a new member.
     */
    public function canAddTeamMember(Team $team): bool
    {
        $limits = $team->getPlanLimits();
        $current = $team->users()->count();

        return $limits->isUnlimited('team_members') || $current < $limits->team_members;
    }

    /**
     * Check if team can use a specific check interval.
     */
    public function canUseCheckInterval(Team $team, int $seconds): bool
    {
        $limits = $team->getPlanLimits();

        return $seconds >= $limits->check_interval_min;
    }

    /**
     * Check if team can create a new status page.
     */
    public function canCreateStatusPage(Team $team): bool
    {
        $limits = $team->getPlanLimits();
        $current = $team->statusPages()->count();

        return $limits->isUnlimited('status_pages') || $current < $limits->status_pages;
    }

    /**
     * Check if team can create a new alert channel.
     */
    public function canCreateAlertChannel(Team $team): bool
    {
        $limits = $team->getPlanLimits();
        $current = $team->alertChannels()->count();

        return $limits->isUnlimited('alert_channels') || $current < $limits->alert_channels;
    }

    /**
     * Check if team can send SMS alerts.
     */
    public function canSendSms(Team $team): bool
    {
        $limits = $team->getPlanLimits();

        if (!$limits->hasFeature('sms_alerts')) {
            return false;
        }

        if ($limits->isUnlimited('sms_alerts')) {
            return true;
        }

        $usedThisMonth = UsageRecord::forTeam($team->id)
            ->ofType('sms_sent')
            ->currentMonth()
            ->sum('quantity');

        return $usedThisMonth < $limits->sms_alerts;
    }

    /**
     * Check if team has API access.
     */
    public function hasApiAccess(Team $team): bool
    {
        return $team->getPlanLimits()->hasFeature('api_access');
    }

    /**
     * Check if team can use custom domains for status pages.
     */
    public function canUseCustomDomains(Team $team): bool
    {
        return $team->getPlanLimits()->hasFeature('custom_domains');
    }

    /**
     * Check if team has SLA reports feature.
     */
    public function hasSlaReports(Team $team): bool
    {
        return $team->getPlanLimits()->hasFeature('sla_reports');
    }

    /**
     * Get remaining limits for a team.
     */
    public function getRemainingLimits(Team $team): array
    {
        $limits = $team->getPlanLimits();

        $monitorsUsed = $team->monitors()->count();
        $projectsUsed = $team->projects()->count();
        $membersUsed = $team->users()->count();
        $statusPagesUsed = $team->statusPages()->count();
        $alertChannelsUsed = $team->alertChannels()->count();
        $smsUsedThisMonth = UsageRecord::forTeam($team->id)
            ->ofType('sms_sent')
            ->currentMonth()
            ->sum('quantity');

        return [
            'monitors' => [
                'used' => $monitorsUsed,
                'limit' => $limits->monitors,
                'remaining' => $limits->isUnlimited('monitors') ? -1 : max(0, $limits->monitors - $monitorsUsed),
                'unlimited' => $limits->isUnlimited('monitors'),
            ],
            'projects' => [
                'used' => $projectsUsed,
                'limit' => $limits->projects,
                'remaining' => $limits->isUnlimited('projects') ? -1 : max(0, $limits->projects - $projectsUsed),
                'unlimited' => $limits->isUnlimited('projects'),
            ],
            'team_members' => [
                'used' => $membersUsed,
                'limit' => $limits->team_members,
                'remaining' => $limits->isUnlimited('team_members') ? -1 : max(0, $limits->team_members - $membersUsed),
                'unlimited' => $limits->isUnlimited('team_members'),
            ],
            'status_pages' => [
                'used' => $statusPagesUsed,
                'limit' => $limits->status_pages,
                'remaining' => $limits->isUnlimited('status_pages') ? -1 : max(0, $limits->status_pages - $statusPagesUsed),
                'unlimited' => $limits->isUnlimited('status_pages'),
            ],
            'alert_channels' => [
                'used' => $alertChannelsUsed,
                'limit' => $limits->alert_channels,
                'remaining' => $limits->isUnlimited('alert_channels') ? -1 : max(0, $limits->alert_channels - $alertChannelsUsed),
                'unlimited' => $limits->isUnlimited('alert_channels'),
            ],
            'sms_this_month' => [
                'used' => $smsUsedThisMonth,
                'limit' => $limits->sms_alerts,
                'remaining' => $limits->isUnlimited('sms_alerts') ? -1 : max(0, $limits->sms_alerts - $smsUsedThisMonth),
                'unlimited' => $limits->isUnlimited('sms_alerts'),
            ],
            'check_interval_min' => $limits->check_interval_min,
            'retention_days' => $limits->retention_days,
            'api_access' => $limits->hasFeature('api_access'),
            'custom_domains' => $limits->hasFeature('custom_domains'),
            'sla_reports' => $limits->hasFeature('sla_reports'),
        ];
    }

    /**
     * Get the minimum allowed check interval for a team.
     */
    public function getMinCheckInterval(Team $team): int
    {
        return $team->getPlanLimits()->check_interval_min;
    }

    /**
     * Get the data retention period in days.
     */
    public function getRetentionDays(Team $team): int
    {
        return $team->getPlanLimits()->retention_days;
    }
}
