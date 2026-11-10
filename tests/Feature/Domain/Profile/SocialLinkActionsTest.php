<?php

use App\Domain\Profile\Actions\CreateSocialLinkAction;
use App\Domain\Profile\Actions\DeleteSocialLinkAction;
use App\Domain\Profile\Actions\ReorderSocialLinksAction;
use App\Domain\Profile\Actions\UpdateSocialLinkAction;
use App\Domain\Profile\DataTransferObjects\SocialLinkData;
use App\Domain\Profile\Enums\SocialPlatform;
use App\Domain\Profile\Events\ProfileChanged;
use App\Domain\Profile\Models\SocialLink;
use Illuminate\Support\Facades\Event;

test('email links get a mailto prefix', function () {
    $data = SocialLinkData::fromArray(['platform' => 'email', 'url' => 'me@example.com']);

    expect($data->url)->toBe('mailto:me@example.com')
        ->and(SocialLinkData::fromArray(['platform' => 'email', 'url' => 'mailto:me@example.com'])->url)
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
