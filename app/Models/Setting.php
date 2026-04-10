<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Setting — Key-value ayar modeli (DB destekli).
 *
 * Hassas değerler (`encrypted = true`) otomatik olarak `Crypt::encryptString`
 * ile şifrelenir ve accessor'da decrypt edilir. Panelde düzenleme sırasında
 * decrypt edilmiş değer gösterilir, save'de yeniden encrypt olur.
 *
 * Ayrıca cache'li `Setting::value($group, $key)` helper'ı vardır.
 */
class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'encrypted',
        'label',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'encrypted' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        // Herhangi bir setting değiştiğinde site + theme cache'ini invalidate et
        static::saved(fn () => static::flushCaches());
        static::deleted(fn () => static::flushCaches());
    }

    public static function flushCaches(): void
    {
        Cache::forget('monolith.site_settings');
        Cache::forget('monolith.theme');
        Cache::forget('monolith.settings.all');
    }

    /**
     * Tüm ayarları grup → key → value olarak döner (cache'li).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function allGrouped(): array
    {
        return Cache::rememberForever('monolith.settings.all', function (): array {
            $rows = static::query()->get();

            $grouped = [];
            foreach ($rows as $row) {
                $grouped[$row->group][$row->key] = $row->value; // accessor decrypt eder
            }

            return $grouped;
        });
    }

    public static function value(string $group, string $key, mixed $default = null): mixed
    {
        return data_get(static::allGrouped(), "{$group}.{$key}", $default);
    }

    public static function set(string $group, string $key, mixed $value, array $attributes = []): self
    {
        /** @var self $setting */
        $setting = static::firstOrNew(['group' => $group, 'key' => $key]);

        foreach ($attributes as $k => $v) {
            if ($k === 'value') {
                continue;
            }
            $setting->{$k} = $v;
        }

        $setting->value = $value;
        $setting->save();

        return $setting;
    }

    /**
     * value accessor — encrypted ise decrypt eder, değilse ham döner.
     */
    public function getValueAttribute(mixed $raw): mixed
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if ($this->encrypted) {
            try {
                return Crypt::decryptString($raw);
            } catch (\Throwable) {
                return null; // şifreleme hatasında sessiz fail
            }
        }

        if ($this->type === 'json') {
            $decoded = json_decode($raw, true);

            return $decoded === null && json_last_error() !== JSON_ERROR_NONE ? $raw : $decoded;
        }

        if ($this->type === 'boolean') {
            return filter_var($raw, FILTER_VALIDATE_BOOLEAN);
        }

        return $raw;
    }

    /**
     * value mutator — encrypted ise encrypt eder, JSON ise encode eder.
     */
    public function setValueAttribute(mixed $value): void
    {
        if ($value === null) {
            $this->attributes['value'] = null;

            return;
        }

        if ($this->encrypted) {
            $this->attributes['value'] = Crypt::encryptString((string) $value);

            return;
        }

        if ($this->type === 'json' && ! is_string($value)) {
            $this->attributes['value'] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return;
        }

        if ($this->type === 'boolean') {
            $this->attributes['value'] = $value ? '1' : '0';

            return;
        }

        $this->attributes['value'] = (string) $value;
    }
}
