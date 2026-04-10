<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Services — Faaliyet alanları (Ceza, Aile, Miras, vb. 12 alan).
 *
 * Her hizmet için ayrı bir detay sayfası render edilir (/faaliyet-alanlari/{slug}).
 * Filament panelinden yönetilir; ikon Lucide React icon adıdır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 160)->unique();
            $table->string('title', 160);
            $table->string('icon', 64)->nullable()->comment('Lucide React icon adı');
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
