<?php

use App\Domains\Projects\Support\FolderTree;

test('parses folders, files, notes and nesting', function () {
    $tree = FolderTree::parse(<<<'TREE'
        app/
          Domain/  # business logic
            Billing/
          Models
        composer.json  # deps
        TREE);

    expect($tree)->toBe([
        ['name' => 'app', 'children' => [
            ['name' => 'Domain', 'note' => 'business logic', 'children' => [
                ['name' => 'Billing', 'children' => []],
            ]],
            ['name' => 'Models'],
        ]],
        ['name' => 'composer.json', 'note' => 'deps'],
    ]);
});

test('treats tabs as indentation and ignores blank lines', function () {
    expect(FolderTree::parse("src/\n\n\tindex.ts\n"))->toBe([
        ['name' => 'src', 'children' => [['name' => 'index.ts']]],
    ]);
});

test('empty input gives an empty tree', function () {
    expect(FolderTree::parse(null))->toBe([])
        ->and(FolderTree::parse("  \n "))->toBe([]);
});

test('format is the inverse of parse', function () {
    $text = "app/\n  Domain/  # logic\n    Billing/\n  Models\ncomposer.json";

    expect(FolderTree::format(FolderTree::parse($text)))->toBe($text);
});

test('caps the number of lines', function () {
    $text = implode("\n", array_map(fn (int $i) => "file{$i}", range(1, FolderTree::MAX_LINES + 50)));

    expect(FolderTree::parse($text))->toHaveCount(FolderTree::MAX_LINES);
});
