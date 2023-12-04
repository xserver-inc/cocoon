const defaultConfig = require( '@wordpress/scripts/config/.eslintrc.js' );

module.exports = {
  ...defaultConfig,
  env: {
    browser: true,
  },
  rules: {
    'react-hooks/rules-of-hooks': 0,
    'jsdoc/check-tag-names': 0,
    '@wordpress/i18n-text-domain': 0,
    'import/no-extraneous-dependencies': 0,
  },
};
