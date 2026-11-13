import js from '@eslint/js';
import prettier from 'eslint-config-prettier';
import { createTypeScriptImportResolver } from 'eslint-import-resolver-typescript';
import importX from 'eslint-plugin-import-x';
import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import globals from 'globals';
import tseslint from 'typescript-eslint';

/**
 * Layout boundaries (see README → Frontend):
 * - components/, hooks/, lib/ and types/ never import from pages/;
 * - a page folder's private components (e.g. pages/admin/posts/post-form.tsx) are imported relatively
 *   by that folder's pages only, so nothing imports through `@/pages`.
 */
const layoutZones = ['components', 'hooks', 'lib', 'types', 'layouts'].map((dir) => ({
    target: `./resources/js/${dir}`,
    from: './resources/js/pages',
    message: 'Shared code must not depend on pages. Move what both need into components/custom or lib/.',
}));

export default tseslint.config(
    {
        ignores: [
            'vendor/**',
            'node_modules/**',
            'public/**',
            'bootstrap/ssr/**',
            // Runtime files (uploads, fake test disks); never source code.
            'storage/**',
            'graphify-out/**',
            'resources/js/actions/**',
            'resources/js/routes/**',
            'resources/js/wayfinder/**',
            'resources/js/components/ui/**',
        ],
    },
    js.configs.recommended,
    ...tseslint.configs.recommended,
    {
        ...react.configs.flat.recommended,
        ...react.configs.flat['jsx-runtime'],
        languageOptions: {
            globals: { ...globals.browser },
        },
        settings: { react: { version: 'detect' } },
        rules: {
            ...react.configs.flat.recommended.rules,
            ...react.configs.flat['jsx-runtime'].rules,
            'react/prop-types': 'off',
            'react/no-unescaped-entities': 'off',
            // Inertia's <Head> de-duplicates tags by `head-key`.
            'react/no-unknown-property': ['error', { ignore: ['head-key'] }],
        },
    },
    {
        plugins: { 'react-hooks': reactHooks },
        rules: {
            'react-hooks/rules-of-hooks': 'error',
            'react-hooks/exhaustive-deps': 'warn',
        },
    },
    {
        files: ['resources/js/**/*.{ts,tsx}'],
        plugins: { 'import-x': importX },
        settings: {
            'import-x/resolver-next': [createTypeScriptImportResolver({ alwaysTryTypes: true })],
        },
        rules: {
            '@typescript-eslint/no-explicit-any': 'error',
            '@typescript-eslint/consistent-type-imports': [
                'error',
                { prefer: 'type-imports', fixStyle: 'separate-type-imports' },
            ],
            '@typescript-eslint/no-unused-vars': ['error', { argsIgnorePattern: '^_', varsIgnorePattern: '^_' }],
            'import-x/no-restricted-paths': ['error', { zones: layoutZones }],
            'no-restricted-imports': [
                'error',
                {
                    patterns: [
                        {
                            group: ['@/pages/*'],
                            message: "Import a page folder's components relatively from that folder only.",
                        },
                    ],
                },
            ],
            'import-x/order': [
                'error',
                {
                    groups: ['builtin', 'external', 'internal', 'parent', 'sibling', 'index'],
                    pathGroups: [{ pattern: '@/**', group: 'internal' }],
                    alphabetize: { order: 'asc', caseInsensitive: true },
                },
            ],
        },
    },
    {
        // Module augmentation must repeat the original generic parameters.
        files: ['**/*.d.ts'],
        rules: { '@typescript-eslint/no-unused-vars': 'off' },
    },
    prettier,
);
