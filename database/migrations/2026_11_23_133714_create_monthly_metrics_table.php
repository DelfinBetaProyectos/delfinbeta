<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Figures the site cannot measure, entered by hand at the monthly review: one row per plan and month,
     * so a month shared by two plans (one ends, the next starts) keeps separate figures for each.
     */
    public function up(): void
    {
        Schema::create('monthly_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('growth_plan_id')->constrained()->cascadeOnDelete();
            $table->date('month');
            $table->unsignedInteger('publications')->default(0);
            $table->unsignedInteger('linkedin_followers')->default(0);
            $table->unsignedInteger('talks')->default(0);
            $table->unsignedInteger('applications')->default(0);
            $table->timestamps();

            $table->unique(['growth_plan_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_metrics');
    }
};
