<?php

declare(strict_types=1);

namespace App\Domains\Profile\Controllers\Admin;

use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Profile\Actions\UpdateProfileAction;
use App\Domains\Profile\Data\ProfileData;
use App\Domains\Profile\Enums\AvailabilityStatus;
use App\Domains\Profile\Enums\SocialPlatform;
use App\Domains\Profile\Models\Profile;
use App\Domains\Profile\Queries\SocialLinkQueries;
use App\Domains\Profile\Resources\AdminProfileResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:update,'.Profile::class)];
    }

    public function edit(GetProfileAction $getProfile, SocialLinkQueries $socialLinks): Response
    {
        return Inertia::render('admin/profile/edit', [
            'profile' => AdminProfileResource::fromModel($getProfile->execute()),
            'socialLinks' => $socialLinks->ordered(),
            'platforms' => SocialPlatform::options(),
            'availabilityStatuses' => AvailabilityStatus::options(),
        ]);
    }

    public function update(ProfileData $data, UpdateProfileAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Settings saved.');

        return to_route('admin.profile.edit');
    }
}
