// __()（コンテキストなし）とblock description\u0004（コンテキストあり）の両方を確認
const path = require( 'path' );
const LANG_DIR = path.join( __dirname, '..', 'languages' );
const j = require( path.join(
  LANG_DIR,
  'cocoon-en_US-cocoon-blocks-js.json'
) );
const m = j.locale_data.messages;
// __() が参照するコンテキストなしキー
const k1 =
  'Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。';
const k2 = '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。';
// block description コンテキストつきキー
const k3 =
  'block description\u0004Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。';
console.log( 'コンテキストなし(Amazon):', JSON.stringify( m[ k1 ] ) );
console.log( 'コンテキストなし(楽天):', JSON.stringify( m[ k2 ] ) );
console.log( 'コンテキストあり(Amazon):', JSON.stringify( m[ k3 ] ) );
