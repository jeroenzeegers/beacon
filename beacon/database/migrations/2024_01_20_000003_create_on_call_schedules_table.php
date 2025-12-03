<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('on_call_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('timezone')->default('UTC');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['team_id', 'is_active']);
        });

        Schema::create('on_call_rotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('on_call_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_override')->default(false);
            $table->timestamps();

            $table->index(['on_call_schedule_id', 'starts_at', 'ends_at']);
        });

        Schema::create('escalation_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('repeat_count')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('escalation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escalation_policy_id')->constrained()->cascadeOnDelete();
            $table->integer('level')->default(1);
            $table->integer('delay_minutes')->default(5);
            $table->string('target_type'); // user, on_call_schedule, alert_channel
            $table->unsignedBigInteger('target_id');
            $table->timestamps();

            $table->index(['escalation_policy_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_rules');
        Schema::dropIfExists('escalation_policies');
        Schema::dropIfExists('on_call_rotations');
        Schema::dropIfExists('on_call_schedules');
    }
};
