<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for getting started with basic monitoring.',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'sort_order' => 0,
                'limits' => [
                    'monitors' => 3,
                    'projects' => 1,
                    'team_members' => 1,
                    'check_interval_min' => 300, // 5 minutes
                    'retention_days' => 7,
                    'status_pages' => 1,
                    'alert_channels' => 2,
                    'api_access' => 0,
                    'sms_alerts' => 0,
                    'custom_domains' => 0,
                    'sla_reports' => 0,
                ],
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'For small teams with moderate monitoring needs.',
                'price_monthly' => 900, // €9
                'price_yearly' => 9000, // €90 (2 months free)
                'sort_order' => 1,
                'limits' => [
                    'monitors' => 10,
                    'projects' => 3,
                    'team_members' => 3,
                    'check_interval_min' => 120, // 2 minutes
                    'retention_days' => 30,
                    'status_pages' => 2,
                    'alert_channels' => 5,
                    'api_access' => 0,
                    'sms_alerts' => 0,
                    'custom_domains' => 0,
                    'sla_reports' => 0,
                ],
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For growing teams requiring advanced monitoring.',
                'price_monthly' => 2900, // €29
                'price_yearly' => 29000, // €290
                'sort_order' => 2,
                'limits' => [
                    'monitors' => 50,
                    'projects' => 10,
                    'team_members' => 10,
                    'check_interval_min' => 60, // 1 minute
                    'retention_days' => 90,
                    'status_pages' => 5,
                    'alert_channels' => 15,
                    'api_access' => 1,
                    'sms_alerts' => 50,
                    'custom_domains' => 1,
                    'sla_reports' => 0,
                ],
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'For large organizations with enterprise requirements.',
                'price_monthly' => 7900, // €79
                'price_yearly' => 79000, // €790
                'sort_order' => 3,
                'limits' => [
                    'monitors' => 200,
                    'projects' => -1, // unlimited
                    'team_members' => -1, // unlimited
                    'check_interval_min' => 30, // 30 seconds
                    'retention_days' => 365,
                    'status_pages' => -1, // unlimited
                    'alert_channels' => -1, // unlimited
                    'api_access' => 1,
                    'sms_alerts' => 200,
                    'custom_domains' => 1,
                    'sla_reports' => 1,
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $limits = $planData['limits'];
            unset($planData['limits']);

            $plan = Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );

            foreach ($limits as $feature => $value) {
                $plan->limits()->updateOrCreate(
                    ['feature' => $feature],
                    ['limit_value' => $value]
                );
            }
        }
    }
}
