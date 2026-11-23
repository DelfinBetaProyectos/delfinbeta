<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Translatable columns are JSON objects keyed by locale: {"es": "...", "en": "..."}.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();

            $table->json('title');
            $table->json('summary');
            $table->json('label')->nullable();
            $table->string('image')->nullable();
            $table->json('role')->nullable();
            $table->json('period')->nullable();
            $table->json('stack')->nullable();
            $table->string('live_url')->nullable();
            $table->json('context');
            $table->json('problem');
            $table->json('built')->nullable();
            $table->json('built_images')->nullable();
            $table->json('stats')->nullable();
            $table->json('result');

            $table->timestamps();

            $table->index(['published_at', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
