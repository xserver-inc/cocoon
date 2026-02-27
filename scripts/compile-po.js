#!/usr/bin/env node
/**
 * .po ファイルを .mo ファイルにコンパイルするスクリプト
 * 使い方: node scripts/compile-po.js
 * 特定ロケールのみ: node scripts/compile-po.js en_US
 */

// Node.js 標準モジュールの読み込み
const fs = require( 'fs' );
const path = require( 'path' );
// gettext-parser ライブラリの読み込み（.po/.mo の読み書きに使用）
const gettextParser = require( '../node_modules/gettext-parser' );

// languages フォルダのパスを定義
const LANGUAGES_DIR = path.join( __dirname, '..', 'languages' );

// コマンドライン引数からロケール指定を取得（例: node compile-po.js en_US）
const targetLocale = process.argv[ 2 ] || null;

// languages フォルダ内の .po ファイルを検索する
const poFiles = fs
  .readdirSync( LANGUAGES_DIR )
  .filter( ( file ) => file.endsWith( '.po' ) )
  .filter( ( file ) => {
    // ロケール指定がある場合はそのファイルのみ処理
    if ( targetLocale ) {
      return file === `${ targetLocale }.po`;
    }
    return true;
  } );

if ( poFiles.length === 0 ) {
  // 対象ファイルが見つからない場合はエラーを表示して終了
  console.error(
    `❌ 対象の .po ファイルが見つかりませんでした。${
      targetLocale ? `(${ targetLocale }.po)` : ''
    }`
  );
  process.exit( 1 );
}

let successCount = 0;
let errorCount = 0;

// 各 .po ファイルを順番に処理する
for ( const poFile of poFiles ) {
  const poPath = path.join( LANGUAGES_DIR, poFile );
  // 出力先の .mo ファイルパスを生成（拡張子を .mo に変更）
  const moPath = path.join( LANGUAGES_DIR, poFile.replace( /\.po$/, '.mo' ) );
  const localeName = poFile.replace( /\.po$/, '' );

  try {
    // .po ファイルをバイナリとして読み込む
    const poContent = fs.readFileSync( poPath );
    // gettext-parser で .po をパースする
    const parsed = gettextParser.po.parse( poContent );
    // パース結果を .mo バイナリ形式にコンパイルする
    const moBuffer = gettextParser.mo.compile( parsed );
    // .mo ファイルとして書き出す
    fs.writeFileSync( moPath, moBuffer );

    console.log(
      `✅ ${ localeName }: ${ poFile } → ${ path.basename( moPath ) }`
    );
    successCount++;
  } catch ( err ) {
    // エラーが発生した場合はスキップして次のファイルに進む
    console.error( `❌ ${ localeName }: コンパイル失敗 - ${ err.message }` );
    errorCount++;
  }
}

// 処理結果のサマリーを表示する
console.log( '\n--- 完了 ---' );
console.log(
  `成功: ${ successCount } / 失敗: ${ errorCount } / 合計: ${ poFiles.length }`
);
