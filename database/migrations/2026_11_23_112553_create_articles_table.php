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
     * An article without `published_at` is a draft; a future date schedules it.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();
            $table->string('category', 20);
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);

            $table->json('title');
            $table->json('summary');
            $table->json('body');
            $table->string('image')->nullable();
            $table->string('medium_url')->nullable();
            $table->string('dev_url')->nullable();

            $table->timestamps();

            $table->index(['published_at', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
