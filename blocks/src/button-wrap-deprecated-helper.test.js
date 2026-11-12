import {
  createBlock,
  getSaveContent,
  registerBlockType,
  unregisterBlockType,
} from '@wordpress/blocks';

// 現行ヘルパーへの依存が再発した場合の読み込み失敗による検知
jest.mock( './block/button-wrap/add-noopener', () => {
  throw new Error( '旧保存形式からの現行ヘルパー読み込み' );
} );

// 旧保存形式が参照するWordPressグローバルの定義
global.wp = { blocks: { createBlock }, components: {} };

const metadata = require( './block/button-wrap/block.json' );
const { preCustomFontSize } = require( './block/button-wrap/deprecated' );
const {
  addNoopenerToBlankTargetsV1,
} = require( './block/button-wrap/deprecated/add-noopener-v1' );

const fixtures = [
  [
    'リンクの既存属性とエンティティ',
    '<a href="https://example.com/?x=1&amp;y=2" target="_blank">日本語</a>',
    '<a href="https://example.com/?x=1&amp;y=2" target="_blank" rel="noopener">日本語</a>',
  ],
  [
    '既存のrel属性',
    "<a rel='nofollow noreferrer' target='_blank' href='/'>詳細</a>",
    "<a rel='nofollow noreferrer noopener' target='_blank' href='/'>詳細</a>",
  ],
  [
    '大文字の属性と文字参照',
    '<a TARGET="&#95;blank" href="/">旧記事</a>',
    '<a TARGET="&#95;blank" rel="noopener" href="/">旧記事</a>',
  ],
  [
    '既存のnoopener',
    '<a target="_blank" rel="noopener nofollow" href="/">詳細</a>',
    '<a target="_blank" rel="noopener nofollow" href="/">詳細</a>',
  ],
  [
    'コメント内のリンク風文字列',
    '<!-- <a target="_blank">旧記事</a> -->',
    '<!-- <a target="_blank">旧記事</a> -->',
  ],
  [
    'スクリプト内のリンク風文字列',
    '<script>const text = `<a target="_blank">旧記事</a>`;</script>',
    '<script>const text = `<a target="_blank">旧記事</a>`;</script>',
  ],
];

describe( '囲みボタンの旧保存形式専用ヘルパー', () => {
  beforeAll( () => {
    registerBlockType( metadata.name, {
      ...metadata,
      ...preCustomFontSize,
      title: '囲みボタンの旧保存形式',
      category: 'text',
      edit: () => null,
    } );
  } );

  afterAll( () => unregisterBlockType( metadata.name ) );

  test.each( fixtures )( '%sを固定仕様どおり補完できる', ( label, input, expected ) => {
    expect( addNoopenerToBlankTargetsV1( input ) ).toBe( expected );
  } );

  test.each( fixtures.slice( 0, 4 ) )( '%sの旧保存HTMLが現行ヘルパーから独立している', ( label, tag, expected ) => {
    const block = createBlock( metadata.name, { tag, customFontSize: '24px' } );
    expect( getSaveContent( metadata.name, block.attributes ) ).toBe(
      `<div class="wp-block-cocoon-blocks-button-wrap-1 btn-wrap btn-wrap-block button-block">${ expected }</div>`
    );
  } );
} );
