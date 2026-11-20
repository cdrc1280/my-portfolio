import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import babel from '@rolldown/plugin-babel';
import tailwindcss from '@tailwindcss/vite';
import react, { reactCompilerPreset } from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig, lazyPlugins } from 'vite-plus';

export default defineConfig({
    plugins: lazyPlugins(() => [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
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
    // On Vercel each file request can start its own container, so a visit should need few files:
    // npm packages in one chunk, shared app code in another. Pages keep their own chunks because
    // app.blade.php loads the current page's chunk through the Vite manifest.
    build: {
        rolldownOptions: {
            output: {
                codeSplitting: {
                    groups: [
                        { name: 'vendor', test: /node_modules/, priority: 20 },
                        { name: 'shared', test: /resources\/js\/(components|hooks|lib|layouts|routes|actions|wayfinder|types)\//, priority: 10 },
                    ],
                },
            },
        },
    },
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
