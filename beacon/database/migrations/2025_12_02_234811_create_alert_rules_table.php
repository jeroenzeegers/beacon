<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monitor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('trigger'); // monitor_down, monitor_up, monitor_degraded, ssl_expiring, response_slow
            $table->json('conditions')->nullable(); // additional conditions like threshold, duration
            $table->integer('cooldown_minutes')->default(15); // prevent alert storms
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['team_id', 'is_active']);
            $table->index(['monitor_id', 'is_active']);
        });

        // Pivot table for alert rule -> channel assignments
        Schema::create('alert_rule_channel', function (Blueprint $table) {
            $table->foreignId('alert_rule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alert_channel_id')->constrained()->cascadeOnDelete();
            $table->primary(['alert_rule_id', 'alert_channel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_rule_channel');
        Schema::dropIfExists('alert_rules');
    }
};
