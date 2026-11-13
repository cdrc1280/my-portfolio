<?php

use App\Domains\Profile\Actions\CreateSocialLinkAction;
use App\Domains\Profile\Actions\DeleteSocialLinkAction;
use App\Domains\Profile\Actions\ReorderSocialLinksAction;
use App\Domains\Profile\Actions\UpdateSocialLinkAction;
use App\Domains\Profile\Data\SocialLinkData;
use App\Domains\Profile\Enums\SocialPlatform;
use App\Domains\Profile\Events\ProfileChanged;
use App\Domains\Profile\Models\SocialLink;
use Illuminate\Support\Facades\Event;

test('email links get a mailto prefix', function () {
    $data = SocialLinkData::from(['platform' => 'email', 'url' => 'me@example.com']);

    expect($data->url)->toBe('mailto:me@example.com')
        ->and(SocialLinkData::from(['platform' => 'email', 'url' => 'mailto:me@example.com'])->url)
        ->toBe('mailto:me@example.com');
});

test('create, update, reorder and delete each announce a change', function () {
    Event::fake([ProfileChanged::class]);

    $link = app(CreateSocialLinkAction::class)->execute(new SocialLinkData(SocialPlatform::GitHub, 'https://github.com/a'));
    app(UpdateSocialLinkAction::class)->execute($link, new SocialLinkData(SocialPlatform::GitHub, 'https://github.com/b'));
    $other = SocialLink::factory()->create();
    app(ReorderSocialLinksAction::class)->execute([$other->id, $link->id]);

    expect($link->fresh())->url->toBe('https://github.com/b')->position->toBe(2);

    app(DeleteSocialLinkAction::class)->execute($link);
    $this->assertModelMissing($link);

    Event::assertDispatchedTimes(ProfileChanged::class, 4);
});
