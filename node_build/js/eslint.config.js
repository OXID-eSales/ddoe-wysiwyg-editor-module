export default [
  {
    files: ['node_build/js/**/*.js'],
    languageOptions: {
      sourceType: 'module',
      globals: {
        browser: true,
        jquery: true,
      },
    },
    rules: {
      'no-use-before-define': 'error',
      'no-irregular-whitespace': 'error',
      'no-unused-vars': ['error', { vars: 'all', args: 'after-used' }],
      'strict': ['error', 'function'],
    },
  },
];
