<?php

use App\Domains\Uses\Actions\CreateUsesItemAction;
use App\Domains\Uses\Actions\DeleteUsesItemAction;
use App\Domains\Uses\Actions\ReorderUsesItemsAction;
use App\Domains\Uses\Actions\UpdateUsesItemAction;
use App\Domains\Uses\Data\UsesItemData;
use App\Domains\Uses\Models\UsesItem;

describe('uses items', function () {
    test('create, update, reorder and delete', function () {
        $item = app(CreateUsesItemAction::class)->execute(UsesItemData::from([
            'category' => ' Editor ', 'name' => 'VS Code', 'description' => '', 'url' => 'https://code.visualstudio.com',
        ]));
        expect($item)->category->toBe('Editor')->description->toBeNull();

        app(UpdateUsesItemAction::class)->execute($item, UsesItemData::from(['category' => 'Editor', 'name' => 'Zed']));
        expect($item->fresh())->name->toBe('Zed')->url->toBeNull();

        $other = UsesItem::factory()->create();
        app(ReorderUsesItemsAction::class)->execute([$other->id, $item->id]);
        expect($other->fresh()->position)->toBe(1);

        app(DeleteUsesItemAction::class)->execute($item);
        $this->assertModelMissing($item);
    });
});
