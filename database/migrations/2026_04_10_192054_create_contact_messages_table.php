<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ContactMessages — İletişim formundan gelen talepler.
 *
 * `subject` iki tür: "iletisim" (genel) veya "gorusme_talebi" (randevu).
 * `ip_address` ve `user_agent` brute-force/spam analizi için loglanır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('email', 200);
            $table->string('phone', 40)->nullable();
            $table->string('subject_type', 32)->default('iletisim')->index();
            $table->string('subject', 240)->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
