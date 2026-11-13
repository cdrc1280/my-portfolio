{{--
    Shown on a full page load until React mounts (hidden by `body:has(#app > *)` in app.css), so a
    reload shows the shape of the page instead of a blank screen. One variant per area of the app.
--}}
@php
    $component = (string) ($page['component'] ?? '');
    $area = str_starts_with($component, 'admin/') || str_starts_with($component, 'settings/')
        ? 'admin'
        : (str_starts_with($component, 'auth/') ? 'auth' : 'public');
    $block = 'animate-pulse motion-reduce:animate-none rounded-md bg-accent';
@endphp
<div class="boot-skeleton fixed inset-0 z-[100] overflow-hidden bg-background" aria-hidden="true">
    @if ($area === 'admin')
        <div class="flex h-full">
            <div class="hidden w-64 shrink-0 flex-col gap-3 border-r border-border p-4 md:flex">
                <div class="{{ $block }} h-10 w-40"></div>
                @foreach (range(1, 9) as $i)
                    <div class="{{ $block }} h-8 w-full"></div>
                @endforeach
            </div>
            <div class="flex-1 space-y-6 p-4 md:p-6">
                <div class="{{ $block }} h-6 w-48"></div>
                <div class="{{ $block }} h-8 w-72"></div>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-12">
                    <div class="{{ $block }} h-72 rounded-xl md:col-span-2 xl:col-span-8"></div>
                    <div class="{{ $block }} h-72 rounded-xl xl:col-span-4"></div>
                    @foreach (range(1, 4) as $i)
                        <div class="{{ $block }} h-28 rounded-xl xl:col-span-3"></div>
                    @endforeach
                </div>
            </div>
        </div>
    @elseif ($area === 'auth')
        <div class="mx-auto flex h-full max-w-sm flex-col justify-center gap-5 px-4">
            <div class="{{ $block }} h-6 w-40"></div>
            <div class="{{ $block }} h-4 w-56"></div>
            <div class="{{ $block }} h-9 w-full"></div>
            <div class="{{ $block }} h-9 w-full"></div>
            <div class="{{ $block }} h-9 w-full"></div>
        </div>
    @else
        <div class="border-b border-border">
            <div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="{{ $block }} h-5 w-44"></div>
                <div class="hidden gap-6 md:flex">
                    @foreach (range(1, 5) as $i)
                        <div class="{{ $block }} h-4 w-14"></div>
                    @endforeach
                </div>
                <div class="{{ $block }} h-8 w-20"></div>
            </div>
        </div>
        <div class="mx-auto max-w-6xl space-y-6 px-4 pt-20 sm:px-6 sm:pt-28 lg:px-8">
            <div class="{{ $block }} h-3 w-40"></div>
            <div class="{{ $block }} h-12 w-full max-w-3xl"></div>
            <div class="{{ $block }} h-12 w-2/3 max-w-2xl"></div>
            <div class="{{ $block }} h-4 w-full max-w-xl"></div>
            <div class="flex gap-3 pt-2">
                <div class="{{ $block }} h-10 w-32"></div>
                <div class="{{ $block }} h-10 w-24"></div>
            </div>
        </div>
    @endif
</div>
