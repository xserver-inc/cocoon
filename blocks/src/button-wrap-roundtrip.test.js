import {
  createBlock,
  getBlockType,
  getSaveContent,
  parse,
  registerBlockType,
  serialize,
  setCategories,
  unregisterBlockType,
} from '@wordpress/blocks';

// deprecated定義が参照するWordPressグローバルのテスト用定義。
global.wp = {
  blocks: { createBlock },
  components: {},
};

const metadata = require( './block/button-wrap/block.json' );
const save = require( './block/button-wrap/save' ).default;
const deprecatedModule = require( './block/button-wrap/deprecated' );
const deprecated = deprecatedModule.default;
const { v1 } = deprecatedModule;
const {
  addNoopenerToBlankTargets,
} = require( './block/button-wrap/add-noopener' );

const BLOCK_NAME = metadata.name;

// deprecated v2が保存された時点のblock.jsonから固定した属性スキーマ。
const V2_HISTORICAL_ATTRIBUTES = {
  tag: { type: 'string', default: '' },
  size: { type: 'string', default: '' },
  isCircle: { type: 'boolean', default: false },
  isShine: { type: 'boolean', default: false },
  align: { type: 'string' },
  backgroundColor: { type: 'string' },
  textColor: { type: 'string' },
  borderColor: { type: 'string' },
  customBorderColor: { type: 'string' },
  fontSize: { type: 'string' },
  customFontSize: { type: 'string' },
};

// deprecated v2が保存された時点のblock.jsonから固定したsupports。
const V2_HISTORICAL_SUPPORTS = {
  align: [ 'left', 'center', 'right' ],
  customClassName: true,
  anchor: true,
};

const BROKEN_TAG =
  '<a href="https://example.com" target="_blank" rel="noopener">日本語</a>';

// 現行の破損保存処理から採取した、属性にtagが残り内側HTMLだけが空の固定fixture。
const CURRENT_BROKEN_FIXTURE =
  '<!-- wp:cocoon-blocks/button-wrap-1 {"tag":"\\u003ca href=\\u0022https://example.com\\u0022 target=\\u0022_blank\\u0022 rel=\\u0022noopener\\u0022\\u003e日本語\\u003c/a\\u003e"} -->\n' +
  '<div class="wp-block-cocoon-blocks-button-wrap-1 btn-wrap btn-wrap-block button-block"></div>\n' +
  '<!-- /wp:cocoon-blocks/button-wrap-1 -->';

// 元データ自体が存在しない場合を、復元可能な破損形式と区別する固定fixture。
const EMPTY_FIXTURE =
  '<!-- wp:cocoon-blocks/button-wrap-1 -->\n' +
  '<div class="wp-block-cocoon-blocks-button-wrap-1 btn-wrap btn-wrap-block button-block"></div>\n' +
  '<!-- /wp:cocoon-blocks/button-wrap-1 -->';

// 各テストで同名ブロックを安全に登録し直すための登録解除処理。
const unregisterButtonWrap = () => {
  if ( getBlockType( BLOCK_NAME ) ) {
    unregisterBlockType( BLOCK_NAME );
  }
};

// 現行定義と旧定義の双方をGutenbergへ登録するための共通処理。
const registerDefinition = ( {
  save: saveImplementation,
  attributes = metadata.attributes,
  supports = metadata.supports,
  apiVersion = metadata.apiVersion,
  deprecated: deprecatedDefinitions = [],
} ) => {
  const settings = {
    title: BLOCK_NAME,
    category: 'text',
    attributes,
    supports,
    edit: () => null,
    save: saveImplementation,
    deprecated: deprecatedDefinitions,
  };

  // apiVersionが存在しない最古形式も再現するための条件付き設定。
  if ( apiVersion !== null ) {
    settings.apiVersion = apiVersion;
  }

  registerBlockType( BLOCK_NAME, settings );
};

// 本番metadata・save・deprecatedを組み合わせた現行登録処理。
const registerCurrentDefinition = () => {
  registerDefinition( {
    save,
    deprecated,
  } );
};

// 属性の初期値も通した実ブロックから保存HTMLを得るGutenberg実行経路。
const getCurrentSaveContent = ( attributes = {} ) => {
  const block = createBlock( BLOCK_NAME, attributes );

  return getSaveContent( BLOCK_NAME, block.attributes, block.innerBlocks );
};

// divラッパーの属性順や引用符を正規化せずに内側HTMLだけを取り出す処理。
const getWrapperInnerHTML = ( saveContent ) => {
  const openingTagEnd = saveContent.indexOf( '>' );
  const closingTagStart = saveContent.lastIndexOf( '</div>' );

  if ( openingTagEnd < 0 || closingTagStart < 0 ) {
    return '';
  }

  return saveContent.slice( openingTagEnd + 1, closingTagStart );
};

// 保存HTMLをブラウザーと同じ規則で検査するためのリンク要素取得処理。
const getSavedLinks = ( saveContent ) => {
  const template = document.createElement( 'template' );
  template.innerHTML = saveContent;

  return Array.from( template.content.querySelectorAll( 'a' ) );
};

// rel属性を大文字小文字を区別しない空白区切りトークンへ変換する処理。
const getRelTokens = ( link ) =>
  ( link.getAttribute( 'rel' ) || '' )
    .trim()
    .split( /\s+/ )
    .filter( Boolean )
    .map( ( token ) => token.toLowerCase() );

// Gutenbergの版差による移行通知の有無だけを明示確認する処理。
const acknowledgeParserInfo = () => {
  if ( global.console.info.mock.calls.length > 0 ) {
    expect( console ).toHaveInformed();
  } else {
    expect( console ).not.toHaveInformed();
  }
};

// 旧定義で保存してから現行定義へ戻す実際のdeprecated解析経路。
const createLegacyContent = ( definition, attributes, apiVersion ) => {
  unregisterButtonWrap();
  registerDefinition( {
    save: definition.save,
    attributes: definition.attributes || metadata.attributes,
    supports: definition.supports || metadata.supports,
    apiVersion,
  } );

  const legacyContent = serialize( createBlock( BLOCK_NAME, attributes ) );

  unregisterButtonWrap();
  registerCurrentDefinition();

  return legacyContent;
};

describe( '囲みボタンのHTML境界保護', () => {
  test.each( [
    [ 'script', '<script>const markup = `<a target="_blank">x</a>`;</script>' ],
    [
      'style',
      '<style>.sample::after { content: "<a target=\'_blank\'>x</a>"; }</style>',
    ],
    [ 'textarea', '<textarea><a target="_blank">x</a></textarea>' ],
    [ 'title', '<title><a target="_blank">x</a></title>' ],
    [ 'iframe', '<iframe><a target="_blank">x</a></iframe>' ],
    [ 'xmp', '<xmp><a target="_blank">x</a></xmp>' ],
    [ 'noembed', '<noembed><a target="_blank">x</a></noembed>' ],
    [ 'noframes', '<noframes><a target="_blank">x</a></noframes>' ],
    [ 'plaintext', '<plaintext><a target="_blank">x</a>' ],
  ] )( '%s要素内のリンク風文字列を変更しない', ( label, html ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( html );
  } );

  test.each( [
    [
      'SVG',
      '<svg><![CDATA[<a target=_blank>1</a><a target=_blank>2</a>]]></svg>',
    ],
    [
      'MathML',
      '<math><![CDATA[<a target=_blank>1</a><a target=_blank>2</a>]]></math>',
    ],
  ] )( '%sのCDATA内にあるリンク風文字列を変更しない', ( label, html ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( html );
  } );

  test( 'bogus comment内の引用符に惑わされず後続リンクを保護する', () => {
    const html = '<!x " ><a target=_blank>x</a>';

    expect( addNoopenerToBlankTargets( html ) ).toBe(
      '<!x " ><a target=_blank rel="noopener">x</a>'
    );
  } );

  test.each( [
    '</1<x y=" ><a target=_blank>x</a>">',
    '<!x<x y=" ><a target=_blank>x</a>">',
  ] )(
    'bogus comment内の偽開始タグに後続の実リンクを飲み込ませない',
    ( html ) => {
      expect( addNoopenerToBlankTargets( html ) ).toBe(
        html.replace( 'target=_blank', 'target=_blank rel="noopener"' )
      );
    }
  );

  test( '等号から始まる属性名をrelと誤認せず冪等に保護する', () => {
    const html = '<a target="_blank"=rel=noopener>x</a>';
    const expected = '<a target="_blank" rel="noopener"=rel=noopener>x</a>';
    const transformed = addNoopenerToBlankTargets( html );

    expect( transformed ).toBe( expected );
    expect( addNoopenerToBlankTargets( transformed ) ).toBe( expected );
  } );

  test.each( [
    '<div data-markup="<a target=_blank>">x</div>',
    '<script>const markup = `<form target=_blank>`;</script>',
  ] )( '実要素ではない点プローブ候補を変更しない', ( html ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( html );
  } );

  test( 'DOCTYPE内の引用符に惑わされず突然の終端後のリンクを保護する', () => {
    const html = '<!DOCTYPE html PUBLIC "x><a target=_blank>x</a>';

    expect( addNoopenerToBlankTargets( html ) ).toBe(
      '<!DOCTYPE html PUBLIC "x><a target=_blank rel="noopener">x</a>'
    );
  } );

  test.each( [
    '<!--><a target=_blank>x</a>',
    '<!--x--!><a target=_blank>x</a>',
  ] )( '特殊なコメント終了後のリンクを保護する', ( html ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe(
      html.replace( ' target=_blank', ' target=_blank rel="noopener"' )
    );
  } );

  test.each( [
    '</<a target=_blank>x</a>',
    '</!<a target=_blank>x</a>',
    '</1<a target=_blank>x</a>',
    '</ <a target=_blank>x</a>',
  ] )( '不正な終了タグ開始後のbogus commentを変更しない', ( html ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( html );
  } );

  test.each( [
    [
      '文字参照で指定したHTML annotation-xml内のstyle',
      '<math><annotation-xml encoding="text&sol;html"><style><a target=_blank>x</a></style></annotation-xml></math>',
    ],
    [
      'annotation-xml直下のSVG foreignObject内のstyle',
      '<math><annotation-xml encoding="text/html"><svg><foreignObject><style><a target=_blank>x</a></style></foreignObject></svg></annotation-xml></math>',
    ],
    [
      'SVGからHTMLへ抜けた後のstyle',
      '<svg><p><style><a target=_blank>x</a></style></p>',
    ],
    [
      'double-escaped状態を含むscript',
      '<script><!--<script></script><a target=_blank>x</a></script>',
    ],
  ] )( '%sのリンク風文字列を変更しない', ( label, html ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( html );
  } );

  test( 'encodingなしannotation-xml直下のSVG内にあるstyleを変更しない', () => {
    const html =
      '<math><annotation-xml><svg><foreignObject><style><a target=_blank>x</a></style></foreignObject></svg></annotation-xml></math>';

    expect( addNoopenerToBlankTargets( html ) ).toBe( html );
  } );

  test( 'special要素をまたぐ汎用終了タグでSVG名前空間を失わない', () => {
    const html = '<x><div><svg></x><![CDATA[</g><a target=_blank>x</a>]]>';

    expect( addNoopenerToBlankTargets( html ) ).toBe( html );
  } );

  test( '大量の未対応終了タグを実用時間で処理する', () => {
    const elementCount = 2_048;
    const prefix = '<div></div>'.repeat( elementCount );
    const unmatchedClosings = '</x>'.repeat( elementCount );
    const html = `${ prefix }${ unmatchedClosings }<a target=_blank>x</a>`;
    const startedAt = performance.now();

    const output = addNoopenerToBlankTargets( html );

    expect( performance.now() - startedAt ).toBeLessThan( 4_000 );
    expect( output ).toBe(
      `${ prefix }${ unmatchedClosings }<a target=_blank rel="noopener">x</a>`
    );
  } );

  test( 'probe識別子生成で入力全体を反復検索しない', () => {
    const html = `<a target=_blank data-value="${ 'x'.repeat(
      16_384
    ) }">x</a>`;
    const includesSpy = jest.spyOn( String.prototype, 'includes' );

    try {
      expect( addNoopenerToBlankTargets( html ) ).toBe(
        html.replace( 'target=_blank', 'target=_blank rel="noopener"' )
      );
      expect( includesSpy ).not.toHaveBeenCalled();
    } finally {
      includesSpy.mockRestore();
    }
  } );

  test.each( [
    [ '無視される入れ子form', '<form><form target=_blank>x</form>' ],
    [ 'select内で無視されるarea', '<select><area target=_blank></select>' ],
  ] )( '%sのリンク風開始タグを変更しない', ( label, html ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( html );
  } );

  test.each( [
    [
      'template',
      '<template><a target=_blank>x</a></template>',
      '<template><a target=_blank rel="noopener">x</a></template>',
    ],
    [
      '入れ子template',
      '<template><template><form target=_blank>x</form></template></template>',
      '<template><template><form target=_blank rel="noopener">x</form></template></template>',
    ],
    [
      'template内のnoscript',
      '<template><noscript><a target=_blank>x</a></noscript></template>',
      '<template><noscript><a target=_blank rel="noopener">x</a></noscript></template>',
    ],
  ] )( '%s内の実要素へnoopenerを追加する', ( label, html, expected ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( expected );
  } );

  test( '候補ごとにnoscript祖先を再走査しない', () => {
    const elementCount = 128;
    const prefix = '<noscript></noscript>' + '<div>'.repeat( elementCount );
    const links = '<area target=_blank>'.repeat( elementCount );
    const suffix = '</div>'.repeat( elementCount );
    const closestSpy = jest.spyOn(
      document.defaultView.Element.prototype,
      'closest'
    );

    try {
      expect( addNoopenerToBlankTargets( prefix + links + suffix ) ).toBe(
        prefix +
          '<area target=_blank rel="noopener">'.repeat( elementCount ) +
          suffix
      );
      expect( closestSpy ).not.toHaveBeenCalled();
    } finally {
      closestSpy.mockRestore();
    }
  } );

  test( '入れ子noscript部分木を重複走査しない', () => {
    const elementCount = 128;
    const html =
      '<noscript>'.repeat( elementCount ) +
      '<a target=_blank>x</a>' +
      '</noscript>'.repeat( elementCount );
    const expected = html.replace(
      'target=_blank',
      'target=_blank rel="noopener"'
    );
    const querySelectorAllSpy = jest.spyOn(
      document.defaultView.Element.prototype,
      'querySelectorAll'
    );

    try {
      expect( addNoopenerToBlankTargets( html ) ).toBe( expected );
      expect( querySelectorAllSpy.mock.calls.length ).toBeLessThan( 16 );
    } finally {
      querySelectorAllSpy.mockRestore();
    }
  } );

  test( '利用者の同名data属性を保持したまま候補を識別する', () => {
    const html = '<a data-cocoon-noopener-probe="user" target=_blank>x</a>';

    expect( addNoopenerToBlankTargets( html ) ).toBe(
      '<a data-cocoon-noopener-probe="user" target=_blank rel="noopener">x</a>'
    );
  } );

  test( 'SVG内のself-closingリンクを元の表記のまま保護する', () => {
    const html = '<svg><a target="_blank" /></svg>';

    expect( addNoopenerToBlankTargets( html ) ).toBe(
      '<svg><a target="_blank" rel="noopener" /></svg>'
    );
  } );

  test( 'SVG名前空間のtemplate同名要素で例外にせず実リンクを保護する', () => {
    const html = '<svg><template><a target=_blank>x</a></template></svg>';

    expect( addNoopenerToBlankTargets( html ) ).toBe(
      '<svg><template><a target=_blank rel="noopener">x</a></template></svg>'
    );
  } );

  test.each( [
    [
      'SVG内のtextarea',
      '<svg><textarea><a href=x target=_blank>x</a></textarea></svg>',
      '<svg><textarea><a href=x target=_blank rel="noopener">x</a></textarea></svg>',
    ],
    [
      'MathML内のstyle',
      '<math><style><a href=x target=_blank>x</a></style></math>',
      '<math><style><a href=x target=_blank rel="noopener">x</a></style></math>',
    ],
  ] )( '%sにある実リンクへnoopenerを追加する', ( label, html, expected ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( expected );
  } );

  test( 'JavaScript無効時に有効なnoscript内リンクへnoopenerを追加する', () => {
    const html = '<noscript><a href=x target=_blank>代替リンク</a></noscript>';

    expect( addNoopenerToBlankTargets( html ) ).toBe(
      '<noscript><a href=x target=_blank rel="noopener">代替リンク</a></noscript>'
    );
  } );

  test.each( [
    [
      'area',
      '<map><area href=x target="_blank"></map>',
      '<map><area href=x target="_blank" rel="noopener"></map>',
    ],
    [
      'form',
      '<form action=x target="_blank"><button>x</button></form>',
      '<form action=x target="_blank" rel="noopener"><button>x</button></form>',
    ],
  ] )( '%sの_blank遷移へnoopenerを追加する', ( label, html, expected ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( expected );
  } );

  test.each( [
    [
      'スラッシュと空白がある開始タグ',
      '<a/ target=_blank href=x>x</a>',
      '<a/ target=_blank rel="noopener" href=x>x</a>',
    ],
    [
      'スラッシュ直後に属性がある開始タグ',
      '<a/target=_blank href=x>x</a>',
      '<a/target=_blank rel="noopener" href=x>x</a>',
    ],
  ] )(
    '%sをブラウザーと同じリンクとして保護する',
    ( label, html, expected ) => {
      expect( addNoopenerToBlankTargets( html ) ).toBe( expected );
    }
  );

  test.each( [
    '2 < 3 と <a href=x target="_blank">x</a>',
    '比較 <<a href=x target="_blank">x</a>',
  ] )( 'タグではない小なり記号の後ろにあるリンクを保護する', ( html ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe(
      html.replace( ' target="_blank"', ' target="_blank" rel="noopener"' )
    );
  } );

  test( 'Unicode小文字化で位置が変わってもRAW TEXT後のリンクを保護する', () => {
    const html = '<script>İ</script><a href=x target="_blank">後続リンク</a>';

    expect( addNoopenerToBlankTargets( html ) ).toBe(
      '<script>İ</script><a href=x target="_blank" rel="noopener">後続リンク</a>'
    );
  } );

  test( 'RAW TEXT要素ごとにHTML全体を小文字化しない', () => {
    const html =
      '<script>const value = 1;</script>'.repeat( 128 ) +
      '<a href=x target="_blank">後続リンク</a>';
    const originalToLowerCase = String.prototype.toLowerCase;
    let wholeHtmlLowerCaseCalls = 0;

    // 長大な入力全体への小文字化だけを数え、通常の短い比較処理は通す監視。
    const lowerCaseSpy = jest
      .spyOn( String.prototype, 'toLowerCase' )
      .mockImplementation( function () {
        if ( String( this ).length === html.length ) {
          wholeHtmlLowerCaseCalls++;
        }
        return originalToLowerCase.call( this );
      } );

    try {
      expect( addNoopenerToBlankTargets( html ) ).toContain(
        '<a href=x target="_blank" rel="noopener">後続リンク</a>'
      );
      expect( wholeHtmlLowerCaseCalls ).toBe( 0 );
    } finally {
      lowerCaseSpy.mockRestore();
    }
  } );

  test.each( [
    [
      '10進数値文字参照',
      '<a target="_&#98;lank">x</a>',
      '<a target="_&#98;lank" rel="noopener">x</a>',
    ],
    [
      '16進数値文字参照',
      '<a target="&#x5f;blank">x</a>',
      '<a target="&#x5f;blank" rel="noopener">x</a>',
    ],
    [
      '名前付き文字参照',
      '<a target="&lowbar;blank">x</a>',
      '<a target="&lowbar;blank" rel="noopener">x</a>',
    ],
  ] )( '%sで表した_blankにもnoopenerを追加する', ( label, html, expected ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( expected );
  } );

  test.each( [
    [ '二重引用符', 'x"' ],
    [ '単一引用符', "x'" ],
    [ '小なり記号', 'x<y' ],
    [ '等号', 'x=y' ],
    [ 'バッククォート', 'x`y' ],
  ] )(
    '引用符なしrelに含まれる%sを壊さずnoopenerを追加する',
    ( label, relValue ) => {
      const html = `<a target=_blank rel=${ relValue }>x</a>`;

      expect( addNoopenerToBlankTargets( html ) ).toBe(
        `<a target=_blank rel=${ relValue }&#32;noopener>x</a>`
      );
    }
  );

  test.each( [
    '<a target="_blank" rel="nofollow&#32;noopener">x</a>',
    '<a target="_blank" rel="no&#111;pener">x</a>',
    '<a target="_blank" rel="no&#x6f;pener">x</a>',
    '<a target="_blank" rel="nofollow&Tab;noopener">x</a>',
    '<a target="_blank" rel="nofollow&NewLine;noopener">x</a>',
  ] )( '文字参照を含む既存noopenerを重複させない', ( html ) => {
    expect( addNoopenerToBlankTargets( html ) ).toBe( html );
  } );
} );

describe( '囲みボタンのリンク保存と旧形式往復互換性', () => {
  beforeAll( () => {
    setCategories( [ { slug: 'text', title: 'テキスト' } ] );
  } );

  beforeEach( () => {
    unregisterButtonWrap();
    registerCurrentDefinition();
  } );

  afterEach( () => {
    unregisterButtonWrap();
  } );

  test.each( [
    [ '空文字', '', '' ],
    [
      'targetなし・noopenerあり',
      '<a rel="noopener" href="https://example.com">日本語</a>',
      '<a rel="noopener" href="https://example.com">日本語</a>',
    ],
    [
      'noopenerのみ',
      '<a href="https://example.com" target="_blank" rel="noopener">日本語</a>',
      '<a href="https://example.com" target="_blank" rel="noopener">日本語</a>',
    ],
    [
      'noopenerとnoreferrer',
      '<a href="https://example.com" target="_blank" rel="noopener noreferrer">日本語</a>',
      '<a href="https://example.com" target="_blank" rel="noopener noreferrer">日本語</a>',
    ],
    [
      'noreferrerとnoopenerの逆順',
      '<a href="https://example.com" target="_blank" rel="noreferrer noopener">日本語</a>',
      '<a href="https://example.com" target="_blank" rel="noreferrer noopener">日本語</a>',
    ],
    [
      'relがtargetより前',
      '<a rel="noopener" data-id="before-target" target="_blank" href="https://example.com">日本語</a>',
      '<a rel="noopener" data-id="before-target" target="_blank" href="https://example.com">日本語</a>',
    ],
    [
      '単一引用符の既存noopener',
      "<a href='https://example.com' target='_blank' rel='noopener'>日本語</a>",
      "<a href='https://example.com' target='_blank' rel='noopener'>日本語</a>",
    ],
  ] )(
    '%sは変換不要時に入力HTMLをそのまま保持する',
    ( label, tag, expected ) => {
      const innerHTML = getWrapperInnerHTML( getCurrentSaveContent( { tag } ) );

      expect( innerHTML ).toBe( expected );
    }
  );

  test.each( [
    {
      label: '二重引用符・relなし',
      tag: '<a href="https://example.com/double" target="_blank" data-track="double">日本語</a>',
      href: 'https://example.com/double',
      expectedRel: [ 'noopener' ],
      preservedFragment: 'href="https://example.com/double" target="_blank"',
    },
    {
      label: '単一引用符・relなし',
      tag: "<a href='https://example.com/single' target='_blank' data-track='single'>日本語</a>",
      href: 'https://example.com/single',
      expectedRel: [ 'noopener' ],
      preservedFragment: "href='https://example.com/single' target='_blank'",
    },
    {
      label: '属性間空白あり',
      tag: '<a href="https://example.com/spaced"  target = "_blank"  data-track="spaced">日本語</a>',
      href: 'https://example.com/spaced',
      expectedRel: [ 'noopener' ],
      preservedFragment: 'target = "_blank"',
    },
    {
      label: '既存nofollow',
      tag: '<a href="https://example.com/nofollow" target="_blank" rel="nofollow" data-track="nofollow">日本語</a>',
      href: 'https://example.com/nofollow',
      expectedRel: [ 'nofollow', 'noopener' ],
      preservedFragment: 'rel="nofollow',
    },
    {
      label: '既存noreferrer',
      tag: '<a href="https://example.com/noreferrer" target="_blank" rel="noreferrer" data-track="noreferrer">日本語</a>',
      href: 'https://example.com/noreferrer',
      expectedRel: [ 'noreferrer', 'noopener' ],
      preservedFragment: 'rel="noreferrer',
    },
    {
      label: 'noopenerに似た別トークン',
      tag: '<a href="https://example.com/lookalike" target="_blank" rel="noopenerx" data-track="lookalike">日本語</a>',
      href: 'https://example.com/lookalike',
      expectedRel: [ 'noopenerx', 'noopener' ],
      preservedFragment: 'rel="noopenerx',
    },
  ] )(
    '$labelでも既存属性を保った単一relへnoopenerを追加する',
    ( { tag, href, expectedRel, preservedFragment } ) => {
      const innerHTML = getWrapperInnerHTML( getCurrentSaveContent( { tag } ) );
      const links = getSavedLinks( `<div>${ innerHTML }</div>` );

      expect( links ).toHaveLength( 1 );
      expect( links[ 0 ].getAttribute( 'href' ) ).toBe( href );
      expect( links[ 0 ].textContent ).toBe( '日本語' );
      expect( getRelTokens( links[ 0 ] ) ).toEqual(
        expect.arrayContaining( expectedRel )
      );
      expect(
        getRelTokens( links[ 0 ] ).filter( ( token ) => token === 'noopener' )
      ).toHaveLength( 1 );
      expect( innerHTML.match( /\srel\s*=/gi ) || [] ).toHaveLength( 1 );
      expect( innerHTML ).toContain( preservedFragment );
    }
  );

  test( '複数リンクを個別に変換し既存relとtargetなしリンクを保持する', () => {
    const tag =
      '<a href="/one" target="_blank">一</a>' +
      '<a href="/two" target="_blank" rel="nofollow">二</a>' +
      '<a href="/three" target="_self" rel="noreferrer">三</a>' +
      '<a href="/four">四</a>';
    const links = getSavedLinks( getCurrentSaveContent( { tag } ) );

    expect( links ).toHaveLength( 4 );
    expect( getRelTokens( links[ 0 ] ) ).toEqual( [ 'noopener' ] );
    expect( getRelTokens( links[ 1 ] ) ).toEqual(
      expect.arrayContaining( [ 'nofollow', 'noopener' ] )
    );
    expect( getRelTokens( links[ 2 ] ) ).toEqual( [ 'noreferrer' ] );
    expect( links[ 3 ].hasAttribute( 'rel' ) ).toBe( false );
    expect( links.map( ( link ) => link.textContent ) ).toEqual( [
      '一',
      '二',
      '三',
      '四',
    ] );
  } );

  test( '複数リンク中の既存noopenerを理由に他リンクを含むHTML全体を失わない', () => {
    const tag =
      '<a href="/one" target="_blank">一</a>' +
      '<a href="/two" target="_blank" rel="noopener noreferrer">二</a>';
    const links = getSavedLinks( getCurrentSaveContent( { tag } ) );

    expect( links ).toHaveLength( 2 );
    expect( getRelTokens( links[ 0 ] ) ).toEqual( [ 'noopener' ] );
    expect( getRelTokens( links[ 1 ] ) ).toEqual( [
      'noopener',
      'noreferrer',
    ] );
  } );

  test( 'noopenerを追加する現行saveを解析・再保存して同じHTMLを維持する', () => {
    const tag =
      '<a href="https://example.com/current" target="_blank" data-track="current">現行日本語</a>';
    const originalContent = serialize( createBlock( BLOCK_NAME, { tag } ) );
    const parsedBlock = parse( originalContent )[ 0 ];
    const serializedBlock = serialize( parsedBlock );
    const reparsedBlock = parse( serializedBlock )[ 0 ];

    acknowledgeParserInfo();
    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes.tag ).toBe( tag );
    expect( getWrapperInnerHTML( serializedBlock ) ).toContain(
      'target="_blank" rel="noopener" data-track="current"'
    );
    expect( serializedBlock ).toBe( originalContent );
    expect( reparsedBlock.isValid ).toBe( true );
    expect( reparsedBlock.attributes.tag ).toBe( tag );
  } );

  test( '属性にtagが残る現行破損fixtureを有効に移行してリンクを復元する', () => {
    const parsedBlock = parse( CURRENT_BROKEN_FIXTURE )[ 0 ];
    const recoveredContent = serialize( parsedBlock );
    const reparsedBlock = parse( recoveredContent )[ 0 ];

    acknowledgeParserInfo();
    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes.tag ).toBe( BROKEN_TAG );
    expect(
      getWrapperInnerHTML(
        getSaveContent(
          BLOCK_NAME,
          parsedBlock.attributes,
          parsedBlock.innerBlocks
        )
      )
    ).toBe( BROKEN_TAG );
    expect( reparsedBlock.isValid ).toBe( true );
    expect( reparsedBlock.attributes.tag ).toBe( BROKEN_TAG );
    expect( recoveredContent ).toContain( '>日本語</a>' );
  } );

  test( '複雑な属性を持つ現行破損形式を属性欠落なしで移行する', () => {
    const preNoopenerFix = deprecated[ 0 ];
    const tag =
      '<a href="https://example.com/complex?first=1&amp;second=2" target="_blank" rel="noopener noreferrer" data-track="complex">複雑な旧形式</a>';
    const attributes = {
      tag,
      size: 'btn-wrap-l',
      isCircle: true,
      isShine: true,
      align: 'center',
      backgroundColor: 'red',
      textColor: 'blue',
      borderColor: 'green',
      customBackgroundColor: '#112233',
      customTextColor: '#223344',
      customBorderColor: '#334455',
      fontSize: 'large',
      customFontSize: '20px',
      width: '75',
      justifyContent: 'right',
      verticalAlignment: 'center',
      anchor: 'broken-complex-anchor',
      className: 'broken-complex-class',
      lock: { move: true, remove: false },
      metadata: { name: '複雑な旧形式fixture' },
    };
    const legacyContent = createLegacyContent(
      preNoopenerFix,
      attributes,
      preNoopenerFix.apiVersion
    );
    const parsedBlock = parse( legacyContent )[ 0 ];
    const migratedContent = serialize( parsedBlock );
    const reparsedBlock = parse( migratedContent )[ 0 ];

    acknowledgeParserInfo();
    expect( getWrapperInnerHTML( legacyContent ) ).not.toContain( tag );
    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( attributes );
    expect( migratedContent ).toContain( tag );
    expect( reparsedBlock.isValid ).toBe( true );
    expect( reparsedBlock.attributes ).toMatchObject( attributes );
  } );

  test( 'tag自体が存在しない空fixtureには推測した内容を追加しない', () => {
    const parsedBlock = parse( EMPTY_FIXTURE )[ 0 ];
    const serializedBlock = serialize( parsedBlock );

    acknowledgeParserInfo();
    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes.tag ).toBe( '' );
    expect( getWrapperInnerHTML( getCurrentSaveContent() ) ).toBe( '' );
    expect( serializedBlock ).not.toContain( '<a ' );
  } );

  test( 'deprecated v2の記事は属性とリンクを保持して現行形式へ再保存・再解析できる', () => {
    const v2 = deprecated[ deprecated.length - 2 ];
    const historicalV2 = {
      ...v2,
      attributes: V2_HISTORICAL_ATTRIBUTES,
      supports: V2_HISTORICAL_SUPPORTS,
    };
    const tag =
      '<a href="https://example.com/v2?first=1&amp;second=2" target="_blank" rel="noopener noreferrer" data-track="v2">旧v2日本語</a>';
    const attributes = {
      tag,
      size: 'btn-wrap-l',
      isCircle: true,
      isShine: true,
      align: 'center',
      backgroundColor: 'red',
      textColor: 'blue',
      borderColor: 'green',
      fontSize: 'large',
      customFontSize: '20px',
      anchor: 'legacy-v2-anchor',
      className: 'legacy-v2-class',
    };
    const legacyContent = createLegacyContent( historicalV2, attributes, 2 );
    const parsedBlock = parse( legacyContent )[ 0 ];
    const migratedContent = serialize( parsedBlock );
    const reparsedBlock = parse( migratedContent )[ 0 ];

    acknowledgeParserInfo();
    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( attributes );
    expect( getWrapperInnerHTML( migratedContent ) ).toContain( tag );
    expect( reparsedBlock.isValid ).toBe( true );
    expect( reparsedBlock.attributes ).toMatchObject( attributes );
  } );

  test( 'deprecated v1の記事は旧色属性とリンクを保持して現行形式へ再保存・再解析できる', () => {
    const tag =
      '<a href="https://example.com/v1" target="_blank" rel="noopener">旧v1日本語</a>';
    const legacyAttributes = {
      tag,
      color: '#e60033',
      size: 'btn-wrap-m',
      isCircle: true,
      isShine: true,
      align: 'center',
    };
    const legacyContent = createLegacyContent( v1, legacyAttributes, null );
    const parsedBlock = parse( legacyContent )[ 0 ];
    const migratedContent = serialize( parsedBlock );
    const reparsedBlock = parse( migratedContent )[ 0 ];

    acknowledgeParserInfo();
    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( {
      tag,
      size: 'btn-wrap-m',
      isCircle: true,
      isShine: true,
      align: 'center',
      backgroundColor: 'red',
    } );
    expect( getWrapperInnerHTML( migratedContent ) ).toContain( tag );
    expect( reparsedBlock.isValid ).toBe( true );
    expect( reparsedBlock.attributes.tag ).toBe( tag );
  } );

  test( '通常記事の全属性と日本語・URL・エンティティを解析往復で保持する', () => {
    const tag =
      '<a data-track=\'roundtrip\' aria-label="詳細を見る" href="https://例.jp/path?first=1&amp;second=2" rel="nofollow noreferrer">日本語 &amp; 記号</a>';
    const attributes = {
      tag,
      size: 'btn-wrap-l',
      isCircle: true,
      isShine: true,
      align: 'center',
      backgroundColor: 'red',
      textColor: 'blue',
      borderColor: 'green',
      customBackgroundColor: '#112233',
      customTextColor: '#223344',
      customBorderColor: '#334455',
      fontSize: 'large',
      customFontSize: '20px',
      width: '75',
      justifyContent: 'right',
      verticalAlignment: 'center',
      anchor: 'button-wrap-anchor',
      className: 'fixture-user-class',
      lock: { move: true, remove: false },
      metadata: { name: '保持fixture' },
    };
    const originalContent = serialize( createBlock( BLOCK_NAME, attributes ) );
    const parsedBlock = parse( originalContent )[ 0 ];
    const serializedBlock = serialize( parsedBlock );

    acknowledgeParserInfo();
    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( attributes );
    expect( getWrapperInnerHTML( serializedBlock ) ).toContain( tag );
    expect( serializedBlock ).toBe( originalContent );
  } );
} );
