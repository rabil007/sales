<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value', 'label', 'group'])]
class CompanySetting extends Model
{
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
}
