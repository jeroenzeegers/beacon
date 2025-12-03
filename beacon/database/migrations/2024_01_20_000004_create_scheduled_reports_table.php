<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // daily_summary, weekly_summary, monthly_summary, sla_report
            $table->string('frequency'); // daily, weekly, monthly
            $table->string('day_of_week')->nullable(); // for weekly reports
            $table->integer('day_of_month')->nullable(); // for monthly reports
            $table->string('time')->default('09:00'); // HH:MM format
            $table->string('timezone')->default('UTC');
            $table->json('recipients'); // array of email addresses
            $table->json('config')->nullable(); // report-specific configuration
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('next_send_at')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'is_active']);
            $table->index('next_send_at');
        });

        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_report_id')->constrained()->cascadeOnDelete();
            $table->string('status'); // sent, failed
            $table->integer('recipients_count');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_logs');
        Schema::dropIfExists('scheduled_reports');
    }
};
