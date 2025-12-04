<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            // Keyword/Content monitoring
            $table->boolean('keyword_check_enabled')->default(false);
            $table->string('keyword_check_type')->nullable(); // contains, not_contains, regex
            $table->text('keyword_check_value')->nullable();

            // Response time alerts
            $table->boolean('response_time_alert_enabled')->default(false);
            $table->integer('response_time_threshold')->nullable(); // in ms

            // Escalation policy
            $table->foreignId('escalation_policy_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('alert_channels', function (Blueprint $table) {
            // Add Slack/Discord specific fields
            $table->string('webhook_url')->nullable();
            $table->string('slack_channel')->nullable();
            $table->string('discord_username')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropForeign(['escalation_policy_id']);
            $table->dropColumn([
                'keyword_check_enabled',
                'keyword_check_type',
                'keyword_check_value',
                'response_time_alert_enabled',
                'response_time_threshold',
                'escalation_policy_id',
            ]);
        });

        Schema::table('alert_channels', function (Blueprint $table) {
            $table->dropColumn(['webhook_url', 'slack_channel', 'discord_username']);
        });
    }
};
