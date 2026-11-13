<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'dark') !== 'light'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- Browser UI colour; use-appearance.tsx keeps it in sync with the theme toggle. --}}
        <meta name="theme-color" content="{{ ($appearance ?? 'dark') === 'light' ? '#f6f4f1' : '#09090b' }}">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "dark" }}';

                // Dark is the default; "system" follows the OS.
                if (appearance === 'system' && !window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.remove('dark');
                    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#f6f4f1');
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(0.968 0.005 90);
            }

            html.dark {
                background-color: oklch(0.141 0.005 285.823);
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @include('partials.analytics')

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        @include('partials.boot-skeleton')
        <x-inertia::app />
    </body>
</html>
