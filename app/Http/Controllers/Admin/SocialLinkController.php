<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Profile\Actions\CreateSocialLinkAction;
use App\Domain\Profile\Actions\DeleteSocialLinkAction;
use App\Domain\Profile\Actions\ReorderSocialLinksAction;
use App\Domain\Profile\Actions\UpdateSocialLinkAction;
use App\Domain\Profile\Models\SocialLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\StoreSocialLinkRequest;
use App\Http\Requests\Admin\Profile\UpdateSocialLinkRequest;
use App\Http\Requests\Admin\ReorderRequest;
use Illuminate\Http\RedirectResponse;

class SocialLinkController extends Controller
{
    public function store(StoreSocialLinkRequest $request, CreateSocialLinkAction $action): RedirectResponse
    {
        $action->execute($request->toData());

        $this->toast('Link added.');

        return to_route('admin.profile.edit');
    }

    public function update(UpdateSocialLinkRequest $request, SocialLink $socialLink, UpdateSocialLinkAction $action): RedirectResponse
    {
        $action->execute($socialLink, $request->toData());

        $this->toast('Link updated.');

        return to_route('admin.profile.edit');
    }

    public function destroy(SocialLink $socialLink, DeleteSocialLinkAction $action): RedirectResponse
    {
        $this->authorize('delete', $socialLink);

        $action->execute($socialLink);

        $this->toast('Link removed.');

        return to_route('admin.profile.edit');
    }

    public function reorder(ReorderRequest $request, ReorderSocialLinksAction $action): RedirectResponse
    {
        $this->authorize('reorder', SocialLink::class);

        $action->execute($request->ids());

        return to_route('admin.profile.edit');
    }
}
