import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import babel from '@rolldown/plugin-babel';
import tailwindcss from '@tailwindcss/vite';
import react, { reactCompilerPreset } from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig, lazyPlugins } from 'vite-plus';

export default defineConfig({
    plugins: lazyPlugins(() => [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
            fonts: [
                bunny('Geist', {
                    weights: [400, 500, 600],
                }),
                // Tags, section labels, dates, metadata, code and folder trees.
                bunny('Geist Mono', {
                    weights: [400, 500],
                }),
            ],
        }),
        inertia(),
        react(),
        babel({
            presets: [reactCompilerPreset()],
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ]),
    // The SSR bundle includes its dependencies, so production only needs Node + bootstrap/ssr
    // (no node_modules in the Vercel image).
    ssr: {
        noExternal: true,
    },
    server: {
        // Inside Docker the dev server listens on all interfaces, while the
        // browser on the host reaches it (and its HMR socket) via localhost.
        host: process.env.VITE_HOST ?? 'localhost',
        port: Number(process.env.VITE_PORT ?? 5173),
        strictPort: true,
        hmr: { host: process.env.VITE_HMR_HOST ?? 'localhost' },
        watch: {
            ignored: [
                '**/.agents/**',
                '**/.claude/**',
                '**/.cursor/**',
                '**/.junie/**',
                '**/vendor/**',
                '**/graphify-out/**',
            ],
        },
    },
});
