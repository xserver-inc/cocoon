import {
  createBlock,
  getBlockType,
  parse,
  registerBlockType,
  serialize,
  setCategories,
  unregisterBlockType,
} from '@wordpress/blocks';

// deprecated定義が参照するWordPressグローバルをテスト環境へ用意します。
global.wp = {
  blocks: { createBlock },
  components: {},
};
global.gbSettings = { speechBalloonDefaultIconUrl: '' };
global.gbSpeechBalloons = [
  {
    name: '設定名',
    id: '7',
    icon: 'https://example.com/default.png',
    style: 'stn',
    position: 'l',
    iconstyle: 'cb',
    visible: '1',
    title: '設定名',
  },
];

const futureCoreStyle = {
  css: 'margin-bottom:50px;',
  '@mobile': { ':hover': { color: { text: '#123456' } } },
  futureKey: [ 'alpha', { beta: true } ],
};

const CHILD_BLOCK_NAME = 'test/legacy-style-child';

const fixedNestedFixture =
  '<!-- wp:cocoon-blocks/blank-box {"style":"blank-box bb-yellow","className":"fixture-user-class"} -->\n<div class="wp-block-cocoon-blocks-blank-box blank-box bb-yellow block-box fixture-user-class"><!-- wp:test/legacy-style-child -->\n<p class="legacy-style-child">固定fixture本文</p>\n<!-- /wp:test/legacy-style-child --></div>\n<!-- /wp:cocoon-blocks/blank-box -->';

// 同じsaveからfixtureを生成せず、WordPress 7.0で実際に壊れた保存形式を文字列で固定します。
const fixedFixtures = [
  {
    label: '旧白抜きボックス',
    modulePath: './old/blank-box/block',
    blockName: 'cocoon-blocks/blank-box',
    attributeName: 'boxStyle',
    legacyValue: 'blank-box',
    content:
      '<!-- wp:cocoon-blocks/blank-box {"style":{"0":"b","1":"l","2":"a","3":"n","4":"k","5":"-","6":"b","7":"o","8":"x","css":"margin-bottom:50px;","@mobile":{":hover":{"color":{"text":"#123456"}}},"futureKey":["alpha",{"beta":true}]}} -->\n<div class="wp-block-cocoon-blocks-blank-box [object Object] block-box has-custom-css"></div>\n<!-- /wp:cocoon-blocks/blank-box -->',
  },
  {
    label: '旧タブボックス',
    modulePath: './old/tab-box/block',
    blockName: 'cocoon-blocks/tab-box',
    attributeName: 'boxStyle',
    legacyValue: 'blank-box bb-tab bb-check',
    content:
      '<!-- wp:cocoon-blocks/tab-box {"style":{"0":"b","1":"l","2":"a","3":"n","4":"k","5":"-","6":"b","7":"o","8":"x","9":" ","10":"b","11":"b","12":"-","13":"t","14":"a","15":"b","16":" ","17":"b","18":"b","19":"-","20":"c","21":"h","22":"e","23":"c","24":"k","css":"margin-bottom:50px;","@mobile":{":hover":{"color":{"text":"#123456"}}},"futureKey":["alpha",{"beta":true}]},"color":" bb-red"} -->\n<div class="wp-block-cocoon-blocks-tab-box [object Object] bb-red block-box has-custom-css"></div>\n<!-- /wp:cocoon-blocks/tab-box -->',
  },
  {
    label: '旧マイクロバルーン',
    modulePath: './old/micro-balloon/block',
    blockName: 'cocoon-blocks/micro-balloon',
    attributeName: 'balloonStyle',
    legacyValue: 'micro-balloon',
    content:
      '<!-- wp:cocoon-blocks/micro-balloon {"content":"固定本文","style":{"0":"m","1":"i","2":"c","3":"r","4":"o","5":"-","6":"b","7":"a","8":"l","9":"l","10":"o","11":"o","12":"n","css":"margin-bottom:50px;","@mobile":{":hover":{"color":{"text":"#123456"}}},"futureKey":["alpha",{"beta":true}]},"color":" mc-blue","isCircle":true} -->\n<div class="wp-block-cocoon-blocks-micro-balloon [object Object] mc-blue mc-circle micro-copy block-box has-custom-css">固定本文</div>\n<!-- /wp:cocoon-blocks/micro-balloon -->',
  },
  {
    label: '旧マイクロバルーン1',
    modulePath: './old/micro-balloon-1/block',
    blockName: 'cocoon-blocks/micro-balloon-1',
    attributeName: 'balloonStyle',
    legacyValue: ' micro-top',
    content:
      '<!-- wp:cocoon-blocks/micro-balloon-1 {"content":"固定本文","style":{"0":" ","1":"m","2":"i","3":"c","4":"r","5":"o","6":"-","7":"t","8":"o","9":"p","css":"margin-bottom:50px;","@mobile":{":hover":{"color":{"text":"#123456"}}},"futureKey":["alpha",{"beta":true}]},"color":" mc-blue","isCircle":true} -->\n<div class="wp-block-cocoon-blocks-micro-balloon-1 micro-balloon[object Object] mc-blue mc-circle micro-copy block-box has-custom-css">固定本文</div>\n<!-- /wp:cocoon-blocks/micro-balloon-1 -->',
  },
  {
    label: '旧吹き出し2',
    modulePath: './old/balloon-2/block',
    blockName: 'cocoon-blocks/balloon-box-2',
    attributeName: 'balloonStyle',
    legacyValue: 'stn',
    content:
      '<!-- wp:cocoon-blocks/balloon-box-2 {"name":"固定名","id":"fixture-id","icon":"https://example.com/icon.png","style":{"0":"s","1":"t","2":"n","css":"margin-bottom:50px;","@mobile":{":hover":{"color":{"text":"#123456"}}},"futureKey":["alpha",{"beta":true}]},"position":"r"} -->\n<div class="wp-block-cocoon-blocks-balloon-box-2 speech-wrap sb-id-fixture-id sbs-[object Object] sbp-r sbis-cb cf block-box has-custom-css"><div class="speech-person"><figure class="speech-icon"><img src="https://example.com/icon.png" alt="固定名" class="speech-icon-image"/></figure><div class="speech-name">固定名</div></div><div class="speech-balloon"></div></div>\n<!-- /wp:cocoon-blocks/balloon-box-2 -->',
  },
  {
    label: '旧吹き出しEX',
    modulePath: './old/balloon-ex/block',
    blockName: 'cocoon-blocks/balloon-ex-box',
    attributeName: 'balloonStyle',
    legacyValue: 'stn',
    content:
      '<!-- wp:cocoon-blocks/balloon-ex-box {"name":"固定名","style":{"0":"s","1":"t","2":"n","css":"margin-bottom:50px;","@mobile":{":hover":{"color":{"text":"#123456"}}},"futureKey":["alpha",{"beta":true}]},"position":"r","icon":"https://example.com/icon.png"} -->\n<div class="wp-block-cocoon-blocks-balloon-ex-box speech-wrap sb-id-7 sbs-[object Object] sbp-r sbis-cb cf block-box has-custom-css"><div class="speech-person"><figure class="speech-icon"><img src="https://example.com/icon.png" alt="" class="speech-icon-image"/></figure><div class="speech-name">固定名</div></div><div class="speech-balloon"></div></div>\n<!-- /wp:cocoon-blocks/balloon-ex-box -->',
  },
];

describe( '挿入不可の旧6ブロックの固定fixture往復互換性', () => {
  beforeAll( () => {
    setCategories( [ { slug: 'cocoon-old', title: 'Cocoon旧ブロック' } ] );
    registerBlockType( CHILD_BLOCK_NAME, {
      apiVersion: 3,
      title: CHILD_BLOCK_NAME,
      category: 'cocoon-old',
      edit: () => null,
      save: () => <p className="legacy-style-child">固定fixture本文</p>,
    } );
    fixedFixtures.forEach( ( { modulePath } ) => require( modulePath ) );
  } );

  afterAll( () => {
    fixedFixtures.forEach( ( { blockName } ) => {
      unregisterBlockType( blockName );
    } );
    unregisterBlockType( CHILD_BLOCK_NAME );
  } );

  test.each( fixedFixtures )(
    '$label は破損styleを復元しCSSと将来キーを保持する',
    ( { attributeName, legacyValue, content } ) => {
      const parsedBlock = parse( content )[ 0 ];

      expect( parsedBlock ).toBeDefined();
      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.attributes ).toMatchObject( {
        [ attributeName ]: legacyValue,
        style: futureCoreStyle,
      } );

      const serializedBlock = serialize( parsedBlock );
      expect( serializedBlock ).not.toContain( '[object Object]' );
      expect( serializedBlock ).toContain( 'margin-bottom:50px;' );
      expect( serializedBlock ).toContain( '"@mobile"' );
      expect( serializedBlock ).toContain( '"futureKey"' );
      expect( console ).toHaveInformed();
    }
  );

  test( '正常な旧style文字列もユーザークラスとInnerBlocksを保って移行する', () => {
    const parsedBlock = parse( fixedNestedFixture )[ 0 ];

    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( {
      boxStyle: 'blank-box bb-yellow',
      className: 'fixture-user-class',
    } );
    expect( parsedBlock.attributes ).not.toHaveProperty( 'style' );
    expect( parsedBlock.innerBlocks ).toHaveLength( 1 );
    expect( parsedBlock.innerBlocks[ 0 ] ).toMatchObject( {
      name: CHILD_BLOCK_NAME,
      isValid: true,
    } );
    expect( serialize( parsedBlock ) ).toContain( '固定fixture本文' );
  } );

  test.each( fixedFixtures )(
    '$label のdeprecated saveは現行saveから固定分離されている',
    ( { blockName } ) => {
      const blockType = getBlockType( blockName );
      expect( blockType.deprecated[ 0 ].save ).not.toBe( blockType.save );
    }
  );
} );
