<?php

declare(strict_types=1);

namespace App\Support\Checkers;

use App\Models\Monitor;
use App\Support\CheckResult;

interface CheckerInterface
{
    public function check(Monitor $monitor): CheckResult;

    public function supports(string $type): bool;
}
