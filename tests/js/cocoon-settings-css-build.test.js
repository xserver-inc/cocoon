'use strict';

const assert = require( 'assert' );
const fs = require( 'fs' );
const path = require( 'path' );
const sass = require( 'sass' );
const postcss = require( 'postcss' );

const scssPath = path.resolve( __dirname, '../../scss/cocoon-settings.scss' );
const modernScssPath = path.resolve(
  __dirname,
  '../../scss/_cocoon-settings-modern.scss'
);
const cssPath = path.resolve( __dirname, '../../css/cocoon-settings.css' );
const adminScssPath = path.resolve( __dirname, '../../scss/admin.scss' );
const adminCssPath = path.resolve( __dirname, '../../css/admin.css' );
const navigationScriptPath = path.resolve(
  __dirname,
  '../../js/cocoon-settings-navigation.js'
);

// コメントと空の規則だけを除外し、宣言・セレクター・カスケード順を保持した比較
const normalizeCss = ( css ) => {
  const normalizeNodes = ( nodes ) => nodes.flatMap( ( node ) => {
    if ( node.type === 'comment' ) {
      return [];
    }
    const children = node.nodes ? normalizeNodes( node.nodes ) : null;
    if ( node.type === 'rule' && children.length === 0 ) {
      return [];
    }
    if ( node.type === 'decl' ) {
      return [ [ node.type, node.prop, node.value, Boolean( node.important ) ] ];
    }
    return [ [ node.type, node.selector || node.name, node.params || '', children ] ];
  } );
  return normalizeNodes( postcss.parse( css.replace( /\r\n?/gu, '\n' ) ).nodes );
};

assert.deepStrictEqual(
  normalizeCss( '.empty { /* 制御コメント */ } .a { color: red; }' ),
  normalizeCss( '.a { color: red; /* 制御コメント */ }' )
);
// 文字列内のコメント風表記と宣言順・詳細度・条件の違いの検出
for ( const [ before, after ] of [
  [ '.a { content: "/* a */"; }', '.a { content: "/* b */"; }' ],
  [ '.a { color: red; }', '.a { color: blue; }' ],
  [ '.a { color: red; }', '.b { color: red; }' ],
  [ '.a { color: red; }', '.a { color: red !important; }' ],
  [ '.a { color: red; color: blue; }', '.a { color: blue; color: red; }' ],
  [ '@media (width < 600px) { .a { color: red; } }', '@media (width < 782px) { .a { color: red; } }' ],
  [ '@layer first, second;', '@layer second, first;' ],
] ) {
  assert.notDeepStrictEqual( normalizeCss( before ), normalizeCss( after ) );
}

// 現在のSassによる再生成結果と配布CSSの実効スタイルの照合
const generatedCss = sass.compile( scssPath, { style: 'expanded' } ).css;
const distributedCss = fs.readFileSync( cssPath, 'utf8' );
const modernScss = fs.readFileSync( modernScssPath, 'utf8' );
const inputDefinitions = fs.readFileSync( path.resolve( __dirname, '../../lib/_defins.php' ), 'utf8' );
const navigationScript = fs.readFileSync( navigationScriptPath, 'utf8' );
const adminCss = fs.readFileSync( adminCssPath, 'utf8' );
const adminGeneratedCss = sass.compile( adminScssPath, { style: 'expanded' } ).css;

// 生成CSSと既存CSSから同じセレクターの宣言だけを抽出
const findDeclarations = ( css, selector ) => {
  const matches = [];
  postcss.parse( css ).walkRules( ( rule ) => {
    if ( rule.selector === selector ) {
      matches.push( rule.nodes.filter( ( node ) => node.type === 'decl' )
        .map( ( node ) => [ node.prop, node.value, Boolean( node.important ) ] ) );
    }
  } );
  return matches;
};

assert.deepStrictEqual(
  normalizeCss( distributedCss ),
  normalizeCss( generatedCss ),
  'scss/cocoon-settings.scssとcss/cocoon-settings.cssが一致しません。'
);

// 設定画面専用CSSの空ルール排除と通知欄の幅制限解除
postcss.parse( distributedCss ).walkRules( ( rule ) => {
  assert.ok(
    rule.nodes.some( ( node ) => node.type !== 'comment' ),
    `空のCSSルールが残っています: ${ rule.selector }`
  );
} );
assert.doesNotMatch( modernScss, /> :where\(\.notice\)\s*\{/u );

// 設定画面・説明文・文章入力欄の固定幅上限撤廃と画面内制約の維持
const settingsSelector = '.toplevel_page_theme-settings .wrap.admin-settings';
const settingsDeclarations = findDeclarations( distributedCss, settingsSelector );
assert.ok( settingsDeclarations.length > 0 );
assert.ok( settingsDeclarations.flat().every( ( [ property ] ) =>
  property !== 'max-inline-size' && property !== '--cocoon-settings-content-max'
) );
const introDeclarations = findDeclarations( distributedCss, `${ settingsSelector } > :where(p:not(.submit))` );
assert.ok( introDeclarations.length > 0 );
assert.ok( introDeclarations.flat().every( ( [ property ] ) => property !== 'max-inline-size' ) );
// PHPの標準入力文字幅とCSS属性セレクターの一致
const defaultInputColumns = inputDefinitions.match( /define\(\s*'DEFAULT_INPUT_COLS'\s*,\s*(\d+)\s*\)/u );
assert.ok( defaultInputColumns );
assert.strictEqual( defaultInputColumns[ 1 ], '60' );
for ( const fieldSelector of [ '.regular-text, input[type=text][size="60"]', '.large-text, textarea' ] ) {
  const selector = `${ settingsSelector } :where(.postbox > .inside) :where(${ fieldSelector }):not(:where(.demo *, .wp-picker-container *, .iris-picker *, .wp-editor-wrap *))`;
  const declarations = findDeclarations( distributedCss, selector );
  assert.strictEqual( declarations.length, 1 );
  assert.ok( declarations[ 0 ].some( ( [ property, value ] ) =>
    property === 'inline-size' && value === '100%'
  ) );
}

// 常時表示のヒントの幅制限解除とポップアップの画面内制限の維持
const tipsSelector = '.toplevel_page_theme-settings .wrap.admin-settings :where(#tabs > .metabox-holder) :where(.tips):not(:where(.demo *))';
const tipsDeclarations = findDeclarations( distributedCss, tipsSelector );
assert.strictEqual( tipsDeclarations.length, 1 );
assert.ok( tipsDeclarations[ 0 ].every( ( [ property ] ) =>
  property !== 'max-inline-size' && property !== 'max-width'
) );
assert.ok( tipsDeclarations[ 0 ].some( ( [ property, value ] ) =>
  property === 'overflow-wrap' && value === 'anywhere'
) );
const tooltipSelector = '.toplevel_page_theme-settings .wrap.admin-settings :where(#tabs > .metabox-holder) :where(.tooltip .tip-content):not(:where(.demo *, .wp-editor-wrap *))';
const tooltipDeclarations = findDeclarations( distributedCss, tooltipSelector );
assert.ok( tooltipDeclarations.some( ( declarations ) => declarations.some( ( [ property, value ] ) =>
  property === 'max-inline-size' && value === 'calc(100vw - 48px)'
) ) );
assert.ok( tooltipDeclarations.some( ( declarations ) => declarations.some( ( [ property, value ] ) =>
  property === 'max-inline-size' && value === 'calc(100vw - 24px)'
) ) );

// 旧タブ入力のキーボード操作と生成CSSとの宣言一致
const legacyTabSelector = '#tabs .tab-input';
const legacyTabDeclarations = findDeclarations( adminCss, legacyTabSelector );
assert.deepStrictEqual(
  legacyTabDeclarations,
  findDeclarations( adminGeneratedCss, legacyTabSelector ),
  '旧タブ入力の配布CSSがSCSSの生成結果と一致しません。'
);
assert.strictEqual( legacyTabDeclarations.length, 1 );
assert.ok( legacyTabDeclarations[ 0 ].some( ( [ property, value ] ) =>
  property === 'position' && value === 'absolute'
) );
assert.ok( legacyTabDeclarations[ 0 ].some( ( [ property, value ] ) =>
  property === 'min-width' && value === '0'
) );
assert.ok( legacyTabDeclarations[ 0 ].every( ( [ property ] ) => property !== 'display' ) );
const legacyFocusSelector = '#tabs > .tab-input:focus-visible + .tab-label';
const legacyFocusDeclarations = findDeclarations( adminCss, legacyFocusSelector );
assert.strictEqual( legacyFocusDeclarations.length, 1 );
assert.deepStrictEqual( legacyFocusDeclarations, findDeclarations( adminGeneratedCss, legacyFocusSelector ) );

// 設定カード見出しの非ドラッグ表示とフォーカス表示の維持
assert.match( distributedCss, /:where\(\.hndle\)\s*\{[^}]*cursor: default;/u );
assert.match( distributedCss, /#tabs > \.tab-input:focus-visible \+ \.tab-label/u );

// WordPress管理画面がモバイルレイアウトへ切り替わる782pxを、選択UI側でも同じ境界として固定する。
const mobileBreakpointStart = modernScss.indexOf(
  '@media screen and (width <= 782px)'
);
const mobileBreakpointEnd = modernScss.indexOf(
  '@media screen and (width <= 600px)',
  mobileBreakpointStart
);

assert.ok(
  mobileBreakpointStart >= 0 && mobileBreakpointEnd > mobileBreakpointStart,
  '782px以下のモバイル契約を特定できません。'
);

const mobileBreakpointScss = modernScss.slice(
  mobileBreakpointStart,
  mobileBreakpointEnd
);

assert.match(
  mobileBreakpointScss,
  /:where\(\.cocoon-settings-view-mode-control\)\s*\{[\s\S]*?grid-template-columns:\s*minmax\(0, 1fr\) minmax\(0, 1fr\);/u,
  '782px以下でも表示モードの2列が内容幅で押し広げられないようにしてください。'
);

// JSの開始幅1040px・解除幅1020pxとSCSSの2カラム表示の対応
assert.match(
  navigationScript,
  /const minimumWidth = tabs\.classList\.contains\( 'is-navigation-wide' \)\s*\? 1020\s*: 1040;/u,
  '2カラムの開始幅と解除幅を維持してください。'
);
assert.match(
  modernScss,
  /:where\(#tabs\.is-navigation-enhanced\.is-navigation-mode-responsive\.is-navigation-wide\)\s*\{[\s\S]*?grid-template-columns:\s*232px minmax\(0, 1fr\);/u,
  'wide表示の本文列はminmax(0, 1fr)で横溢れを防いでください。'
);

// 空白なしの長文翻訳でもグリッド項目が縮み、320px幅でボタン内改行できる宣言を固定する。
const viewModeButtonRule = modernScss.match(
  /:where\(\.cocoon-settings-view-mode-button\)\s*\{([\s\S]*?)\n\s*\}/u
);

assert.ok( viewModeButtonRule, '表示モードボタンの基本ルールがありません。' );
for ( const declaration of [
  'min-inline-size: 0;',
  'max-inline-size: 100%;',
  'overflow-wrap: anywhere;',
  'white-space: normal;',
] ) {
  assert.ok(
    viewModeButtonRule[ 1 ].includes( declaration ),
    `長文翻訳対策の宣言がありません: ${ declaration }`
  );
}

// 管理バーの固定位置と、全モード共通の固定保存欄の契約
assert.match( modernScss, /--cocoon-settings-navigation-top: 32px;/u );
assert.match( mobileBreakpointScss, /--cocoon-settings-navigation-top: 46px;/u );
assert.match( modernScss.slice( mobileBreakpointEnd ), /--cocoon-settings-navigation-top: 0px;/u );
assert.match( mobileBreakpointScss, /:where\(form\.admin-settings > \.submit:last-of-type\)\s*\{\s*position: fixed;/u );
assert.match( mobileBreakpointScss, /padding-block-end: calc\(76px \+ env\(safe-area-inset-bottom\)\);/u );
assert.ok( modernScss.indexOf( 'top: var(--cocoon-settings-navigation-top);' ) < mobileBreakpointStart );

process.stdout.write( 'Cocoon settings SCSS/CSS build test passed.\n' );
