import iconBoxMetadata from './block/icon-box/block.json';
import infoBoxMetadata from './block/info-box/block.json';
import stickyBoxMetadata from './block/sticky-box/block.json';
import blogcardMetadata from './block/blogcard/block.json';
import balloonMetadata from './block/balloon/block.json';
import {
  cleanCoreStyle,
  createLegacyStyleAttributes,
  isLegacyStyle,
  migrateLegacyStyleAttribute,
  recoverLegacyStyleValue,
} from './style-attribute-compat';

describe( 'WordPress 7.0 style属性との互換性', () => {
  test.each( [
    [ 'アイコンボックス', iconBoxMetadata, 'boxStyle', 'information-box' ],
    [ '案内ボックス', infoBoxMetadata, 'boxStyle', 'primary-box' ],
    [ '付箋風ボックス', stickyBoxMetadata, 'boxStyle', '' ],
    [ 'ブログカード', blogcardMetadata, 'cardStyle', 'blogcard-type bct-none' ],
    [ '吹き出し', balloonMetadata, 'balloonStyle', 'stn' ],
  ] )(
    '%s はCocoon専用属性とWordPress標準styleを分離する',
    ( blockName, metadata, attributeName, defaultValue ) => {
      expect( metadata.attributes.style ).toEqual( { type: 'object' } );
      expect( metadata.attributes[ attributeName ] ).toEqual( {
        type: 'string',
        default: defaultValue,
      } );
      expect( metadata.supports.customCSS ).not.toBe( false );
    }
  );

  test( '正常な旧style文字列をCocoon専用属性へ移行する', () => {
    expect(
      migrateLegacyStyleAttribute(
        { content: '本文', style: 'question-box' },
        'boxStyle',
        'information-box'
      )
    ).toEqual( {
      content: '本文',
      boxStyle: 'question-box',
    } );
  } );

  test.each( [
    [ '旧文字列', { style: 'question-box' }, true ],
    [ 'WordPress 7.0の破損オブジェクト', { style: { ...'flat' } }, true ],
    [
      'CSSだけの正常なstyleオブジェクト',
      { style: { css: 'display:block;' } },
      false,
    ],
    [ 'styleなし', {}, false ],
    [ 'null', { style: null }, false ],
    [ '数値', { style: 123 }, false ],
    [ '空オブジェクト', { style: {} }, false ],
    [ '配列', { style: [] }, false ],
    [ '空の旧文字列', { style: '' }, true ],
    [ '不正値を含む破損オブジェクト', { style: { 0: 's', 1: null } }, false ],
    [ '巨大indexだけのオブジェクト', { style: { 999999999: 'x' } }, false ],
  ] )( '%s の移行対象判定が正しい', ( label, attributes, expected ) => {
    expect( isLegacyStyle( attributes ) ).toBe( expected );
  } );

  test( '旧HTMLに壊れたクラスがあるCSSだけのstyleは移行対象にする', () => {
    expect(
      isLegacyStyle( { style: { css: 'padding:1rem;' } }, [], {
        block: {
          originalContent:
            '<div class="blank-box [object Object] has-custom-css"></div>',
        },
      } )
    ).toBe( true );
  } );

  test( 'style自体が削除されてもルートHTMLに破損クラスがあれば移行対象にする', () => {
    expect(
      isLegacyStyle( {}, [], {
        block: {
          originalContent:
            '<div class="blank-box [object Object] has-custom-css"></div>',
        },
      } )
    ).toBe( true );
  } );

  test.each( [
    [
      'ダブルクォートの単独クラス',
      '<div class="[object Object] block-box"></div>',
    ],
    [
      'シングルクォートの接頭辞付きクラス',
      "<div class='speech-wrap sbs-[object Object] block-box'></div>",
    ],
  ] )( '%sをルート要素の破損として検出する', ( label, originalContent ) => {
    expect(
      isLegacyStyle( { style: { css: 'padding:1rem;' } }, [], {
        block: { originalContent },
      } )
    ).toBe( true );
  } );

  test.each( [
    [
      'data-class属性',
      '<div data-class="[object Object]" class="blank-box"></div>',
    ],
    [
      '子要素のclass属性',
      '<div class="blank-box"><span class="[object Object]"></span></div>',
    ],
    [
      '境界のない類似クラス',
      '<div class="prefix[object Object]suffix"></div>',
    ],
    [
      '任意接頭辞付きクラス',
      '<div class="prefix-[object Object] block-box"></div>',
    ],
    [
      '別属性の引用値内にあるclass文字列',
      `<div data-note=" class='[object Object]'" class="blank-box"></div>`,
    ],
  ] )( '%sをルート要素の破損と誤認しない', ( label, originalContent ) => {
    expect(
      isLegacyStyle( { style: { css: 'padding:1rem;' } }, [], {
        block: { originalContent },
      } )
    ).toBe( false );
  } );

  test( 'blockNodeだけが渡される環境でもルート要素の破損を検出する', () => {
    expect(
      isLegacyStyle( { style: { css: 'padding:1rem;' } }, [], {
        block: { originalContent: '' },
        blockNode: {
          innerHTML:
            '<div class="micro-balloon [object Object] block-box"></div>',
        },
      } )
    ).toBe( true );
  } );

  test( 'WordPress 7.0で壊れたstyleから元の値とカスタムCSSを復元する', () => {
    const brokenStyle = {
      ...'information-box',
      css: 'margin-bottom:50px;',
    };

    expect(
      migrateLegacyStyleAttribute(
        { content: '本文', style: brokenStyle },
        'boxStyle',
        'information-box'
      )
    ).toEqual( {
      content: '本文',
      boxStyle: 'information-box',
      style: { css: 'margin-bottom:50px;' },
    } );
  } );

  test( 'WordPress標準styleの非数値プロパティをすべて保持する', () => {
    const brokenStyle = {
      ...'flat',
      css: 'padding:1rem;',
      color: { text: '#333333' },
      spacing: { margin: { top: '10px' } },
    };

    expect( cleanCoreStyle( brokenStyle ) ).toEqual( {
      css: 'padding:1rem;',
      color: { text: '#333333' },
      spacing: { margin: { top: '10px' } },
    } );
    expect( recoverLegacyStyleValue( brokenStyle, 'stn' ) ).toBe( 'flat' );
  } );

  test( 'WordPress 7.1以降の未知キーと深い構造を参照ごと保持する', () => {
    const mobileStyle = {
      ':hover': { color: { text: '#123456' } },
      elements: { link: { color: { text: '#abcdef' } } },
    };
    const responsiveStyle = {
      viewport: [ { minWidth: '480px', spacing: { blockGap: '1rem' } } ],
    };
    const brokenStyle = {
      ...'flat',
      css: 'padding:1rem;',
      '@mobile': mobileStyle,
      '@tablet': null,
      ':hover': { typography: { fontWeight: '700' } },
      responsive: responsiveStyle,
      futureArray: [ 'alpha', { beta: true } ],
    };

    const cleanedStyle = cleanCoreStyle( brokenStyle );

    expect( cleanedStyle ).toEqual( {
      css: 'padding:1rem;',
      '@mobile': mobileStyle,
      '@tablet': null,
      ':hover': { typography: { fontWeight: '700' } },
      responsive: responsiveStyle,
      futureArray: [ 'alpha', { beta: true } ],
    } );
    expect( cleanedStyle[ '@mobile' ] ).toBe( mobileStyle );
    expect( cleanedStyle.responsive ).toBe( responsiveStyle );
  } );

  test( 'WordPress 7.1の正常な未知styleだけでは旧属性と判定しない', () => {
    expect(
      isLegacyStyle( {
        style: {
          css: 'display:block;',
          '@mobile': { ':hover': { color: { text: '#123456' } } },
          elements: { link: { color: { text: '#abcdef' } } },
          responsive: { viewport: [ { minWidth: '480px' } ] },
        },
      } )
    ).toBe( false );
  } );

  test( '正常なWordPress標準styleは保持しCocoon属性に初期値を設定する', () => {
    expect(
      migrateLegacyStyleAttribute(
        { style: { css: 'display:block;' } },
        'boxStyle',
        'information-box'
      )
    ).toEqual( {
      boxStyle: 'information-box',
      style: { css: 'display:block;' },
    } );
  } );

  test( '数値キーだけの破損styleは復元後にWordPress標準styleを残さない', () => {
    expect(
      migrateLegacyStyleAttribute(
        { style: { ...'st-green' } },
        'boxStyle',
        ''
      )
    ).toEqual( {
      boxStyle: 'st-green',
    } );
  } );

  test( 'styleが省略された旧記事はブロック固有の初期値へ移行する', () => {
    expect(
      migrateLegacyStyleAttribute(
        { content: '本文' },
        'cardStyle',
        'blogcard-type bct-none'
      )
    ).toEqual( {
      content: '本文',
      cardStyle: 'blogcard-type bct-none',
    } );
  } );

  test( '連番キーに欠番がある不明なstyleは初期値へ安全に戻す', () => {
    expect(
      recoverLegacyStyleValue(
        { 0: 'b', 2: 'd', css: 'display:block;' },
        'blogcard-type bct-none'
      )
    ).toBe( 'blogcard-type bct-none' );
  } );

  test( '連番キーに文字列以外が混ざるstyleは部分復元しない', () => {
    expect(
      recoverLegacyStyleValue(
        { 0: 's', 1: 't', 2: null, css: 'display:block;' },
        'stn'
      )
    ).toBe( 'stn' );
  } );

  test.each( [
    [ '複数文字', { 0: 'st', 1: 'n' } ],
    [ '空文字', { 0: '', 1: 's' } ],
  ] )( '連番キーに%sが混ざるstyleは初期値へ戻す', ( label, style ) => {
    expect( recoverLegacyStyleValue( style, 'fallback' ) ).toBe( 'fallback' );
  } );

  test.each( [
    [ 'サロゲートペア', '😀flat' ],
    [ '結合文字', `e\u0301-flat` ],
    [ '日本語', '和風-stn' ],
  ] )( '%sを含む文字列スプレッドを欠落なく復元する', ( label, value ) => {
    expect( recoverLegacyStyleValue( { ...value }, 'fallback' ) ).toBe( value );
  } );

  test( '負数や先頭ゼロ付きキーを旧文字列の連番として扱わない', () => {
    expect(
      recoverLegacyStyleValue(
        { '-1': 'x', '00': 'y', css: 'display:block;' },
        'information-box'
      )
    ).toBe( 'information-box' );
  } );

  test.each( [
    [ '巨大index', { 999999999: 'x' } ],
    [ '指数表記', { '1e0': 'x' } ],
    [ '負のゼロ', { '-0': 'x' } ],
    [ '正符号付きゼロ', { '+0': 'x' } ],
    [ '空白付きキー', { ' 0 ': 'x' } ],
    [ '空キー', { '': 'x' } ],
  ] )( '%sを連番キーと誤認しない', ( label, style ) => {
    expect( recoverLegacyStyleValue( style, 'fallback' ) ).toBe( 'fallback' );
  } );

  test( 'deprecated用属性を作っても現行属性定義を変更しない', () => {
    const currentAttributes = {
      content: { type: 'string' },
      cardStyle: { type: 'string', default: 'blogcard-type bct-none' },
    };

    const legacyAttributes = createLegacyStyleAttributes(
      currentAttributes,
      'cardStyle',
      [ 'string', 'object' ],
      'blogcard-type bct-none'
    );

    expect( currentAttributes ).toHaveProperty( 'cardStyle' );
    expect( currentAttributes ).not.toHaveProperty( 'style' );
    expect( legacyAttributes ).not.toHaveProperty( 'cardStyle' );
    expect( legacyAttributes.style ).toEqual( {
      type: [ 'string', 'object' ],
      default: 'blogcard-type bct-none',
    } );
  } );
} );
