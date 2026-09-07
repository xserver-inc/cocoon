const fs = require( 'fs' );
const path = require( 'path' );

const DIST_DIRECTORY = path.resolve( __dirname, '../dist' );
const ASSET_PATH = path.join( DIST_DIRECTORY, 'blocks.build.asset.php' );
const BUNDLE_PATH = path.join( DIST_DIRECTORY, 'blocks.build.js' );

// 追跡対象の生成物を実ファイルから読み込む固定入力。
const assetSource = fs.readFileSync( ASSET_PATH, 'utf8' );
const bundleSource = fs.readFileSync( BUNDLE_PATH, 'utf8' );

// asset PHPのdependencies配列だけを文字列として抽出する処理。
const dependencyListSource = assetSource.match(
  /'dependencies' => array\(([^)]*)\)/
);
const dependencies = Array.from(
  dependencyListSource?.[ 1 ].matchAll( /'([^']+)'/g ) || [],
  ( match ) => match[ 1 ]
);

describe( 'ブロック生成物のWordPress依存', () => {
  test( '共有SSRとDOM準備依存を宣言する', () => {
    expect( dependencies ).toEqual(
      expect.arrayContaining( [ 'wp-dom-ready', 'wp-server-side-render' ] )
    );
  } );

  test( '旧エディター依存を宣言しない', () => {
    expect( dependencies ).not.toContain( 'wp-editor' );
    expect( dependencies ).not.toContain( 'wp-edit-post' );
  } );

  test( '生成JSが共有SSRグローバルだけを参照する', () => {
    expect( bundleSource ).toContain( 'window.wp.serverSideRender' );
    expect( bundleSource ).not.toContain( 'window.wp.editor' );
  } );

  test( '生成物バージョンを20桁の16進ハッシュで保持する', () => {
    expect( assetSource ).toMatch( /'version' => '[0-9a-f]{20}'/ );
  } );
} );
