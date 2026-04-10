<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Settings tablosu — Filament Settings panellerinden yönetilen tüm key-value
 * ayarlar burada tutulur. `group` alanı panelde kategori ayırımı için, `type`
 * alanı frontend render'ı için (color, text, textarea, boolean, json, image).
 *
 * Hassas değerler (SMTP password, API key'ler, Turnstile secret) `encrypted`
 * true olarak işaretlenir ve Setting modelinde otomatik Crypt ile şifrelenir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 64)->index(); // site, theme, smtp, security, api_keys
            $table->string('key', 128);
            $table->longText('value')->nullable();
            $table->string('type', 32)->default('text'); // text, textarea, color, boolean, json, image, password
            $table->boolean('encrypted')->default(false);
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
