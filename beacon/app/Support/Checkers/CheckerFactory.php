<?php

declare(strict_types=1);

namespace App\Support\Checkers;

use App\Models\Monitor;
use InvalidArgumentException;

class CheckerFactory
{
    private array $checkers = [];

    public function __construct()
    {
        $this->registerChecker(new HttpChecker);
        $this->registerChecker(new TcpChecker);
        $this->registerChecker(new PingChecker);
        $this->registerChecker(new SslExpiryChecker);
    }

    public function registerChecker(CheckerInterface $checker): void
    {
        $this->checkers[] = $checker;
    }

    public function getChecker(string $type): CheckerInterface
    {
        foreach ($this->checkers as $checker) {
            if ($checker->supports($type)) {
                return $checker;
            }
        }

        throw new InvalidArgumentException("No checker available for type: {$type}");
    }

    public function check(Monitor $monitor): \App\Support\CheckResult
    {
        $checker = $this->getChecker($monitor->type);

        return $checker->check($monitor);
    }
}
