#!/usr/bin/env node
/**
 * ブロックエディター用 JED JSON 翻訳ファイルに
 * scripts/translations/{locale}.js の辞書を追記するスクリプト
 * 使い方: node scripts/add-blocks-translations.js
 *   または: npm run add-blocks-translations
 */
const fs = require( 'fs' );
const path = require( 'path' );

// languages フォルダのパスを定義
const LANG_DIR = path.join( __dirname, '..', 'languages' );

// 対象ロケール一覧
const LOCALES = [
  'de_DE',
  'en_US',
  'es_ES',
  'fr_FR',
  'ko_KR',
  'pt_PT',
  'zh_CN',
  'zh_TW',
];

let success = 0;
let error = 0;

for ( const locale of LOCALES ) {
  // 翻訳辞書 JS ファイルを動的に読み込む
  const dictPath = path.join( __dirname, 'translations', `${ locale }.js` );
  if ( ! fs.existsSync( dictPath ) ) {
    console.warn(
      `⚠️  ${ locale }: 辞書ファイルが見つかりません (${ dictPath })`
    );
    continue;
  }

  // JED JSON ファイルのパスを定義
  const jsonPath = path.join(
    LANG_DIR,
    `cocoon-${ locale }-cocoon-blocks-js.json`
  );
  if ( ! fs.existsSync( jsonPath ) ) {
    console.warn(
      `⚠️  ${ locale }: JSON ファイルが見つかりません (${ jsonPath })`
    );
    error++;
    continue;
  }

  try {
    // JED JSON を読み込む
    const jed = JSON.parse( fs.readFileSync( jsonPath, 'utf8' ) );

    // 翻訳辞書を読み込む（module.exports = { ... } 形式）
    const dict = require( dictPath );

    // locale_data.messages が存在しない場合は初期化する
    if ( ! jed.locale_data ) jed.locale_data = {};
    if ( ! jed.locale_data.messages ) {
      jed.locale_data.messages = { '': { domain: 'messages', lang: locale } };
    }

    const messages = jed.locale_data.messages;
    let added = 0;

    // 辞書の各エントリを JED 形式で messages に追記する
    for ( const [ jp, translated ] of Object.entries( dict ) ) {
      // JED1.x 形式では各エントリは ["翻訳文字列"] の1要素配列（WordPress/tannin の仕様）
      // キーが存在しない場合のみ追加する（既存翻訳を上書きしない）
      if ( ! messages[ jp ] ) {
        messages[ jp ] = [ translated ];
        added++;
      }
    }

    // 更新した JSON を書き出す
    fs.writeFileSync( jsonPath, JSON.stringify( jed ) );
    console.log(
      `✅ ${ locale }: ${ added }件 追加 → ${ path.basename( jsonPath ) }`
    );
    success++;
  } catch ( err ) {
    console.error( `❌ ${ locale }: エラー - ${ err.message }` );
    error++;
  }
}

console.log( `\n--- 完了 ---` );
console.log(
  `成功: ${ success } / 失敗: ${ error } / 合計: ${ LOCALES.length }`
);
