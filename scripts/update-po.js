#!/usr/bin/env node
/**
 * cocoon.pot を元に全言語の .po ファイルを更新するスクリプト
 * 既存の翻訳は保持したまま、新規文字列を空欄で追加します。
 * 使い方: node scripts/update-po.js
 *   または: npm run update-po
 */

// Node.js 標準モジュールの読み込み
const fs = require( 'fs' );
const path = require( 'path' );
const { execSync } = require( 'child_process' );

// 各パスの定義
const WP_CLI_PHAR = path.join(
  __dirname,
  '..',
  'vendor',
  'bin',
  'wp-cli.phar'
);
const LANGUAGES_DIR = path.join( __dirname, '..', 'languages' );
const POT_FILE = path.join( LANGUAGES_DIR, 'cocoon.pot' );

// WP-CLI Phar が存在するか確認する
if ( ! fs.existsSync( WP_CLI_PHAR ) ) {
  console.error(
    '❌ WP-CLI が見つかりません。先に npm run make-pot を実行してください。'
  );
  process.exit( 1 );
}

// POT ファイルが存在するか確認する
if ( ! fs.existsSync( POT_FILE ) ) {
  console.error(
    '❌ cocoon.pot が見つかりません。先に npm run make-pot を実行してください。'
  );
  process.exit( 1 );
}

// languages フォルダ内の .po ファイル一覧を取得する（cocoon.pot 以外）
const poFiles = fs
  .readdirSync( LANGUAGES_DIR )
  .filter( ( f ) => f.endsWith( '.po' ) )
  .map( ( f ) => path.join( LANGUAGES_DIR, f ) );

if ( poFiles.length === 0 ) {
  console.error( '❌ 更新対象の .po ファイルが見つかりませんでした。' );
  process.exit( 1 );
}

console.log( `📄 POT ファイル: ${ POT_FILE }` );
console.log( `🌐 更新対象: ${ poFiles.length } 言語\n` );

let successCount = 0;
let errorCount = 0;

// 各 .po ファイルを順番に更新する
for ( const poFile of poFiles ) {
  const locale = path.basename( poFile, '.po' );

  // wp i18n update-po コマンドを組み立てる
  const command = [
    `php "${ WP_CLI_PHAR }"`,
    'i18n update-po',
    `"${ POT_FILE }"`, // 参照する POT ファイル
    `"${ poFile }"`, // 更新対象の .po ファイル
  ].join( ' ' );

  try {
    // コマンドを実行する（エラーとワーニングを stderr に分離）
    execSync( command, { stdio: [ 'ignore', 'ignore', 'ignore' ] } );
    console.log( `✅ ${ locale }: 更新完了` );
    successCount++;
  } catch ( err ) {
    console.error( `❌ ${ locale }: 更新失敗 - ${ err.message }` );
    errorCount++;
  }
}

// 処理結果のサマリーを表示する
console.log( '\n--- 完了 ---' );
console.log(
  `成功: ${ successCount } / 失敗: ${ errorCount } / 合計: ${ poFiles.length }`
);
console.log(
  '\n💡 次のステップ: 新しく追加された空欄 (msgstr "") を各言語で翻訳してください。'
);
console.log(
  '   翻訳完了後は npm run compile-mo で .mo ファイルを更新してください。'
);
