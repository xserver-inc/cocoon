#!/usr/bin/env node
/**
 * WP-CLI Phar をダウンロードして cocoon.pot を生成するセットアップスクリプト
 * 使い方: node scripts/setup-wpcli.js
 *   または: npm run make-pot
 */

// Node.js 標準モジュールの読み込み
const fs = require( 'fs' );
const path = require( 'path' );
const https = require( 'https' );
const { execSync } = require( 'child_process' );

// WP-CLI Phar の配置先パスを定義
const VENDOR_BIN_DIR = path.join( __dirname, '..', 'vendor', 'bin' );
const WP_CLI_PHAR = path.join( VENDOR_BIN_DIR, 'wp-cli.phar' );

// WP-CLI Phar のダウンロード URL
const WP_CLI_URL =
  'https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar';

// テーマのルートディレクトリを定義
const THEME_DIR = path.join( __dirname, '..' );

/**
 * ファイルを HTTPS でダウンロードする関数
 * @param {string} url  - ダウンロード元の URL
 * @param {string} dest - 保存先のファイルパス
 * @returns {Promise<void>}
 */
function downloadFile( url, dest ) {
  return new Promise( ( resolve, reject ) => {
    // 保存先ディレクトリが存在しない場合は作成する
    const dir = path.dirname( dest );
    if ( ! fs.existsSync( dir ) ) {
      fs.mkdirSync( dir, { recursive: true } );
    }

    const file = fs.createWriteStream( dest );

    // HTTPSリダイレクトを自動的に追跡する関数
    function get( requestUrl ) {
      https
        .get( requestUrl, ( response ) => {
          // 301/302 リダイレクトの場合は新しい URL に再リクエスト
          if ( response.statusCode === 301 || response.statusCode === 302 ) {
            get( response.headers.location );
            return;
          }
          // 200 以外はエラーとして処理する
          if ( response.statusCode !== 200 ) {
            reject(
              new Error( `ダウンロード失敗: HTTP ${ response.statusCode }` )
            );
            return;
          }
          // レスポンスをファイルに書き込む
          response.pipe( file );
          file.on( 'finish', () => {
            file.close( resolve );
          } );
        } )
        .on( 'error', ( err ) => {
          // エラー発生時は不完全なファイルを削除する
          fs.unlink( dest, () => {} );
          reject( err );
        } );
    }

    get( url );
  } );
}

// メイン処理
async function main() {
  // WP-CLI Phar がすでに存在するか確認する
  if ( ! fs.existsSync( WP_CLI_PHAR ) ) {
    console.log( '📥 WP-CLI をダウンロード中...' );
    console.log( `   URL: ${ WP_CLI_URL }` );
    console.log( `   保存先: ${ WP_CLI_PHAR }` );

    try {
      await downloadFile( WP_CLI_URL, WP_CLI_PHAR );
      console.log( '✅ WP-CLI のダウンロードが完了しました。\n' );
    } catch ( err ) {
      console.error(
        `❌ WP-CLI のダウンロードに失敗しました: ${ err.message }`
      );
      process.exit( 1 );
    }
  } else {
    console.log(
      '✅ WP-CLI はすでに存在します。ダウンロードをスキップします。\n'
    );
  }

  // WP-CLIのバージョンを確認する
  try {
    const version = execSync( `php "${ WP_CLI_PHAR }" --version`, {
      encoding: 'utf8',
    } ).trim();
    console.log( `🔧 ${ version }` );
  } catch ( e ) {
    console.warn( '⚠️  WP-CLI バージョン確認に失敗しました。続行します。' );
  }

  // make-pot コマンドの引数を定義する
  const excludePaths = [
    'node_modules',
    'vendor',
    'tests',
    'scripts',
    'tmp/css-custom.php',
    'plugins', // サードパーティJSプラグイン（minified含む）を除外
    'fonts', // フォントファイルを除外
    'icomoon', // IcoMoon フォントJSを除外
  ].join( ',' );

  // WP-CLI make-pot コマンドを組み立てる
  const command = [
    `php "${ WP_CLI_PHAR }"`,
    'i18n make-pot',
    `"${ THEME_DIR }"`, // スキャン対象ディレクトリ
    `"${ path.join( THEME_DIR, 'languages', 'cocoon.pot' ) }"`, // 出力先
    '--slug=cocoon', // テーマのスラッグ
    '--domain=cocoon', // テキストドメイン
    '--ignore-domain', // ドメインを無視して全文字列を抽出
    `--exclude="${ excludePaths }"`, // 除外するパス
    // PHP 8.5 では mck89/peast ライブラリの互換性問題でJSパースがクラッシュするためスキップ
    // blocks/ のJS翻訳は -cocoon-blocks-js.json で別管理されているため影響なし
    '--skip-js',
  ].join( ' ' );

  console.log( '\n🚀 cocoon.pot を生成中...' );
  console.log( `   コマンド: ${ command }\n` );

  try {
    // make-pot コマンドを実行して出力をリアルタイムで表示する
    execSync( command, { stdio: 'inherit', cwd: THEME_DIR } );
    console.log( '\n✅ languages/cocoon.pot の更新が完了しました！' );
  } catch ( err ) {
    console.error( '\n❌ POT 生成に失敗しました。' );
    process.exit( 1 );
  }
}

main();
