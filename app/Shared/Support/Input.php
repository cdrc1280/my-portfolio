<?php

declare(strict_types=1);

namespace App\Shared\Support;

use Carbon\CarbonImmutable;

/**
 * Normalizers for validated form input, used when building DTOs.
 */
final class Input
{
    public static function nullableString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }

    /**
     * A checkbox/switch value from a JSON or multipart form ("1", "0", "true", true, …).
     */
    public static function bool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * An optional id from a select: "" or null → null, numeric strings → int. Anything else is
     * passed through so validation can reject it.
     */
    public static function nullableInt(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : $value;
    }

    /**
     * Trims entries and drops empty ones (e.g. a textarea split by line).
     *
     * @return list<string>
     */
    public static function stringList(mixed $values): array
    {
        $values = array_map(fn (mixed $value): string => trim((string) $value), (array) $values);

        return array_values(array_filter($values, fn (string $value): bool => $value !== ''));
    }

    /**
     * @return list<int>
     */
    public static function intList(mixed $values): array
    {
        return array_values(array_map(intval(...), (array) $values));
    }

    /**
     * Parses a `YYYY-MM` month (from <input type="month">) to its first day.
     */
    public static function month(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return CarbonImmutable::createFromFormat('!Y-m', $value) ?: null;
    }
}
