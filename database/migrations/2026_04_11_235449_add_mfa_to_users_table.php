<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * users tablosuna Filament v5 Multi-Factor Authentication kolonları ekler.
 *
 * - app_authentication_secret: TOTP (Authenticator app) için şifrelenmiş
 *   secret. Filament Crypt::encryptString ile yazar.
 * - app_authentication_recovery_codes: Şifrelenmiş JSON dizi, recovery
 *   kodlar (kullanıcı authenticator'ını kaybederse).
 * - has_email_authentication: Boolean, kullanıcı email OTP'yi açtıysa true.
 *
 * Email OTP için ek kolon yok — kod cache'te tutulur, DB'ye yazılmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // TOTP (Authenticator app) — Google Authenticator, Authy, 1Password vb.
            $table->text('app_authentication_secret')->nullable()->after('password');
            $table->text('app_authentication_recovery_codes')->nullable()->after('app_authentication_secret');

            // Email OTP — kullanıcı bunu profil sayfasından aktif eder
            $table->boolean('has_email_authentication')->default(false)->after('app_authentication_recovery_codes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'app_authentication_secret',
                'app_authentication_recovery_codes',
                'has_email_authentication',
            ]);
        });
    }
};
