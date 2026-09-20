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
const navigationScript = fs.readFileSync( navigationScriptPath, 'utf8' );

assert.deepStrictEqual(
  normalizeCss( distributedCss ),
  normalizeCss( generatedCss ),
  'scss/cocoon-settings.scssとcss/cocoon-settings.cssが一致しません。'
);

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
