module.exports = {
  root: true,
  env: {
    browser: true,
    es6: true,
    jquery: true,
    node: true,
  },
  extends: [ 'eslint:recommended' ],
  parserOptions: {
    ecmaVersion: 2020,
    sourceType: 'module',
  },
  globals: {
    // WordPress globals
    wp: 'readonly',
    jQuery: 'readonly',
    $: 'readonly',
    // Cocoon specific globals
    cocoon_js: 'readonly',
    cocoon_localize_script_options: 'readonly',
    // Admin globals
    ajaxurl: 'readonly',
    // Common browser globals
    console: 'readonly',
    document: 'readonly',
    window: 'readonly',
    navigator: 'readonly',
    alert: 'readonly',
    confirm: 'readonly',
  },
  rules: {
    // Code quality
    'no-console': 'warn',
    'no-debugger': 'warn',
    'no-alert': 'warn',
    'no-eval': 'error',
    'no-implied-eval': 'error',
    'no-new-func': 'error',

    // Best practices
    eqeqeq: [ 'error', 'always' ],
    curly: [ 'error', 'all' ],
    'no-var': 'warn',
    'prefer-const': 'warn',
    'prefer-arrow-callback': 'warn',

    // Style (Prettierで管理するため緩め)
    semi: [ 'error', 'always' ],
    quotes: [ 'error', 'single', { avoidEscape: true } ],

    // Accessibility
    'no-global-assign': 'error',
    'no-implicit-globals': 'error',

    // WordPress/jQuery specific
    'no-undef': 'error',
  },
  overrides: [
    {
      // Admin scripts
      files: [ 'js/admin-*.js', 'js/*admin*.js' ],
      globals: {
        ajaxurl: 'readonly',
        pagenow: 'readonly',
        adminpage: 'readonly',
      },
    },
    {
      // Block editor files
      files: [ 'blocks/**/*.js' ],
      env: {
        browser: true,
        es6: true,
      },
      extends: [ 'eslint:recommended' ],
      globals: {
        wp: 'readonly',
        React: 'readonly',
      },
    },
  ],
  ignorePatterns: [
    'node_modules/',
    'vendor/',
    'dist/',
    'build/',
    'blocks/dist/',
    'blocks/build/',
    '**/*.min.js',
    '**/*.build.js',
    // Third party libraries
    'plugins/**/*',
    'webfonts/**/*',
    'js/lib/',
    // Skin files (optional - 多すぎる警告を避けるため)
    'skins/**/javascript.js',
    // Temporary files
    'tmp/**/*',
  ],
};
