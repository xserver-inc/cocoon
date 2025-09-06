module.exports = {
  extends: [ 'stylelint-config-standard', 'stylelint-config-standard-scss' ],
  plugins: [ 'stylelint-scss' ],
  rules: {
    // SCSS rules
    'scss/at-rule-no-unknown': true,
    'scss/selector-no-redundant-nesting-selector': true,

    // CSS順序 (Prettierで管理するため無効化)
    'order/properties-order': null,

    // WordPress/Cocoon specific
    'selector-class-pattern': null, // WordPressの命名規則に合わせるため無効
    'custom-property-pattern': null,

    // ベンダープレフィックス
    'property-no-vendor-prefix': null,
    'value-no-vendor-prefix': null,
    'selector-no-vendor-prefix': null,
    'at-rule-no-vendor-prefix': null,

    // フォント
    'font-family-no-missing-generic-family-keyword': null,

    // 色
    'color-function-notation': 'legacy',
    'alpha-value-notation': 'number',

    // セレクタ
    'selector-max-id': 1,
    'selector-max-compound-selectors': 4,
    'selector-max-specificity': '0,4,0',

    // 値
    'length-zero-no-unit': true,
    'shorthand-property-no-redundant-values': true,

    // 無効なルール
    'no-descending-specificity': null, // WordPressテーマでは困難
    'keyframes-name-pattern': null,

    // Comment
    'comment-empty-line-before': null,

    // Declaration
    'declaration-empty-line-before': null,
    'declaration-block-no-redundant-longhand-properties': null,

    // Function
    'function-url-quotes': 'always',

    // String
    'string-quotes': 'single',
  },
  ignoreFiles: [
    '**/*.min.css',
    'dist/**/*',
    'build/**/*',
    'node_modules/**/*',
    'vendor/**/*',
    'plugins/**/*',
    'webfonts/**/*',
    'tmp/**/*',
    // コンパイル済みファイル（直接編集しないため除外）
    'style.css',
    'amp.css',
    'css/**/*.css',
    'skins/**/style.css',
    // サードパーティライブラリ
    'js/lib/**/*',
  ],
  overrides: [
    {
      files: [ '**/*.scss' ],
      customSyntax: 'postcss-scss',
    },
  ],
};
