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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monitor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('status'); // investigating, identified, monitoring, resolved
            $table->string('severity')->default('minor'); // minor, major, critical
            $table->text('description')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'started_at']);
            $table->index('status');
        });

        // Pivot table for incident -> monitor associations
        Schema::create('incident_monitor', function (Blueprint $table) {
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monitor_id')->constrained()->cascadeOnDelete();
            $table->primary(['incident_id', 'monitor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_monitor');
        Schema::dropIfExists('incidents');
    }
};
