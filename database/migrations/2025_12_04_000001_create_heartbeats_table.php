<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heartbeats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('expected_interval')->default(60); // in minutes
            $table->integer('grace_period')->default(5); // in minutes
            $table->string('status')->default('pending'); // pending, healthy, late, missing
            $table->timestamp('last_ping_at')->nullable();
            $table->timestamp('next_expected_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'status']);
            $table->index('next_expected_at');
        });

        Schema::create('heartbeat_pings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('heartbeat_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('success'); // success, fail
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('pinged_at');
            $table->timestamps();

            $table->index(['heartbeat_id', 'pinged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heartbeat_pings');
        Schema::dropIfExists('heartbeats');
    }
};
