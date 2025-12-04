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
        Schema::create('status_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('primary_color')->default('#3B82F6');
            $table->boolean('is_public')->default(true);
            $table->boolean('show_uptime')->default(true);
            $table->boolean('show_response_time')->default(true);
            $table->integer('uptime_days_shown')->default(90);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'is_public']);
        });

        // Pivot table for status page -> monitor associations
        Schema::create('status_page_monitor', function (Blueprint $table) {
            $table->foreignId('status_page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monitor_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('display_name')->nullable();
            $table->primary(['status_page_id', 'monitor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_page_monitor');
        Schema::dropIfExists('status_pages');
    }
};
