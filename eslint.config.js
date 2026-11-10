import js from '@eslint/js';
import prettier from 'eslint-config-prettier';
import { createTypeScriptImportResolver } from 'eslint-import-resolver-typescript';
import importX from 'eslint-plugin-import-x';
import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import { existsSync, readdirSync } from 'node:fs';
import globals from 'globals';
import tseslint from 'typescript-eslint';

const featuresDir = 'resources/js/features';
const features = existsSync(featuresDir)
    ? readdirSync(featuresDir, { withFileTypes: true })
          .filter((entry) => entry.isDirectory())
          .map((entry) => entry.name)
    : [];

/**
 * Feature modules are isolated: a feature may import from components/,
 * hooks/, lib/, types/ and itself — never from a sibling feature. Shared
 * code belongs in components/shared or lib/.
 */
const featureBoundaryZones = features.map((feature) => ({
    target: `./${featuresDir}/${feature}`,
    from: `./${featuresDir}`,
    except: [`./${feature}`],
    message: 'Features must not import from other features. Move shared code to components/shared or lib/.',
}));

export default tseslint.config(
    {
        ignores: [
            'vendor/**',
            'node_modules/**',
            'public/**',
            'bootstrap/ssr/**',
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
            ...(featureBoundaryZones.length > 0
                ? { 'import-x/no-restricted-paths': ['error', { zones: featureBoundaryZones }] }
                : {}),
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
