<?php

declare(strict_types=1);

namespace App\Shared\Support;

use Closure;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Turns a paginator into the `{ data, links, meta }` shape the React `Paginated<T>` type and the
 * Pagination component expect, mapping each model through a Resource's fromModel().
 */
final class Paginated
{
    /**
     * @template TModel
     * @template TResource
     *
     * @param  LengthAwarePaginator<int, TModel>  $paginator
     * @param  Closure(TModel): TResource  $map
     * @return array{data: list<TResource>, links: array{first: string|null, last: string|null, prev: string|null, next: string|null}, meta: array<string, mixed>}
     */
    public static function from(LengthAwarePaginator $paginator, Closure $map): array
    {
        return [
            'data' => array_values(array_map($map, $paginator->items())),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'links' => $paginator->linkCollection()->all(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        ];
    }
}
