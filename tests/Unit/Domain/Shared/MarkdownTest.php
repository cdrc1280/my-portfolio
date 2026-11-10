<?php

use App\Domain\Shared\Support\Markdown;

test('renders markdown to html', function () {
    expect(Markdown::toHtml('# Title'))->toContain('<h1>Title</h1>');
});

test('returns null for empty input', function (?string $input) {
    expect(Markdown::toHtml($input))->toBeNull();
})->with([null, '', "  \n "]);

test('strips raw html and unsafe links', function () {
    $html = Markdown::toHtml("<script>alert(1)</script>\n\n[x](javascript:alert(1))");

    expect($html)
        ->not->toContain('<script>')
        ->not->toContain('javascript:');
});

test('estimates reading time with a one minute floor', function () {
    expect(Markdown::readingTime('short'))->toBe(1)
        ->and(Markdown::readingTime(str_repeat('word ', 450)))->toBe(3);
});
