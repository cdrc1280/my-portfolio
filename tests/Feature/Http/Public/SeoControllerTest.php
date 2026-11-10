<?php

use App\Domain\Projects\Models\Project;

test('sitemap.xml lists published pages as xml', function () {
    Project::factory()->published()->create(['slug' => 'dts']);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>'.route('projects.show', 'dts').'</loc>', escape: false)
        ->assertSee('<urlset', escape: false);
});

test('robots.txt blocks everything outside production', function () {
    $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
});

test('robots.txt points to the sitemap in production', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->get('/robots.txt')->assertSee('Sitemap: '.route('sitemap'))->assertSee('Disallow: /admin');
});
