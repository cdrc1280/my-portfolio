<?php

declare(strict_types=1);

namespace App\Domains\Profile\Controllers\Admin;

use App\Domains\Profile\Actions\CreateSocialLinkAction;
use App\Domains\Profile\Actions\DeleteSocialLinkAction;
use App\Domains\Profile\Actions\ReorderSocialLinksAction;
use App\Domains\Profile\Actions\UpdateSocialLinkAction;
use App\Domains\Profile\Data\SocialLinkData;
use App\Domains\Profile\Models\SocialLink;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SocialLinkController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:create,'.SocialLink::class, only: ['store']),
            new Middleware('can:update,social_link', only: ['update']),
            new Middleware('can:reorder,'.SocialLink::class, only: ['reorder']),
            new Middleware('can:delete,social_link', only: ['destroy']),
        ];
    }

    public function store(SocialLinkData $data, CreateSocialLinkAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Link added.');

        return to_route('admin.profile.edit');
    }

    public function update(SocialLinkData $data, SocialLink $socialLink, UpdateSocialLinkAction $action): RedirectResponse
    {
        $action->execute($socialLink, $data);

        $this->toast('Link updated.');

        return to_route('admin.profile.edit');
    }

    public function destroy(SocialLink $socialLink, DeleteSocialLinkAction $action): RedirectResponse
    {
        $action->execute($socialLink);

        $this->toast('Link removed.');

        return to_route('admin.profile.edit');
    }

    public function reorder(ReorderData $data, ReorderSocialLinksAction $action): RedirectResponse
    {
        $action->execute($data->ids);

        return to_route('admin.profile.edit');
    }
}
