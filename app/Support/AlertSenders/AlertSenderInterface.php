<?php

declare(strict_types=1);

namespace App\Support\AlertSenders;

use App\Models\AlertChannel;
use App\Models\Monitor;

interface AlertSenderInterface
{
    public function send(AlertChannel $channel, Monitor $monitor, string $trigger, string $message): bool;

    public function supports(string $type): bool;
}
