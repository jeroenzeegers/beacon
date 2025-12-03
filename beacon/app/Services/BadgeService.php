<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Monitor;

class BadgeService
{
    public function generateUptimeBadge(Monitor $monitor, string $style = 'flat'): string
    {
        $uptime = $monitor->getUptimePercentage(30);
        $color = $this->getColorForUptime($uptime);
        $label = urlencode($monitor->name);
        $value = $uptime.'%';

        return $this->generateSvg($label, $value, $color, $style);
    }

    public function generateStatusBadge(Monitor $monitor, string $style = 'flat'): string
    {
        $status = $monitor->status;
        $color = $this->getColorForStatus($status);
        $label = urlencode($monitor->name);
        $value = ucfirst($status);

        return $this->generateSvg($label, $value, $color, $style);
    }

    public function generateResponseTimeBadge(Monitor $monitor, string $style = 'flat'): string
    {
        $responseTime = $monitor->getAverageResponseTime(7) ?? 0;
        $color = $this->getColorForResponseTime($responseTime);
        $label = urlencode($monitor->name);
        $value = round($responseTime).'ms';

        return $this->generateSvg($label, $value, $color, $style);
    }

    private function generateSvg(string $label, string $value, string $color, string $style): string
    {
        $labelWidth = strlen(urldecode($label)) * 7 + 10;
        $valueWidth = strlen($value) * 7 + 10;
        $totalWidth = $labelWidth + $valueWidth;

        $labelColor = '#555';
        $textColor = '#fff';

        if ($style === 'flat-square') {
            $radius = 0;
        } else {
            $radius = 3;
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$totalWidth}" height="20" role="img" aria-label="{$label}: {$value}">
  <title>{$label}: {$value}</title>
  <linearGradient id="s" x2="0" y2="100%">
    <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
    <stop offset="1" stop-opacity=".1"/>
  </linearGradient>
  <clipPath id="r">
    <rect width="{$totalWidth}" height="20" rx="{$radius}" fill="#fff"/>
  </clipPath>
  <g clip-path="url(#r)">
    <rect width="{$labelWidth}" height="20" fill="{$labelColor}"/>
    <rect x="{$labelWidth}" width="{$valueWidth}" height="20" fill="{$color}"/>
    <rect width="{$totalWidth}" height="20" fill="url(#s)"/>
  </g>
  <g fill="{$textColor}" text-anchor="middle" font-family="Verdana,Geneva,DejaVu Sans,sans-serif" text-rendering="geometricPrecision" font-size="11">
    <text aria-hidden="true" x="{$this->getCenterX($labelWidth, 0)}" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="{$this->getTextLength($labelWidth)}">{$label}</text>
    <text x="{$this->getCenterX($labelWidth, 0)}" y="140" transform="scale(.1)" fill="{$textColor}" textLength="{$this->getTextLength($labelWidth)}">{$label}</text>
    <text aria-hidden="true" x="{$this->getCenterX($valueWidth, $labelWidth)}" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="{$this->getTextLength($valueWidth)}">{$value}</text>
    <text x="{$this->getCenterX($valueWidth, $labelWidth)}" y="140" transform="scale(.1)" fill="{$textColor}" textLength="{$this->getTextLength($valueWidth)}">{$value}</text>
  </g>
</svg>
SVG;
    }

    private function getCenterX(int $width, int $offset): int
    {
        return ($offset * 10) + (($width * 10) / 2);
    }

    private function getTextLength(int $width): int
    {
        return ($width - 10) * 10;
    }

    private function getColorForUptime(float $uptime): string
    {
        if ($uptime >= 99.9) {
            return '#4c1';
        }      // Bright green
        if ($uptime >= 99.0) {
            return '#97ca00';
        }   // Green
        if ($uptime >= 95.0) {
            return '#dfb317';
        }   // Yellow
        if ($uptime >= 90.0) {
            return '#fe7d37';
        }   // Orange

        return '#e05d44';                         // Red
    }

    private function getColorForStatus(string $status): string
    {
        return match ($status) {
            'up' => '#4c1',
            'degraded' => '#dfb317',
            'down' => '#e05d44',
            default => '#9f9f9f',
        };
    }

    private function getColorForResponseTime(float $ms): string
    {
        if ($ms <= 200) {
            return '#4c1';
        }
        if ($ms <= 500) {
            return '#97ca00';
        }
        if ($ms <= 1000) {
            return '#dfb317';
        }
        if ($ms <= 2000) {
            return '#fe7d37';
        }

        return '#e05d44';
    }
}
