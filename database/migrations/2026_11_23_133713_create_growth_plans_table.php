<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Personal brand plans (usually 90 days), one after another, each with its own goals.
     * Goals are totals for the whole plan, except `visits_per_month`.
     */
    public function up(): void
    {
        Schema::create('growth_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('starts_on')->unique();
            $table->unsignedSmallInteger('days')->default(90);
            $table->unsignedInteger('visits_per_month')->default(0);
            $table->unsignedInteger('araguaneycode_clicks')->default(0);
            $table->unsignedInteger('business_conversations')->default(0);
            $table->unsignedInteger('publications')->default(0);
            $table->unsignedInteger('linkedin_followers')->default(0);
            $table->unsignedInteger('talks')->default(0);
            $table->unsignedInteger('applications')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('growth_plans');
    }
};
