<?php

declare(strict_types=1);

namespace App\Domains\Projects\Support;

/**
 * Converts between the indented text the CMS edits and the JSON tree the site renders.
 *
 * Text format, one entry per line, nesting by indentation (spaces or tabs):
 *
 *     app/                 # trailing slash = folder
 *       Domain/  # a note after a hash becomes a comment
 *     composer.json
 *
 * @phpstan-type FolderNode array{name: string, note?: string, children?: list<array<string, mixed>>}
 */
final class FolderTree
{
    public const MAX_LINES = 200;

    /**
     * @return list<FolderNode>
     */
    public static function parse(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        /** @var list<array{depth: int, node: FolderNode}> $flat */
        $flat = [];
        $lines = array_slice(preg_split('/\R/', $text) ?: [], 0, self::MAX_LINES);

        foreach ($lines as $line) {
            $line = rtrim(str_replace("\t", '    ', $line));

            if (trim($line) === '') {
                continue;
            }

            $depth = strlen($line) - strlen(ltrim($line));
            [$name, $note] = array_pad(explode('#', ltrim($line), 2), 2, null);
            $name = trim($name);

            if ($name === '') {
                continue;
            }

            $node = ['name' => rtrim($name, '/')];

            if ($note !== null && trim($note) !== '') {
                $node['note'] = trim($note);
            }

            if (str_ends_with($name, '/')) {
                $node['children'] = [];
            }

            $flat[] = ['depth' => $depth, 'node' => $node];
        }

        return self::nest($flat);
    }

    /**
     * @param  list<array<string, mixed>>  $nodes
     */
    public static function format(array $nodes, int $level = 0): string
    {
        $lines = [];

        foreach ($nodes as $node) {
            $children = $node['children'] ?? null;
            $line = str_repeat('  ', $level).($node['name'] ?? '').(is_array($children) ? '/' : '');

            if (isset($node['note']) && $node['note'] !== '') {
                $line .= '  # '.$node['note'];
            }

            $lines[] = $line;

            if (is_array($children) && $children !== []) {
                /** @var list<array<string, mixed>> $children */
                $lines[] = self::format($children, $level + 1);
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Builds the hierarchy from (depth, node) pairs using a stack of open folders.
     *
     * @param  list<array{depth: int, node: FolderNode}>  $flat
     * @return list<FolderNode>
     */
    private static function nest(array $flat): array
    {
        $root = ['children' => []];
        /** @var list<array{depth: int, ref: array<string, mixed>}> $stack */
        $stack = [['depth' => -1, 'ref' => &$root]];

        foreach ($flat as $entry) {
            while (count($stack) > 1 && $stack[count($stack) - 1]['depth'] >= $entry['depth']) {
                array_pop($stack);
            }

            $parent = &$stack[count($stack) - 1]['ref'];
            $parent['children'] ??= [];
            $parent['children'][] = $entry['node'];
            $index = array_key_last($parent['children']);

            if (array_key_exists('children', $entry['node'])) {
                $stack[] = ['depth' => $entry['depth'], 'ref' => &$parent['children'][$index]];
            }

            unset($parent);
        }

        /** @var list<FolderNode> */
        return $root['children'];
    }
}
