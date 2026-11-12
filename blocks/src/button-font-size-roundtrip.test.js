import {
  createBlock,
  getBlockType,
  parse,
  registerBlockType,
  serialize,
  unregisterBlockType,
} from '@wordpress/blocks';

// 旧保存形式が参照するWordPressグローバルの定義
global.wp = { blocks: { createBlock }, components: {} };

const buttonCases = [
  {
    label: 'ボタン',
    metadata: require( './block/button/block.json' ),
    save: require( './block/button/save' ).default,
    deprecated: require( './block/button/deprecated' ).default,
    base: { content: '通常ボタン', url: '/' },
  },
  {
    label: '囲みボタン',
    metadata: require( './block/button-wrap/block.json' ),
    save: require( './block/button-wrap/save' ).default,
    deprecated: require( './block/button-wrap/deprecated' ).default,
    base: { tag: '<a href="/">囲みボタン</a>' },
  },
];

// 保存HTMLを変更せずに属性とリンクを検査するためのDOM
function savedElements( content ) {
  const template = document.createElement( 'template' );
  template.innerHTML = content;
  return {
    wrapper: template.content.querySelector( 'div' ),
    link: template.content.querySelector( 'a' ),
  };
}

describe.each( buttonCases )( '$labelのカスタム文字サイズ', ( definition ) => {
  const { metadata, save, deprecated, base } = definition;
  beforeAll( () => {
    registerBlockType( metadata.name, {
      ...metadata,
      title: metadata.name,
      category: 'text',
      edit: () => null,
      save,
      deprecated,
    } );
  } );
  afterAll( () => {
    if ( getBlockType( metadata.name ) ) unregisterBlockType( metadata.name );
  } );

  test.each( [
    [ '旧版の数値', 24, '24px' ],
    [ '小数', 24.5, '24.5px' ],
    [ '旧版の数値文字列', '24', '24px' ],
    [ 'px単位', '24px', '24px' ],
    [ 'em単位', '1.5em', '1.5em' ],
    [ 'rem単位', '2rem', '2rem' ],
    [ 'vw単位', '3vw', '3vw' ],
  ] )( '%sを保存・再解析して表示用HTMLへ保持できる', ( label, value, cssValue ) => {
    const original = createBlock( metadata.name, { ...base, customFontSize: value } );
    const content = serialize( original );
    const block = parse( content )[ 0 ];
    expect( block.isValid ).toBe( true );
    expect( block.attributes.customFontSize ).toBe( value );
    expect( serialize( block ) ).toBe( content );
    const { wrapper, link } = savedElements( content );
    const target = metadata.name.endsWith( 'button-1' ) ? link : wrapper;
    expect( target.style.fontSize ).toBe( cssValue );
    expect( link.textContent ).toBe( base.content || '囲みボタン' );
  } );

  test( '定型サイズの既存HTMLとクラスを変更しない', () => {
    const block = createBlock( metadata.name, { ...base, fontSize: 'large' } );
    const content = serialize( block );
    const parsed = parse( content )[ 0 ];
    const { wrapper, link } = savedElements( content );
    const target = metadata.name.endsWith( 'button-1' ) ? link : wrapper;
    expect( parsed.isValid ).toBe( true );
    expect( target.classList.contains( 'has-large-font-size' ) ).toBe( true );
    expect( target.hasAttribute( 'style' ) ).toBe( false );
    expect( parsed.attributes.customFontSize ).toBeUndefined();
    expect( serialize( parsed ) ).toBe( content );
  } );

  test( '定型サイズへの切り替え時に残ったカスタム値で上書きしない', () => {
    const content = serialize( createBlock( metadata.name, {
      ...base, fontSize: 'large', customFontSize: 24,
    } ) );
    const { wrapper, link } = savedElements( content );
    expect( ( metadata.name.endsWith( 'button-1' ) ? link : wrapper ).style.fontSize ).toBe( '' );
  } );

  test( 'リセット後は数値とインライン文字サイズを保存しない', () => {
    const block = createBlock( metadata.name, { ...base, customFontSize: 24 } );
    const content = serialize( { ...block, attributes: { ...block.attributes, customFontSize: undefined } } );
    const parsed = parse( content )[ 0 ];
    expect( parsed.isValid ).toBe( true );
    expect( parsed.attributes.customFontSize ).toBeUndefined();
    expect( content ).not.toContain( 'font-size:' );
    expect( content ).not.toContain( 'customFontSize' );
  } );
} );

describe( '囲みボタンの旧カスタム文字サイズの復元', () => {
  const definition = buttonCases[ 1 ];
  beforeAll( () => {
    registerBlockType( definition.metadata.name, {
      ...definition.metadata, title: '囲みボタン', category: 'text', edit: () => null,
      save: definition.save, deprecated: definition.deprecated,
    } );
  } );
  afterAll( () => unregisterBlockType( definition.metadata.name ) );

  test.each( [ 24, '24', '24px', '1.5em' ] )( '文字サイズ%sを旧HTMLから失わずに移行できる', ( customFontSize ) => {
    // 文字サイズのstyle属性がなかった修正前の保存形式
    const content = `<!-- wp:cocoon-blocks/button-wrap-1 ${ JSON.stringify( { customFontSize } ) } -->\n<div class="wp-block-cocoon-blocks-button-wrap-1 btn-wrap btn-wrap-block button-block"></div>\n<!-- /wp:cocoon-blocks/button-wrap-1 -->`;
    const block = parse( content )[ 0 ];
    expect( block.isValid ).toBe( true );
    expect( block.attributes.customFontSize ).toBe( customFontSize );
    expect( serialize( block ) ).toContain( 'font-size:' );
    expect( parse( serialize( block ) )[ 0 ].isValid ).toBe( true );
    if ( console.info.mock.calls.length ) expect( console ).toHaveInformed();
  } );

  test.each( [ false, true ] )( '旧リンクの空保存状態%sでも文字サイズと本文を復元できる', ( empty ) => {
    const tag = '<a href="/" target="_blank" rel="noopener">旧ボタン</a>';
    // WordPressのコメント内属性形式を再現した旧記事データ
    const attributes = JSON.stringify( { tag, customFontSize: 24 } )
      .replace( /</g, '\\u003c' ).replace( />/g, '\\u003e' ).replace( /\\"/g, '\\u0022' );
    const content = `<!-- wp:cocoon-blocks/button-wrap-1 ${ attributes } -->\n<div class="wp-block-cocoon-blocks-button-wrap-1 btn-wrap btn-wrap-block button-block">${ empty ? '' : tag }</div>\n<!-- /wp:cocoon-blocks/button-wrap-1 -->`;
    const block = parse( content )[ 0 ];
    expect( block.isValid ).toBe( true );
    expect( block.attributes.customFontSize ).toBe( 24 );
    expect( block.attributes.tag ).toBe( tag );
    const saved = serialize( block );
    const { wrapper, link } = savedElements( saved );
    expect( wrapper.style.fontSize ).toBe( '24px' );
    expect( link.textContent ).toBe( '旧ボタン' );
    expect( parse( saved )[ 0 ].isValid ).toBe( true );
    if ( console.info.mock.calls.length ) expect( console ).toHaveInformed();
  } );
} );
