#!/usr/bin/env node
/**
 * 各言語の翻訳辞書を .po ファイルに適用するスクリプト
 * msgctxt（メッセージコンテキスト）付きエントリも対象とします。
 * 使い方: node scripts/apply-translations.js
 */
const fs = require( 'fs' );
const path = require( 'path' );
const gp = require( '../node_modules/gettext-parser' );

// 翻訳辞書を言語ごとに定義する
// キー: msgid（日本語原文）、値: 各言語の翻訳
const translations = require( './translations-dict.js' );

const LANGUAGES_DIR = path.join( __dirname, '..', 'languages' );

// 適用対象の言語ファイルと辞書キーのマッピング
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

let totalApplied = 0;

for ( const locale of LOCALES ) {
  const poPath = path.join( LANGUAGES_DIR, `${ locale }.po` );
  if ( ! fs.existsSync( poPath ) ) continue;

  // .po ファイルをパースする
  const parsed = gp.po.parse( fs.readFileSync( poPath ) );
  const dict = translations[ locale ] || {};
  let applied = 0;

  // すべてのコンテキスト（msgctxt含む）を走査して未翻訳に辞書を適用する
  // translations[''] はデフォルト、translations['block title'] 等は msgctxt 付きエントリ
  for ( const [ ctx, entries ] of Object.entries( parsed.translations ) ) {
    for ( const [ msgid, entry ] of Object.entries( entries ) ) {
      if ( msgid === '' ) continue;
      if ( ! entry.msgstr || entry.msgstr[ 0 ] === '' ) {
        if ( dict[ msgid ] ) {
          entry.msgstr = [ dict[ msgid ] ];
          applied++;
        }
      }
    }
  }

  // 更新した内容を .po ファイルに書き戻す
  fs.writeFileSync( poPath, gp.po.compile( parsed ) );
  console.log( `✅ ${ locale }: ${ applied }件 適用` );
  totalApplied += applied;
}

console.log( `\n合計: ${ totalApplied }件 適用完了` );
