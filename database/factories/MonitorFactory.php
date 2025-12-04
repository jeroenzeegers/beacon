<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Monitor;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Monitor>
 */
class MonitorFactory extends Factory
{
    protected $model = Monitor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->domainWord().' Monitor',
            'type' => Monitor::TYPE_HTTPS,
            'target' => fake()->domainName(),
            'port' => 443,
            'check_interval' => 60,
            'timeout' => 30,
            'is_active' => true,
            'status' => Monitor::STATUS_UNKNOWN,
            'consecutive_failures' => 0,
            'failure_threshold' => 3,
        ];
    }

    /**
     * Configure the monitor for SSL expiry checking.
     */
    public function sslExpiry(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Monitor::TYPE_SSL_EXPIRY,
            'ssl_options' => [
                'warning_days' => 30,
                'critical_days' => 7,
            ],
        ]);
    }

    /**
     * Configure the monitor for HTTP checking.
     */
    public function http(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Monitor::TYPE_HTTP,
            'port' => 80,
        ]);
    }

    /**
     * Configure the monitor for TCP checking.
     */
    public function tcp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Monitor::TYPE_TCP,
        ]);
    }

    /**
     * Configure the monitor for ping checking.
     */
    public function ping(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Monitor::TYPE_PING,
            'port' => null,
        ]);
    }

    /**
     * Set the monitor as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set the monitor status as up.
     */
    public function up(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Monitor::STATUS_UP,
        ]);
    }

    /**
     * Set the monitor status as down.
     */
    public function down(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Monitor::STATUS_DOWN,
        ]);
    }
}
