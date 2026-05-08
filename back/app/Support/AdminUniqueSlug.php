<?php

namespace App\Support;

use Illuminate\Support\Str;

final class AdminUniqueSlug
{
    public static function forModel(string $modelClass, string $column, ?string $explicitSlug, string $nameFallback, ?int $exceptId = null): string
    {
        $raw = $explicitSlug !== null && $explicitSlug !== ''
            ? $explicitSlug
            : $nameFallback;

        $slug = Str::slug($raw);
        if ($slug === '') {
            $slug = 'n-'.Str::lower(Str::random(10));
        }

        return self::ensureUnique($modelClass, $column, $slug, $exceptId);
    }

    private static function ensureUnique(string $modelClass, string $column, string $base, ?int $exceptId): string
    {
        $candidate = $base;
        $n = 2;
        while ($modelClass::query()
            ->where($column, $candidate)
            ->when($exceptId !== null, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $candidate = $base.'-'.$n;
            $n++;
        }

        return $candidate;
    }
}
