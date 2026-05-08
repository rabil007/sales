<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value', 'label', 'group'])]
class CompanySetting extends Model
{
    private const DEFAULT_LOGO = 'overseas-marine logo.png';

    /**
     * Allowed keys when updating settings through the authenticated settings form.
     * Prevents arbitrary key injection via crafted POST payloads.
     *
     * @var list<string>
     */
    public const MANAGEABLE_SETTING_KEYS = [
        'app_name',
        'app_logo_path',
        'company_name',
        'company_legal_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_website',
        'signatory_name',
        'signatory_role',
        'accom_single_rate',
        'accom_double_rate',
        'accom_events_rate',
        'transport_rate_1',
        'transport_rate_2',
        'transport_rate_3',
        'transport_rate_4',
        'transport_rate_5',
    ];

    /**
     * Retrieve a setting value by key with an optional default.
     */
    public static function get(string $key, string $default = ''): string
    {
        return (string) static::query()->where('key', $key)->value('value') ?: $default;
    }

    /**
     * Retrieve all settings as a flat key → value array.
     *
     * @return array<string, string>
     */
    public static function allKeyed(): array
    {
        return static::query()->pluck('value', 'key')->map(fn ($v) => (string) $v)->all();
    }

    public static function logoPath(): string
    {
        return self::get('app_logo_path', self::DEFAULT_LOGO);
    }

    public static function logoUrl(): string
    {
        $path = self::logoPath();

        if (str_starts_with($path, 'app-branding/')) {
            return asset('uploads/'.$path);
        }

        return asset('storage/'.$path);
    }

    public static function logoAbsolutePath(): ?string
    {
        $path = self::logoPath();

        $absolutePath = str_starts_with($path, 'app-branding/')
            ? public_path('uploads/'.$path)
            : storage_path('app/public/'.$path);

        return is_file($absolutePath) ? $absolutePath : null;
    }
}
