const defaultConfig = require('@wordpress/scripts/config/.eslintrc.js');

module.exports = {
  ...defaultConfig,
  env: {
    browser: true,
  },
  rules: {
    'react-hooks/rules-of-hooks': 0,
  },
};

