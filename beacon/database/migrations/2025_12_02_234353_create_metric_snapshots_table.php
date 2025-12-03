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
        Schema::create('metric_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('metric_type'); // request_count, error_rate, response_time_avg, etc.
            $table->decimal('value', 20, 6);
            $table->json('dimensions')->nullable(); // additional labels/tags
            $table->string('aggregation_period')->default('minute'); // minute, hour, day
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['project_id', 'metric_type', 'recorded_at']);
            $table->index(['project_id', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metric_snapshots');
    }
};
