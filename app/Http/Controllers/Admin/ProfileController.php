<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Profile\Actions\GetProfileAction;
use App\Domain\Profile\Actions\UpdateProfileAction;
use App\Domain\Profile\Enums\SocialPlatform;
use App\Domain\Profile\Models\Profile;
use App\Domain\Profile\Models\SocialLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdateProfileRequest;
use App\Http\Resources\Admin\ProfileResource;
use App\Http\Resources\SocialLinkResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The public site profile (not the admin's login account, which lives
 * under /settings).
 */
class ProfileController extends Controller
{
    public function edit(GetProfileAction $getProfile): Response
    {
        $this->authorize('update', Profile::class);

        return Inertia::render('admin/profile/edit', [
            'profile' => ProfileResource::make($getProfile->execute()),
            'socialLinks' => SocialLinkResource::collection(SocialLink::query()->ordered()->get()),
            'platforms' => SocialPlatform::options(),
        ]);
    }

    public function update(UpdateProfileRequest $request, UpdateProfileAction $action): RedirectResponse
    {
        $action->execute($request->toData());

        $this->toast('Profile saved.');

        return to_route('admin.profile.edit');
    }
}
