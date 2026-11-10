<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Domain\Seo\Actions\BuildSitemapAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(BuildSitemapAction $buildSitemap): Response
    {
        return response()
            ->view('seo.sitemap', ['entries' => $buildSitemap->execute()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = app()->isProduction()
            ? ['User-agent: *', 'Disallow: /admin', 'Disallow: /settings', '', 'Sitemap: '.route('sitemap')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
