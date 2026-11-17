<?php

test('web responses carry the hardening headers', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

    expect($response->headers->get('Content-Security-Policy'))
        ->toContain("frame-ancestors 'self'")
        ->toContain("object-src 'none'")
        ->and($response->headers->get('Permissions-Policy'))->toContain('camera=()');
});

test('admin pages carry them too', function () {
    $this->actingAs(admin())->get(route('admin.dashboard'))->assertHeader('X-Frame-Options', 'SAMEORIGIN');
});

test('an unknown appearance cookie falls back to dark', function () {
    $this->withUnencryptedCookie('appearance', "dark'</script><script>alert(1)</script>")
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('alert(1)', false)
        ->assertSee("const appearance = 'dark'", false);
});

test('a known appearance cookie is kept', function () {
    $this->withUnencryptedCookie('appearance', 'light')
        ->get(route('home'))
        ->assertSee("const appearance = 'light'", false);
});

test('private files are not served by url', function () {
    $this->get('/storage/anything.txt')->assertNotFound();
});
