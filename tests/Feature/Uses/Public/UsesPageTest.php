<?php

use App\Domains\Uses\Models\UsesItem;
use Inertia\Testing\AssertableInertia as Assert;

test('uses page lists items in order', function () {
    $second = UsesItem::factory()->create(['position' => 2]);
    $first = UsesItem::factory()->create(['position' => 1]);

    $this->get(route('uses'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/uses')
            ->where('items.0.id', $first->id)
            ->where('items.1.id', $second->id));
});
