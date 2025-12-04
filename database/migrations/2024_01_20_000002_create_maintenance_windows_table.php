<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_windows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly
            $table->json('recurrence_config')->nullable();
            $table->boolean('suppress_alerts')->default(true);
            $table->boolean('show_on_status_page')->default(true);
            $table->string('status')->default('scheduled'); // scheduled, active, completed, cancelled
            $table->timestamps();

            $table->index(['team_id', 'status']);
            $table->index(['starts_at', 'ends_at']);
        });

        Schema::create('maintenance_window_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_window_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monitor_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['maintenance_window_id', 'monitor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_window_monitors');
        Schema::dropIfExists('maintenance_windows');
    }
};
