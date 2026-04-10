<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pages — Statik/düzenlenebilir sayfalar (KVKK, Çerez Politikası, Hakkımızda
 * ek bloklar, Vizyon, Misyon vb.). Slug ile /sayfa/{slug} altında render edilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 160)->unique();
            $table->string('title', 240);
            $table->longText('body')->nullable();
            $table->string('meta_title', 240)->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->boolean('is_system')->default(false)->comment('Silinemez sistem sayfası (KVKK, Çerez)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
