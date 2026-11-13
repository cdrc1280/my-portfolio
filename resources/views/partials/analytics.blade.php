{{--
    Cookie-free page analytics for the public site only (never /admin or /settings).
    Off unless ANALYTICS_PROVIDER and ANALYTICS_SITE_ID are set; see config/portfolio.php.
    Both scripts follow Inertia's client-side navigation through the History API.
--}}
@php
    $analytics = config('portfolio.analytics');
    $provider = $analytics['provider'] ?? null;
    $siteId = $analytics['site_id'] ?? null;
    $isPublicPage = ! request()->is('admin', 'admin/*', 'settings', 'settings/*', 'login', 'register', 'two-factor*');
@endphp

@if ($isPublicPage && $siteId)
    @if ($provider === 'plausible')
        <script defer data-domain="{{ $siteId }}" src="{{ $analytics['script_url'] ?: 'https://plausible.io/js/script.js' }}"></script>
        <script>window.plausible = window.plausible || function () { (window.plausible.q = window.plausible.q || []).push(arguments); };</script>
    @elseif ($provider === 'umami' && $analytics['script_url'])
        <script defer data-website-id="{{ $siteId }}" src="{{ $analytics['script_url'] }}"></script>
    @endif
@endif
