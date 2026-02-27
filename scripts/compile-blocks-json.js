#!/usr/bin/env node
/**
 * 全言語の cocoon-{locale}-cocoon-blocks-js.json を po2json で生成するスクリプト
 * 使い方: node scripts/compile-blocks-json.js
 *   または: npm run compile-blocks-json
 */
const fs = require( 'fs' );
const path = require( 'path' );
const { execSync } = require( 'child_process' );

// po2json のパスと languages フォルダのパスを定義
const PO2JSON = path.join(
  __dirname,
  '..',
  'node_modules',
  'po2json',
  'bin',
  'po2json'
);
const LANG_DIR = path.join( __dirname, '..', 'languages' );

// languages フォルダ内の .po ファイルから対象ロケールを取得する
const locales = fs
  .readdirSync( LANG_DIR )
  .filter( ( f ) => f.endsWith( '.po' ) )
  .map( ( f ) => f.replace( '.po', '' ) );

let success = 0;
let error = 0;

console.log( `🌐 対象ロケール: ${ locales.join( ', ' ) }\n` );

// 各ロケールの .po ファイルを JED1.x 形式の JSON に変換する
for ( const locale of locales ) {
  const input = path.join( LANG_DIR, `${ locale }.po` );
  const output = path.join(
    LANG_DIR,
    `cocoon-${ locale }-cocoon-blocks-js.json`
  );

  // po2json コマンドを実行して JED1.x 形式で変換する
  const cmd = `node "${ PO2JSON }" -f jed1.x "${ input }" "${ output }"`;

  try {
    execSync( cmd, { stdio: 'pipe' } );
    console.log( `✅ ${ locale }: cocoon-${ locale }-cocoon-blocks-js.json` );
    success++;
  } catch ( err ) {
    console.error( `❌ ${ locale }: 変換失敗 - ${ err.message }` );
    error++;
  }
}

console.log( `\n--- 完了 ---` );
console.log(
  `成功: ${ success } / 失敗: ${ error } / 合計: ${ locales.length }`
);
