<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const SUBJECT_ILETISIM = 'iletisim';
    public const SUBJECT_GORUSME = 'gorusme_talebi';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject_type',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'read_at',
        'replied_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    public static function subjectTypes(): array
    {
        return [
            self::SUBJECT_ILETISIM => 'İletişim',
            self::SUBJECT_GORUSME => 'Görüşme Talebi',
        ];
    }
}
