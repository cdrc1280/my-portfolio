<?php

use App\Shared\Support\Input;

test('normalizes nullable strings', function () {
    expect(Input::nullableString('  x '))->toBe('x')
        ->and(Input::nullableString('   '))->toBeNull()
        ->and(Input::nullableString(null))->toBeNull()
        ->and(Input::nullableString(5))->toBeNull();
});

test('cleans string and int lists', function () {
    expect(Input::stringList([' a ', '', null, 'b']))->toBe(['a', 'b'])
        ->and(Input::intList(['3', 4]))->toBe([3, 4]);
});

test('parses months to the first day', function () {
    expect(Input::month('2024-02')?->format('Y-m-d'))->toBe('2024-02-01')
        ->and(Input::month(''))->toBeNull()
        ->and(Input::month(null))->toBeNull();
});
