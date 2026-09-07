'use strict';

const assert = require( 'assert' );
const fs = require( 'fs' );
const path = require( 'path' );
const sass = require( 'sass' );

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

// OSごとの改行差だけを除き、末尾改行を含む配布CSS全体を同じ形式へそろえる。
const normalizeCss = ( css ) =>
  css.replace( /\r\n?/gu, '\n' ).replace( /\n*$/u, '\n' );

// 固定されたSass依存で専用SCSSを再生成し、配布CSSの更新漏れを1文字単位で検出する。
const generatedCss = sass.compile( scssPath, { style: 'expanded' } ).css;
const distributedCss = fs.readFileSync( cssPath, 'utf8' );
const modernScss = fs.readFileSync( modernScssPath, 'utf8' );
const navigationScript = fs.readFileSync( navigationScriptPath, 'utf8' );

assert.strictEqual(
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

// JSの実測幅とSCSSのwideクラスを対にし、1040px以上だけ2カラムへ移行する契約を固定する。
assert.match(
  navigationScript,
  /tabs\.classList\.toggle\(\s*'is-navigation-wide',\s*tabs\.clientWidth\s*>=\s*1040\s*\);/u,
  '広い画面へ切り替える実測幅は1040pxを維持してください。'
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

process.stdout.write( 'Cocoon settings SCSS/CSS build test passed.\n' );
