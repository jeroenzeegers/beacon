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
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type'); // http, https, tcp, ping, ssl_expiry
            $table->string('target'); // URL, hostname, IP address
            $table->integer('port')->nullable();
            $table->integer('check_interval')->default(300); // seconds
            $table->integer('timeout')->default(30); // seconds
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('unknown'); // up, down, degraded, unknown
            $table->timestamp('last_check_at')->nullable();
            $table->timestamp('last_status_change_at')->nullable();
            $table->integer('consecutive_failures')->default(0);
            $table->integer('failure_threshold')->default(3); // checks before marking down
            $table->json('http_options')->nullable(); // method, headers, body, expected_status, expected_body
            $table->json('ssl_options')->nullable(); // warning_days, critical_days
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'is_active']);
            $table->index(['team_id', 'status']);
            $table->index(['is_active', 'last_check_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
