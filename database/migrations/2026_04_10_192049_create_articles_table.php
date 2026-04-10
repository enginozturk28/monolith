<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Articles — Makaleler / yazılar.
 *
 * Tek avukat (Ethem Kaan Loğoğlu) yazar olduğu için `author_name` statik
 * varsayılan alır, user_id foreign key opsiyonel bırakılır (ileride çok
 * yazar desteği eklenebilir).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_category_id')
                ->nullable()
                ->constrained('article_categories')
                ->nullOnDelete();
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('slug', 200)->unique();
            $table->string('title', 240);
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('meta_title', 240)->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('reading_time_minutes')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
