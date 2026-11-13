<?php

declare(strict_types=1);

namespace App\Domains\Seo\Controllers\Public;

use App\Domains\Blog\Models\Post;
use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Projects\Models\Project;
use App\Domains\Seo\Actions\RenderShareCardAction;
use App\Domains\Seo\ValueObjects\ShareCard;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Generated Open Graph images. Only known pages and published content get a card, so the endpoint
 * can't be used to render arbitrary text under the site owner's name.
 */
class ShareCardController extends Controller
{
    /** Public pages with a card: key → [label, title]. */
    private const PAGES = [
        'home' => null,
        'about' => ['about', 'About'],
        'projects' => ['projects', 'Projects'],
        'services' => ['services', 'Services'],
        'contact' => ['contact', 'Contact'],
        'blog' => ['blog', 'Writing'],
        'uses' => ['uses', 'What I use'],
    ];

    public function __construct(
        private readonly GetProfileAction $getProfile,
        private readonly RenderShareCardAction $render,
    ) {}

    public function page(string $page): BinaryFileResponse
    {
        abort_unless(array_key_exists($page, self::PAGES), 404);

        $profile = $this->getProfile->execute();
        [$label, $title] = self::PAGES[$page] ?? [mb_strtolower($profile->headline), $profile->name];
        $subtitle = $page === 'home' ? ($profile->tagline ?? $profile->short_bio) : $profile->headline;

        return $this->respond($label, $title, $subtitle);
    }

    public function project(string $slug): BinaryFileResponse
    {
        $project = Project::query()->published()->where('slug', $slug)->firstOrFail();

        return $this->respond('case study', $project->title, $project->summary);
    }

    public function post(string $slug): BinaryFileResponse
    {
        abort_unless($this->getProfile->snapshot()->blogEnabled, 404);

        $post = Post::query()->published()->where('slug', $slug)->firstOrFail();

        return $this->respond('blog', $post->title, $post->excerpt);
    }

    private function respond(string $label, string $title, ?string $subtitle): BinaryFileResponse
    {
        $profile = $this->getProfile->execute();
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        $path = $this->render->execute(new ShareCard(
            label: $label,
            title: $title,
            subtitle: $subtitle,
            name: $profile->name,
            domain: is_string($host) ? $host : null,
        ));

        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
