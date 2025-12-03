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
        Schema::create('monitor_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitor_id')->constrained()->cascadeOnDelete();
            $table->string('status'); // up, down, degraded
            $table->integer('response_time')->nullable(); // milliseconds
            $table->integer('status_code')->nullable(); // HTTP status code
            $table->text('error_message')->nullable();
            $table->json('response_headers')->nullable();
            $table->integer('response_size')->nullable(); // bytes
            $table->json('ssl_info')->nullable(); // certificate details
            $table->json('dns_info')->nullable(); // DNS resolution details
            $table->string('checked_from')->nullable(); // region/location
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->index(['monitor_id', 'checked_at']);
            $table->index(['monitor_id', 'status']);
            $table->index('checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_checks');
    }
};
