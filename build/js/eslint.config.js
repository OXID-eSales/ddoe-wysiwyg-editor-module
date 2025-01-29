export default [
  {
    files: ['build/js/**/*.js'],
    languageOptions: {
      sourceType: 'script',
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
