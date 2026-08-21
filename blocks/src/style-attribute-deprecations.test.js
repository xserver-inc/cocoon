import {
  createBlock,
  getBlockType,
  setCategories,
  unregisterBlockType,
} from '@wordpress/blocks';

// deprecated定義が参照するWordPressグローバルをテスト環境へ用意します。
global.wp = {
  blocks: { createBlock },
  components: {},
};

const compatibilityCases = [
  [
    'アイコンボックス',
    require( './block/icon-box/deprecated' ).default,
    'boxStyle',
    'question-box',
  ],
  [
    '案内ボックス',
    require( './block/info-box/deprecated' ).default,
    'boxStyle',
    'success-box',
  ],
  [
    '付箋風ボックス',
    require( './block/sticky-box/deprecated' ).default,
    'boxStyle',
    'st-yellow',
    1,
  ],
  [
    'ブログカード',
    require( './block/blogcard/deprecated' ).default,
    'cardStyle',
    'blogcard-type bct-related',
  ],
  [
    '吹き出し',
    require( './block/balloon/deprecated' ).default,
    'balloonStyle',
    'flat',
  ],
];

const legacyBlockCases = [
  [
    '旧白抜きボックス',
    './old/blank-box/block',
    'cocoon-blocks/blank-box',
    'boxStyle',
  ],
  [
    '旧タブボックス',
    './old/tab-box/block',
    'cocoon-blocks/tab-box',
    'boxStyle',
  ],
  [
    '旧マイクロバルーン',
    './old/micro-balloon/block',
    'cocoon-blocks/micro-balloon',
    'balloonStyle',
  ],
  [
    '旧マイクロバルーン1',
    './old/micro-balloon-1/block',
    'cocoon-blocks/micro-balloon-1',
    'balloonStyle',
  ],
  [
    '旧吹き出し2',
    './old/balloon-2/block',
    'cocoon-blocks/balloon-box-2',
    'balloonStyle',
  ],
  [
    '旧吹き出しEX',
    './old/balloon-ex/block',
    'cocoon-blocks/balloon-ex-box',
    'balloonStyle',
  ],
];

describe( '旧style属性のdeprecated定義', () => {
  test.each( compatibilityCases )(
    '%s は正常な旧記事を現行属性へ直接移行する',
    ( blockName, deprecated, attributeName, legacyValue, legacyIndex = 0 ) => {
      const legacyDefinition = deprecated[ legacyIndex ];

      expect( legacyDefinition ).toMatchObject( {
        apiVersion: 3,
        supports: { anchor: true, html: false },
      } );
      expect( legacyDefinition.attributes.style ).toEqual( {
        type: [ 'string', 'object' ],
        default: expect.any( String ),
      } );
      expect( legacyDefinition.isEligible( { style: legacyValue } ) ).toBe(
        true
      );
      expect(
        legacyDefinition.migrate( {
          style: legacyValue,
          anchor: 'sample',
        } )
      ).toEqual( {
        [ attributeName ]: legacyValue,
        anchor: 'sample',
      } );
    }
  );

  test.each( compatibilityCases )(
    '%s はWordPress 7.0で壊れた記事とカスタムCSSを復元する',
    ( blockName, deprecated, attributeName, legacyValue, legacyIndex = 0 ) => {
      const legacyDefinition = deprecated[ legacyIndex ];

      expect( legacyDefinition ).toMatchObject( {
        apiVersion: 3,
        supports: { anchor: true, html: false },
      } );
      expect(
        legacyDefinition.isEligible( {
          style: { ...legacyValue, css: 'margin-bottom:50px;' },
        } )
      ).toBe( true );
      expect(
        legacyDefinition.migrate( {
          style: { ...legacyValue, css: 'margin-bottom:50px;' },
        } )
      ).toEqual( {
        [ attributeName ]: legacyValue,
        style: { css: 'margin-bottom:50px;' },
      } );
    }
  );

  test.each( compatibilityCases )(
    '%s の全deprecated定義は現行形式への移行処理を持つ',
    ( blockName, deprecated ) => {
      deprecated.forEach( ( definition ) => {
        expect( definition.migrate ).toEqual( expect.any( Function ) );
      } );
    }
  );
} );

describe( '挿入不可の旧ブロックに対する予防措置', () => {
  beforeAll( () => {
    global.gbSettings = { speechBalloonDefaultIconUrl: '' };
    global.gbSpeechBalloons = [];
    setCategories( [ { slug: 'cocoon-old', title: 'Cocoon旧ブロック' } ] );

    // 実際の登録設定を読み込み、旧styleとコアstyleが分離されることを検証します。
    legacyBlockCases.forEach( ( [ , modulePath ] ) => require( modulePath ) );
  } );

  afterAll( () => {
    legacyBlockCases.forEach( ( [ , , blockName ] ) => {
      unregisterBlockType( blockName );
    } );
  } );

  test.each( legacyBlockCases )(
    '%s は専用属性とコアstyleを分離して追加CSSを利用可能にする',
    ( label, modulePath, blockName, attributeName ) => {
      const blockType = getBlockType( blockName );

      expect( blockType.supports ).toMatchObject( { inserter: false } );
      expect( blockType.supports.customCSS ).not.toBe( false );
      expect( blockType.attributes[ attributeName ] ).toMatchObject( {
        type: 'string',
      } );
      expect( blockType.attributes.style ).toEqual( { type: 'object' } );
    }
  );

  test.each( legacyBlockCases )(
    '%s は破損した表示styleを改名しコアstyleを欠落なく保持する',
    ( label, modulePath, blockName, attributeName ) => {
      const blockType = getBlockType( blockName );
      const [ deprecation ] = blockType.deprecated;
      const futureStyle = {
        '@mobile': { ':hover': { color: { text: '#123456' } } },
        elements: { link: { color: { text: '#abcdef' } } },
        responsive: { viewport: [ { minWidth: '480px' } ] },
      };
      const brokenStyle = {
        0: 's',
        1: 't',
        2: 'n',
        css: 'color:red;',
        ...futureStyle,
      };

      expect( deprecation.attributes.style.type ).toEqual( [
        'string',
        'object',
      ] );
      expect( deprecation.attributes ).not.toHaveProperty( attributeName );
      // 破損当時のHTMLを再現するため、deprecated側では追加CSSを無効化しません。
      expect( deprecation.supports.customCSS ).toBeUndefined();
      expect( deprecation.isEligible( { style: brokenStyle } ) ).toBe( true );
      expect( deprecation.isEligible( { style: 'stn' } ) ).toBe( true );
      expect( deprecation.migrate( { style: brokenStyle } ) ).toEqual( {
        [ attributeName ]: 'stn',
        style: { css: 'color:red;', ...futureStyle },
      } );
      expect( deprecation.save ).not.toBe( blockType.save );
    }
  );

  test.each( legacyBlockCases )(
    '%s はWordPress 7.1の正常なstyleオブジェクトを旧形式へ誤移行しない',
    ( label, modulePath, blockName ) => {
      const [ deprecation ] = getBlockType( blockName ).deprecated;

      expect(
        deprecation.isEligible( {
          style: {
            css: 'display:block;',
            '@mobile': { ':hover': { color: { text: '#123456' } } },
            elements: { link: { color: { text: '#abcdef' } } },
          },
        } )
      ).toBe( false );
    }
  );
} );
