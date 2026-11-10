<?php

use App\Domain\Seo\ValueObjects\SitemapEntry;

test('accepts a valid entry', function () {
    expect(new SitemapEntry('https://example.com/a', priority: 0.8))->priority->toBe(0.8);
});

test('rejects invalid urls and priorities', function (string $url, float $priority) {
    expect(fn () => new SitemapEntry($url, priority: $priority))->toThrow(InvalidArgumentException::class);
})->with([
    ['not a url', 0.5],
    ['https://example.com', 1.5],
    ['https://example.com', -0.1],
]);
