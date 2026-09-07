import {
  createBlock,
  getBlockType,
  parse,
  registerBlockType,
  serialize,
  unregisterBlockType,
} from '@wordpress/blocks';

// deprecated定義が参照するWordPressグローバルをテスト環境へ用意します。
global.wp = {
  blocks: { createBlock },
  components: {},
};

const metadata = require( './block/button/block.json' );
const save = require( './block/button/save' ).default;
const deprecated = require( './block/button/deprecated' ).default;

const BLOCK_NAME = metadata.name;

// フォーラム報告89430で提示された、太字と改行が混在する現行保存形式を固定します。
const MIXED_FORMAT_FIXTURE =
  '<!-- wp:cocoon-blocks/button-1 {"content":"\\u003cstrong\\u003e購入\\u003c/strong\\u003eは公式サイトで\\u003cbr\\u003eリンクは\\u003cstrong\\u003eこちら\\u003c/strong\\u003e","size":"btn-l","backgroundColor":"key-color"} -->\n' +
  '<div class="wp-block-cocoon-blocks-button-1 button-block"><a href="" class="btn btn-l has-background has-key-color-background-color" target="_self" rel="noopener"><strong>購入</strong>は公式サイトで<br>リンクは<strong>こちら</strong></a></div>\n' +
  '<!-- /wp:cocoon-blocks/button-1 -->';

// 本番と同じ属性・保存処理・deprecated定義でブロックを登録します。
const registerCurrentDefinition = () => {
  registerBlockType( BLOCK_NAME, {
    apiVersion: metadata.apiVersion,
    title: BLOCK_NAME,
    category: 'text',
    attributes: metadata.attributes,
    supports: metadata.supports,
    edit: () => null,
    save,
    deprecated,
  } );
};

describe( 'ボタンブロックの装飾・改行混在コンテンツ', () => {
  beforeAll( () => {
    registerCurrentDefinition();
  } );

  afterAll( () => {
    if ( getBlockType( BLOCK_NAME ) ) {
      unregisterBlockType( BLOCK_NAME );
    }
  } );

  test( 'フォーラム報告の保存HTMLを有効な現行ブロックとして往復できる', () => {
    const parsedBlock = parse( MIXED_FORMAT_FIXTURE )[ 0 ];

    expect( parsedBlock ).toBeDefined();
    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( {
      content:
        '<strong>購入</strong>は公式サイトで<br>リンクは<strong>こちら</strong>',
      size: 'btn-l',
      backgroundColor: 'key-color',
    } );
    expect( serialize( parsedBlock ) ).toBe( MIXED_FORMAT_FIXTURE );
  } );
} );
