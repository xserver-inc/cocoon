import {
  createBlock,
  getBlockType,
  parse,
  registerBlockType,
  serialize,
  unregisterBlockType,
} from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

// deprecated定義が参照するWordPressグローバルをテスト環境へ用意します。
global.wp = {
  blocks: { createBlock },
  components: {},
};

const blockCases = [
  {
    label: 'アイコンボックス',
    metadata: require( './block/icon-box/block.json' ),
    save: require( './block/icon-box/save' ).default,
    deprecated: require( './block/icon-box/deprecated' ).default,
    attributeName: 'boxStyle',
    defaultValue: 'information-box',
    conflictingCustomClass: 'information-box',
    legacyValue: 'question-box',
  },
  {
    label: '案内ボックス',
    metadata: require( './block/info-box/block.json' ),
    save: require( './block/info-box/save' ).default,
    deprecated: require( './block/info-box/deprecated' ).default,
    attributeName: 'boxStyle',
    defaultValue: 'primary-box',
    conflictingCustomClass: 'primary-box',
    legacyValue: 'success-box',
  },
  {
    label: '付箋風ボックス',
    metadata: require( './block/sticky-box/block.json' ),
    save: require( './block/sticky-box/save' ).default,
    deprecated: require( './block/sticky-box/deprecated' ).default,
    attributeName: 'boxStyle',
    defaultValue: '',
    conflictingCustomClass: 'st-red',
    legacyValue: 'st-yellow',
    legacyStyleIndex: 1,
    historicalIndex: 2,
  },
  {
    label: 'ブログカード',
    metadata: require( './block/blogcard/block.json' ),
    save: require( './block/blogcard/save' ).default,
    deprecated: require( './block/blogcard/deprecated' ).default,
    attributeName: 'cardStyle',
    defaultValue: 'blogcard-type bct-none',
    conflictingCustomClass: 'bct-none',
    legacyValue: 'blogcard-type bct-related',
  },
  {
    label: '吹き出し',
    metadata: require( './block/balloon/block.json' ),
    save: require( './block/balloon/save' ).default,
    deprecated: require( './block/balloon/deprecated' ).default,
    attributeName: 'balloonStyle',
    defaultValue: 'stn',
    conflictingCustomClass: 'sbs-stn',
    legacyValue: 'flat',
  },
];

const CHILD_BLOCK_NAME = 'test/style-attribute-compat-child';
const CHILD_CONTAINER_BLOCK_NAME =
  'test/style-attribute-compat-child-container';

// WordPress 7.0で保存されたコメント属性とCSS識別クラスを、旧WordPressで読む固定fixtureです。
const WORDPRESS_7_CUSTOM_CSS_FIXTURE = `<!-- wp:cocoon-blocks/icon-box {"boxStyle":"question-box","style":{"css":"&:hover{outline:2px solid red;}@media (max-width:600px){&{margin:0;}}"},"className":"wp7-user-class"} -->
<div class="wp-block-cocoon-blocks-icon-box common-icon-box block-box question-box wp7-user-class has-custom-css"></div>
<!-- /wp:cocoon-blocks/icon-box -->`;

const WORDPRESS_7_CUSTOM_CSS =
  '&:hover{outline:2px solid red;}@media (max-width:600px){&{margin:0;}}';

// 権限変更でCSSだけ除去された後も、7.1以降の状態別styleと入れ子を保持する固定fixtureです。
const WORDPRESS_71_PERMISSION_STRIPPED_FIXTURE = `<!-- wp:cocoon-blocks/icon-box {"boxStyle":"question-box","style":{"@mobile":{"spacing":{"padding":{"top":"1rem"}}},":hover":{"color":{"text":"#123456"}}}} -->
<div class="wp-block-cocoon-blocks-icon-box common-icon-box block-box question-box has-custom-css">
<!-- wp:test/style-attribute-compat-child-container -->
<div class="wp-block-test-style-attribute-compat-child-container compat-child-container">
<!-- wp:test/style-attribute-compat-child -->
<p class="compat-child">入れ子の本文</p>
<!-- /wp:test/style-attribute-compat-child -->
</div>
<!-- /wp:test/style-attribute-compat-child-container -->
</div>
<!-- /wp:cocoon-blocks/icon-box -->`;

// 空の旧styleから追加CSSだけが除去され、HTMLに破損クラスだけ残った付箋を固定します。
const WORDPRESS_7_ORPHANED_STICKY_FIXTURE = `<!-- wp:cocoon-blocks/sticky-box {"anchor":"orphaned-sticky","className":"sticky-user-class"} -->
<div id="orphaned-sticky" class="wp-block-cocoon-blocks-sticky-box blank-box block-box sticky [object Object] sticky-user-class has-custom-css"></div>
<!-- /wp:cocoon-blocks/sticky-box -->`;

// InnerBlocksを持つ旧記事でも、子ブロック本文が移行後に残ることを確認するためのテスト用ブロックです。
const registerChildBlock = () => {
  registerBlockType( CHILD_BLOCK_NAME, {
    apiVersion: 3,
    title: CHILD_BLOCK_NAME,
    category: 'text',
    edit: () => null,
    save: () => <p className="compat-child">入れ子の本文</p>,
  } );

  registerBlockType( CHILD_CONTAINER_BLOCK_NAME, {
    apiVersion: 3,
    title: CHILD_CONTAINER_BLOCK_NAME,
    category: 'text',
    edit: () => null,
    save: () => {
      // API v3の固定fixtureと同じ標準ブロッククラスを保存HTMLへ付与します。
      const blockProps = useBlockProps.save( {
        className: 'compat-child-container',
      } );

      return (
        <div { ...blockProps }>
          <InnerBlocks.Content />
        </div>
      );
    },
  } );
};

// 同名ブロックを旧定義と現行定義で順に登録できるよう、毎回登録状態を初期化します。
const unregisterIfNeeded = ( blockName ) => {
  if ( getBlockType( blockName ) ) {
    unregisterBlockType( blockName );
  }
};

// 付箋だけは権限変更後の孤立破損形式を先頭に持つため、通常の旧定義位置を吸収します。
const getLegacyStyleDefinition = ( blockCase ) =>
  blockCase.deprecated[ blockCase.legacyStyleIndex ?? 0 ];

const getHistoricalDefinition = ( blockCase ) =>
  blockCase.deprecated[ blockCase.historicalIndex ?? 1 ];

const registerDefinition = ( metadata, definition, deprecated = [] ) => {
  registerBlockType( metadata.name, {
    apiVersion: definition.apiVersion,
    title: metadata.name,
    category: 'text',
    attributes: definition.attributes,
    supports: definition.supports,
    edit: () => null,
    save: definition.save,
    deprecated,
  } );
};

// WordPressの版により移行成功時のinfo出力有無が異なるため、発生した通知を明示確認します。
const acknowledgeParserInfo = () => {
  // テスト用consoleモックの呼び出し件数だけを確認します。
  // eslint-disable-next-line no-console
  if ( console.info.mock.calls.length > 0 ) {
    expect( console ).toHaveInformed();
  } else {
    expect( console ).not.toHaveInformed();
  }
};

// 旧定義でfixtureを生成してから現行定義で解析し、Gutenbergの実移行経路を通します。
const parseWithCurrentDefinition = (
  blockCase,
  legacyDefinition,
  attributes,
  className = 'user-defined-class',
  innerBlocks = [],
  includeAnchor = true
) => {
  const { metadata, save, deprecated } = blockCase;
  unregisterIfNeeded( metadata.name );
  registerDefinition( metadata, legacyDefinition );

  const legacyContent = serialize(
    createBlock(
      metadata.name,
      {
        ...attributes,
        ...( includeAnchor ? { anchor: 'legacy-anchor' } : {} ),
        ...( className ? { className } : {} ),
      },
      innerBlocks
    )
  );

  unregisterBlockType( metadata.name );
  registerDefinition(
    metadata,
    {
      apiVersion: metadata.apiVersion,
      attributes: metadata.attributes,
      supports: metadata.supports,
      save,
    },
    deprecated
  );

  const parsedBlock = parse( legacyContent )[ 0 ];

  return { legacyContent, parsedBlock };
};

describe( '旧style属性を含む記事のGutenberg往復互換性', () => {
  beforeAll( () => {
    registerChildBlock();
  } );

  afterAll( () => {
    unregisterIfNeeded( CHILD_BLOCK_NAME );
    unregisterIfNeeded( CHILD_CONTAINER_BLOCK_NAME );
    blockCases.forEach( ( blockCase ) => {
      unregisterIfNeeded( blockCase.metadata.name );
    } );
  } );

  test.each( blockCases )(
    '$label の本番metadataは旧WordPressでもCSSを保持できるstyle属性を明示する',
    ( { metadata } ) => {
      expect( metadata.attributes.style ).toEqual( { type: 'object' } );
    }
  );

  test( 'WordPress 7.0の固定CSS fixtureを旧WordPressで再保存してもCSSを失わない', () => {
    const iconBox = blockCases[ 0 ];
    const { metadata, save, deprecated } = iconBox;
    unregisterIfNeeded( metadata.name );
    registerDefinition(
      metadata,
      {
        apiVersion: metadata.apiVersion,
        attributes: metadata.attributes,
        supports: metadata.supports,
        save,
      },
      deprecated
    );

    const parsedBlock = parse( WORDPRESS_7_CUSTOM_CSS_FIXTURE )[ 0 ];
    const downgradedContent = serialize( parsedBlock );

    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes.style ).toEqual( {
      css: WORDPRESS_7_CUSTOM_CSS,
    } );
    expect( parsedBlock.attributes.className.split( /\s+/ ) ).toEqual(
      expect.arrayContaining( [ 'wp7-user-class', 'has-custom-css' ] )
    );
    const reparsedBlock = parse( downgradedContent )[ 0 ];
    expect( reparsedBlock.attributes.style.css ).toBe( WORDPRESS_7_CUSTOM_CSS );
    expect( downgradedContent ).toContain( 'has-custom-css' );
    acknowledgeParserInfo();
  } );

  test( '権限変更後のWordPress 7.1固定fixtureも状態別styleと入れ子を保持する', () => {
    const iconBox = blockCases[ 0 ];
    const { metadata, save, deprecated } = iconBox;
    unregisterIfNeeded( metadata.name );
    registerDefinition(
      metadata,
      {
        apiVersion: metadata.apiVersion,
        attributes: metadata.attributes,
        supports: metadata.supports,
        save,
      },
      deprecated
    );

    const parsedBlock = parse( WORDPRESS_71_PERMISSION_STRIPPED_FIXTURE )[ 0 ];
    const downgradedContent = serialize( parsedBlock );

    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( {
      boxStyle: 'question-box',
      className: 'has-custom-css',
      style: {
        '@mobile': { spacing: { padding: { top: '1rem' } } },
        ':hover': { color: { text: '#123456' } },
      },
    } );
    expect( parsedBlock.attributes.style ).not.toHaveProperty( 'css' );
    expect( parsedBlock.innerBlocks ).toHaveLength( 1 );
    expect( parsedBlock.innerBlocks[ 0 ] ).toMatchObject( {
      name: CHILD_CONTAINER_BLOCK_NAME,
      isValid: true,
    } );
    expect( parsedBlock.innerBlocks[ 0 ].innerBlocks ).toHaveLength( 1 );
    expect( parsedBlock.innerBlocks[ 0 ].innerBlocks[ 0 ] ).toMatchObject( {
      name: CHILD_BLOCK_NAME,
      isValid: true,
    } );
    expect( downgradedContent ).toContain( '入れ子の本文' );
    expect( downgradedContent ).toContain( 'has-custom-css' );
    expect( downgradedContent ).toContain( '"@mobile"' );
    expect( downgradedContent ).toContain( '":hover"' );
    acknowledgeParserInfo();
  } );

  test( '付箋はstyle自体が消えた権限変更後の孤立破損HTMLも復旧する', () => {
    const stickyBox = blockCases.find(
      ( blockCase ) => blockCase.metadata.name === 'cocoon-blocks/sticky-box'
    );
    const { metadata, save, deprecated } = stickyBox;
    unregisterIfNeeded( metadata.name );
    registerDefinition(
      metadata,
      {
        apiVersion: metadata.apiVersion,
        attributes: metadata.attributes,
        supports: metadata.supports,
        save,
      },
      deprecated
    );

    const parsedBlock = parse( WORDPRESS_7_ORPHANED_STICKY_FIXTURE )[ 0 ];
    const serializedBlock = serialize( parsedBlock );

    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( {
      anchor: 'orphaned-sticky',
      boxStyle: '',
    } );
    expect( parsedBlock.attributes.className.split( /\s+/ ) ).toEqual(
      expect.arrayContaining( [ 'sticky-user-class', 'has-custom-css' ] )
    );
    expect( parsedBlock.attributes.className ).not.toContain(
      '[object Object]'
    );
    expect( parsedBlock.attributes ).not.toHaveProperty( 'style' );
    expect( serializedBlock ).not.toContain( '[object Object]' );
    expect( serializedBlock ).toContain( 'has-custom-css' );
    acknowledgeParserInfo();
  } );

  test.each( blockCases )(
    '$label はアンカー付き旧記事を有効な現行ブロックへ移行する',
    ( blockCase ) => {
      const { legacyContent, parsedBlock } = parseWithCurrentDefinition(
        blockCase,
        getLegacyStyleDefinition( blockCase ),
        {
          style: blockCase.legacyValue,
          lock: { move: true, remove: false },
          metadata: { name: '互換性テスト' },
        }
      );

      expect( legacyContent ).toContain( 'id="legacy-anchor"' );
      expect( legacyContent ).toContain( 'user-defined-class' );
      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.attributes ).toMatchObject( {
        anchor: 'legacy-anchor',
        className: 'user-defined-class',
        lock: { move: true, remove: false },
        metadata: { name: '互換性テスト' },
        [ blockCase.attributeName ]: blockCase.legacyValue,
      } );
      expect( parsedBlock.attributes ).not.toHaveProperty( 'style' );
      acknowledgeParserInfo();
    }
  );

  test.each(
    blockCases.filter(
      ( blockCase ) => blockCase.metadata.name !== 'cocoon-blocks/blogcard'
    )
  )(
    '$label は入れ子ブロックの本文を失わず現行形式へ移行する',
    ( blockCase ) => {
      const { legacyContent, parsedBlock } = parseWithCurrentDefinition(
        blockCase,
        getLegacyStyleDefinition( blockCase ),
        { style: blockCase.legacyValue },
        'user-defined-class',
        [ createBlock( CHILD_BLOCK_NAME ) ]
      );

      expect( legacyContent ).toContain( '入れ子の本文' );
      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.innerBlocks ).toHaveLength( 1 );
      expect( parsedBlock.innerBlocks[ 0 ] ).toMatchObject( {
        name: CHILD_BLOCK_NAME,
        isValid: true,
      } );
      expect( serialize( parsedBlock ) ).toContain( '入れ子の本文' );
      acknowledgeParserInfo();
    }
  );

  test( 'ブログカードはURL本文とHTMLエンティティを失わず移行する', () => {
    const blogcard = blockCases.find(
      ( blockCase ) => blockCase.metadata.name === 'cocoon-blocks/blogcard'
    );
    const content = 'https://example.com/path?first=1&amp;second=2';
    const { parsedBlock } = parseWithCurrentDefinition(
      blogcard,
      blogcard.deprecated[ 0 ],
      { style: blogcard.legacyValue, content }
    );

    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( {
      cardStyle: blogcard.legacyValue,
    } );
    expect( parsedBlock.attributes.content.trim() ).toBe( content );
    expect( serialize( parsedBlock ) ).toContain( content );
    acknowledgeParserInfo();
  } );

  test( '吹き出しは人物・位置・色・文字サイズの設定をすべて保持する', () => {
    const balloon = blockCases.find(
      ( blockCase ) =>
        blockCase.metadata.name === 'cocoon-blocks/balloon-ex-box-1'
    );
    const balloonAttributes = {
      style: 'think',
      name: '互換性テスト',
      id: 'sample-id',
      icon: 'https://example.com/icon.png',
      position: 'r',
      iconstyle: 'maru',
      customBackgroundColor: '#abcdef',
      customTextColor: '#123456',
      customBorderColor: '#654321',
      fontSize: 'large',
      notNestedStyle: false,
    };
    const { parsedBlock } = parseWithCurrentDefinition(
      balloon,
      balloon.deprecated[ 0 ],
      balloonAttributes
    );
    const { style, ...expectedAttributes } = balloonAttributes;

    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( {
      ...expectedAttributes,
      balloonStyle: style,
    } );
    expect( parsedBlock.attributes ).not.toHaveProperty( 'style' );
    acknowledgeParserInfo();
  } );

  test.each( blockCases.slice( 0, 4 ) )(
    '$label はさらに古いdeprecated記事も本文を失わず現行形式へ移行する',
    ( blockCase ) => {
      const usesInnerBlocks =
        blockCase.metadata.name !== 'cocoon-blocks/blogcard';
      const attributes = {
        style: blockCase.legacyValue,
        ...( usesInnerBlocks
          ? {}
          : { content: 'https://example.com/old-entry' } ),
      };
      const { parsedBlock } = parseWithCurrentDefinition(
        blockCase,
        getHistoricalDefinition( blockCase ),
        attributes,
        '',
        usesInnerBlocks ? [ createBlock( CHILD_BLOCK_NAME ) ] : [],
        false
      );

      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.attributes[ blockCase.attributeName ] ).toBe(
        blockCase.legacyValue
      );
      expect( parsedBlock.attributes ).not.toHaveProperty( 'style' );
      expect( parsedBlock.innerBlocks ).toHaveLength( usesInnerBlocks ? 1 : 0 );
      const preservedContent = usesInnerBlocks
        ? serialize( parsedBlock )
        : parsedBlock.attributes.content;
      expect( preservedContent ).toContain(
        usesInnerBlocks ? '入れ子の本文' : 'https://example.com/old-entry'
      );
      acknowledgeParserInfo();
    }
  );

  test.each( [
    [
      'v3',
      1,
      { style: 'think', name: '旧v3', position: 'r' },
      false,
      true,
      'speech-wrap sbs-think sbp-r sbis-cb cf block-box',
    ],
    [
      'v2',
      2,
      { style: 'flat', name: '旧v2', position: 'l' },
      false,
      true,
      'speech-wrap sbs-flat sbp-l sbis-cb cf block-box',
    ],
    [
      'v1',
      3,
      { borderColor: '#ddd' },
      true,
      false,
      'blank-box bb-light-grey block-box',
    ],
  ] )(
    '吹き出しの旧%s記事を現行形式へ移行する',
    (
      version,
      deprecatedIndex,
      attributes,
      isFirstVersion,
      supportsAnchor,
      expectedLegacyClass
    ) => {
      const balloon = blockCases.find(
        ( blockCase ) =>
          blockCase.metadata.name === 'cocoon-blocks/balloon-ex-box-1'
      );
      const userClass = supportsAnchor ? `legacy-${ version }-user-class` : '';
      const { legacyContent, parsedBlock } = parseWithCurrentDefinition(
        balloon,
        balloon.deprecated[ deprecatedIndex ],
        attributes,
        userClass,
        [ createBlock( CHILD_BLOCK_NAME ) ],
        supportsAnchor
      );

      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.attributes.balloonStyle ).toBe(
        isFirstVersion ? balloon.defaultValue : attributes.style
      );
      expect( parsedBlock.attributes ).not.toHaveProperty( 'style' );
      expect( parsedBlock.innerBlocks ).toHaveLength( 1 );
      expect( serialize( parsedBlock ) ).toContain( '入れ子の本文' );
      expect( legacyContent ).toContain( expectedLegacyClass );
      const expectedDeprecatedMetadata = supportsAnchor
        ? { apiVersion: 2, supports: { anchor: true } }
        : {};
      expect( balloon.deprecated[ deprecatedIndex ] ).toMatchObject(
        expectedDeprecatedMetadata
      );
      expect( legacyContent.includes( 'id="legacy-anchor"' ) ).toBe(
        supportsAnchor
      );
      expect(
        Boolean( userClass ) && legacyContent.includes( userClass )
      ).toBe( supportsAnchor );
      expect( parsedBlock.attributes.anchor ).toBe(
        supportsAnchor ? 'legacy-anchor' : undefined
      );
      expect( parsedBlock.attributes.className ).toBe(
        supportsAnchor ? userClass : undefined
      );
      acknowledgeParserInfo();
    }
  );

  test.each( blockCases )(
    '$label はユーザークラスが別スタイル名と一致しても属性を混同しない',
    ( blockCase ) => {
      const { parsedBlock } = parseWithCurrentDefinition(
        blockCase,
        getLegacyStyleDefinition( blockCase ),
        { style: blockCase.legacyValue },
        blockCase.conflictingCustomClass
      );

      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.attributes ).toMatchObject( {
        className: blockCase.conflictingCustomClass,
        [ blockCase.attributeName ]: blockCase.legacyValue,
      } );
      acknowledgeParserInfo();
    }
  );

  test.each( blockCases )(
    '$label はユーザークラスのない旧記事にも不要なclassNameを追加しない',
    ( blockCase ) => {
      const { parsedBlock } = parseWithCurrentDefinition(
        blockCase,
        getLegacyStyleDefinition( blockCase ),
        { style: blockCase.legacyValue },
        ''
      );

      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.attributes[ blockCase.attributeName ] ).toBe(
        blockCase.legacyValue
      );
      expect( parsedBlock.attributes.className ).toBeUndefined();
      acknowledgeParserInfo();
    }
  );

  test.each( blockCases )(
    '$label は旧styleが省略された初期値の記事をそのまま有効に読み込む',
    ( blockCase ) => {
      const { parsedBlock } = parseWithCurrentDefinition(
        blockCase,
        getLegacyStyleDefinition( blockCase ),
        {}
      );

      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.attributes ).toMatchObject( {
        [ blockCase.attributeName ]: blockCase.defaultValue,
        anchor: 'legacy-anchor',
        className: 'user-defined-class',
      } );
      expect( parsedBlock.attributes ).not.toHaveProperty( 'style' );
      acknowledgeParserInfo();
    }
  );

  test.each( blockCases )(
    '$label はアンカーとCSSを含む破損記事を有効な現行ブロックへ復元する',
    ( blockCase ) => {
      const brokenStyle = {
        ...blockCase.legacyValue,
        css: 'margin-bottom:50px;',
      };
      const { legacyContent, parsedBlock } = parseWithCurrentDefinition(
        blockCase,
        getLegacyStyleDefinition( blockCase ),
        { style: brokenStyle }
      );

      expect( legacyContent ).toContain( 'legacy-anchor' );
      expect( legacyContent ).toContain( 'margin-bottom:50px;' );
      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.attributes ).toMatchObject( {
        anchor: 'legacy-anchor',
        className: 'user-defined-class',
        [ blockCase.attributeName ]: blockCase.legacyValue,
        style: { css: 'margin-bottom:50px;' },
      } );
      expect( serialize( parsedBlock ) ).not.toContain( 'object Object' );
      acknowledgeParserInfo();
    }
  );

  test( '付箋風ボックスは連番キーのない破損styleからCSSを失わず復元する', () => {
    const stickyBox = blockCases.find(
      ( blockCase ) => blockCase.metadata.name === 'cocoon-blocks/sticky-box'
    );
    const { legacyContent, parsedBlock } = parseWithCurrentDefinition(
      stickyBox,
      stickyBox.deprecated[ 0 ],
      { style: { css: 'padding:1rem;' } }
    );

    expect( legacyContent ).toContain( 'object Object' );
    expect( parsedBlock.isValid ).toBe( true );
    expect( parsedBlock.attributes ).toMatchObject( {
      anchor: 'legacy-anchor',
      boxStyle: '',
      style: { css: 'padding:1rem;' },
    } );
    expect( parsedBlock.attributes.className.split( /\s+/ ) ).toEqual(
      expect.arrayContaining( [ 'user-defined-class', 'has-custom-css' ] )
    );
    acknowledgeParserInfo();
  } );

  test.each( blockCases )(
    '$label は修正後の正常なstyleオブジェクトを旧形式へ誤移行しない',
    ( blockCase ) => {
      const { metadata, save, deprecated } = blockCase;
      unregisterIfNeeded( metadata.name );
      registerDefinition(
        metadata,
        {
          apiVersion: metadata.apiVersion,
          attributes: metadata.attributes,
          supports: metadata.supports,
          save,
        },
        deprecated
      );

      const currentContent = serialize(
        createBlock( metadata.name, {
          [ blockCase.attributeName ]: blockCase.legacyValue,
          style: { css: 'display:block;' },
          anchor: 'current-anchor',
          className: 'current-user-class',
        } )
      );
      const parsedBlock = parse( currentContent )[ 0 ];
      unregisterBlockType( metadata.name );

      expect( parsedBlock.isValid ).toBe( true );
      expect( parsedBlock.attributes ).toMatchObject( {
        [ blockCase.attributeName ]: blockCase.legacyValue,
        style: { css: 'display:block;' },
        anchor: 'current-anchor',
        className: 'current-user-class',
      } );
      expect( console ).not.toHaveInformed();
    }
  );
} );
