// Config base de ESLint compartida por shared/ui, web/frontend,
// store/frontend y ticketing/frontend. Cada paquete la extiende en su propio
// eslint.config.js con `...veraguasEslintPreset`.
import tseslint from 'typescript-eslint';
import reactHooks from 'eslint-plugin-react-hooks';
import jsxA11y from 'eslint-plugin-jsx-a11y';

export const veraguasEslintPreset = [
    ...tseslint.configs.recommended,
    {
        plugins: {
            'react-hooks': reactHooks,
            'jsx-a11y': jsxA11y,
        },
        rules: {
            ...reactHooks.configs.recommended.rules,
            ...jsxA11y.configs.recommended.rules,
            '@typescript-eslint/no-unused-vars': ['error', { argsIgnorePattern: '^_', varsIgnorePattern: '^_' }],
        },
    },
    {
        ignores: ['dist/**', 'node_modules/**'],
    },
];
