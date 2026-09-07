const React = require( 'react' );

// SSRを利用する9ブロックの実行条件
const EDIT_BLOCKS = [
  {
    label: 'ナビカード',
    modulePath: './block/navicard/edit',
    blockName: 'cocoon-blocks/navicard',
    attributes: { id: '10' },
  },
  {
    label: 'CTA',
    modulePath: './block/cta/edit',
    blockName: 'cocoon-blocks/cta',
    attributes: { initialized: true },
  },
  {
    label: '新着情報',
    modulePath: './block/info-list/edit',
    blockName: 'cocoon-blocks/info-list',
    attributes: {},
  },
  {
    label: 'テンプレート',
    modulePath: './block/template/edit',
    blockName: 'cocoon-blocks/template',
    attributes: { id: '30' },
  },
  {
    label: 'ボックスメニュー',
    modulePath: './block/box-menu/edit',
    blockName: 'cocoon-blocks/box-menu',
    attributes: { id: '10' },
  },
  {
    label: 'プロフィール',
    modulePath: './block/profile/edit',
    blockName: 'cocoon-blocks/profile',
    attributes: { id: '20', label: '', isImageCircle: false },
  },
  {
    label: '新着記事',
    modulePath: './block/new-list/edit',
    blockName: 'cocoon-blocks/new-list',
    attributes: {},
  },
  {
    label: 'ランキング',
    modulePath: './block/ranking/edit',
    blockName: 'cocoon-blocks/ranking',
    attributes: { id: '40' },
  },
  {
    label: '人気記事',
    modulePath: './block/popular-list/edit',
    blockName: 'cocoon-blocks/popular-list',
    attributes: {},
  },
];

// 設定ID保護を検証する5ブロックの一覧仕様
const ID_GUARD_BLOCKS = [
  {
    label: 'ナビカード',
    modulePath: './block/navicard/edit',
    blockName: 'cocoon-blocks/navicard',
    globalName: 'gbNavMenus',
    matchingItem: { term_id: '42', name: '対象メニュー' },
    missingItem: { term_id: '99', name: '別メニュー' },
    attributes: { id: '42' },
  },
  {
    label: 'テンプレート',
    modulePath: './block/template/edit',
    blockName: 'cocoon-blocks/template',
    globalName: 'gbTemplates',
    matchingItem: { id: '42', title: '対象テンプレート', visible: '1' },
    missingItem: { id: '99', title: '別テンプレート', visible: '1' },
    attributes: { id: '42' },
  },
  {
    label: 'プロフィール',
    modulePath: './block/profile/edit',
    blockName: 'cocoon-blocks/profile',
    globalName: 'gbUsers',
    matchingItem: { id: '42', display_name: '対象ユーザー' },
    missingItem: { id: '99', display_name: '別ユーザー' },
    attributes: { id: '42', label: '', isImageCircle: false },
  },
  {
    label: 'ランキング',
    modulePath: './block/ranking/edit',
    blockName: 'cocoon-blocks/ranking',
    globalName: 'gbItemRankings',
    matchingItem: { id: '42', title: '対象ランキング', visible: '1' },
    missingItem: { id: '99', title: '別ランキング', visible: '1' },
    attributes: { id: '42' },
  },
  {
    label: 'ボックスメニュー',
    modulePath: './block/box-menu/edit',
    blockName: 'cocoon-blocks/box-menu',
    globalName: 'gbNavMenus',
    matchingItem: { term_id: '42', name: '対象メニュー' },
    missingItem: { term_id: '99', name: '別メニュー' },
    attributes: { id: '42' },
  },
];

// 一覧データを格納するグローバル変数名
const GLOBAL_LIST_NAMES = [
  'gbNavMenus',
  'gbUsers',
  'gbTemplates',
  'gbItemRankings',
];

// 共有SSRの利用箇所を識別する描画モック
const sharedServerSideRender = jest.fn( ( props ) =>
  React.createElement( 'div', {
    'data-mock-ssr': props.block,
  } )
);

// 旧wp-editor経路を識別する描画モック
const legacyServerSideRender = ( props ) =>
  React.createElement( 'div', {
    'data-legacy-ssr': props.block,
  } );

// 共有helperを経由しない直接importの識別用モック
const directPackageServerSideRender = ( props ) =>
  React.createElement( 'div', {
    'data-direct-ssr': props.block,
  } );

// React要素木の深さ優先探索
function findElementsByType( node, expectedType, found = [] ) {
  if ( Array.isArray( node ) ) {
    node.forEach( ( child ) =>
      findElementsByType( child, expectedType, found )
    );
    return found;
  }

  if ( ! React.isValidElement( node ) ) {
    return found;
  }

  if ( node.type === expectedType ) {
    found.push( node );
  }

  findElementsByType( node.props.children, expectedType, found );
  return found;
}

// WordPress依存用の共通モック設定
function installEditMocks() {
  jest.doMock( '@wordpress/i18n', () => ( {
    __: ( text ) => text,
  } ) );
  jest.doMock( '@wordpress/element', () => ( {
    Fragment: React.Fragment,
    useState: ( initialValue ) => [ initialValue, jest.fn() ],
  } ) );
  jest.doMock( '@wordpress/block-editor', () => ( {
    InspectorControls: 'mock-inspector-controls',
    MediaUpload: 'mock-media-upload',
    MediaUploadCheck: 'mock-media-upload-check',
    useBlockProps: () => ( { className: 'mock-block-props' } ),
  } ) );
  jest.doMock( '@wordpress/components', () => ( {
    BaseControl: 'mock-base-control',
    Button: 'mock-button',
    CheckboxControl: 'mock-checkbox-control',
    Disabled: 'mock-disabled',
    Panel: 'mock-panel',
    PanelBody: 'mock-panel-body',
    RangeControl: 'mock-range-control',
    SearchControl: 'mock-search-control',
    SelectControl: 'mock-select-control',
    TextareaControl: 'mock-textarea-control',
    TextControl: 'mock-text-control',
    ToggleControl: 'mock-toggle-control',
    __experimentalDivider: 'mock-divider',
  } ) );
  jest.doMock( '@wordpress/data', () => ( {
    useSelect: ( callback ) =>
      callback( () => ( {
        getEntityRecords: () => [],
      } ) ),
  } ) );
  jest.doMock( './helpers', () => ( {
    THEME_NAME: 'cocoon',
    CreateCategoryList: () => null,
  } ) );
  jest.doMock(
    '@wordpress/editor',
    () => ( { ServerSideRender: legacyServerSideRender } ),
    { virtual: true }
  );
  jest.doMock(
    '@wordpress/server-side-render',
    () => directPackageServerSideRender,
    { virtual: true }
  );
  jest.doMock( './server-side-render', () => ( {
    __esModule: true,
    default: sharedServerSideRender,
  } ) );
}

// 9ブロックのedit関数を分離して読み込む処理
function loadEditModules() {
  jest.resetModules();
  installEditMocks();

  return EDIT_BLOCKS.reduce( ( modules, { modulePath } ) => {
    modules[ modulePath ] = require( modulePath ).default;
    return modules;
  }, {} );
}

// 9ブロックのSSR表示に必要な一覧データ
function setAvailableGlobalLists() {
  globalThis.gbNavMenus = [ { term_id: '10', name: 'メニュー' } ];
  globalThis.gbUsers = [ { id: '20', display_name: 'ユーザー' } ];
  globalThis.gbTemplates = [
    { id: '30', title: 'テンプレート', visible: '1' },
  ];
  globalThis.gbItemRankings = [
    { id: '40', title: 'ランキング', visible: '1' },
  ];
}

// テスト間の一覧データ分離
function clearGlobalLists() {
  GLOBAL_LIST_NAMES.forEach( ( globalName ) => {
    delete globalThis[ globalName ];
  } );
}

// IDを未選択へ変更した呼び出しの判定
function hasIdResetCall( setAttributes ) {
  return setAttributes.mock.calls.some(
    ( [ nextAttributes ] ) => nextAttributes.id === '-1'
  );
}

describe( 'ServerSideRender共有helperのWordPress互換性', () => {
  test.each( [
    [ 'WordPress 6.1の直接関数', ( component ) => component ],
    [
      'WordPress 7.0/7.1の互換関数とnamed export',
      ( component ) =>
        Object.assign( component, {
          ServerSideRender: component,
          useServerSideRender: jest.fn(),
        } ),
    ],
    [
      'ES module objectのdefault export',
      ( component ) => ( {
        __esModule: true,
        default: component,
        ServerSideRender: component,
      } ),
    ],
  ] )( '%sを描画可能な関数へ解決する', ( label, createModuleShape ) => {
    const coreServerSideRender = jest.fn( ( props ) =>
      require( 'react' ).createElement( 'span', null, props.marker )
    );
    const moduleShape = createModuleShape( coreServerSideRender );
    let resolvedServerSideRender;
    let renderedElement;

    jest.resetModules();
    jest.dontMock( './server-side-render' );
    jest.doMock( '@wordpress/server-side-render', () => moduleShape, {
      virtual: true,
    } );

    // 同一React実体によるhelperの直接描画
    jest.isolateModules( () => {
      const isolatedReact = require( 'react' );
      resolvedServerSideRender = require( './server-side-render' ).default;
      renderedElement = resolvedServerSideRender( { marker: label } );
      expect( isolatedReact.isValidElement( renderedElement ) ).toBe( true );
    } );

    expect( typeof resolvedServerSideRender ).toBe( 'function' );
    expect( renderedElement.props.children ).toBe( label );
    expect( coreServerSideRender ).toHaveBeenCalled();
  } );
} );

describe( '9ブロックのServerSideRender共有化', () => {
  let editModules;

  beforeAll( () => {
    editModules = loadEditModules();
  } );

  beforeEach( () => {
    sharedServerSideRender.mockClear();
    setAvailableGlobalLists();
  } );

  afterEach( () => {
    clearGlobalLists();
  } );

  test.each( EDIT_BLOCKS )(
    '$labelが共有SSRのReact要素を生成する',
    ( { modulePath, blockName, attributes } ) => {
      const setAttributes = jest.fn();
      const editElement = editModules[ modulePath ]( {
        name: blockName,
        attributes,
        className: '',
        setAttributes,
      } );
      const serverSideRenderElements = findElementsByType(
        editElement,
        sharedServerSideRender
      );

      expect( React.isValidElement( editElement ) ).toBe( true );
      expect( serverSideRenderElements ).toHaveLength( 1 );
      expect( serverSideRenderElements[ 0 ].props ).toEqual(
        expect.objectContaining( {
          block: blockName,
          attributes,
        } )
      );

      // 共有SSR要素の実行可能性確認
      const renderedPreview = serverSideRenderElements[ 0 ].type(
        serverSideRenderElements[ 0 ].props
      );
      expect( React.isValidElement( renderedPreview ) ).toBe( true );
    }
  );
} );

describe( '設定一覧取得前のID保護', () => {
  let editModules;

  beforeAll( () => {
    editModules = loadEditModules();
  } );

  beforeEach( () => {
    clearGlobalLists();
  } );

  afterEach( () => {
    clearGlobalLists();
  } );

  test.each( ID_GUARD_BLOCKS )(
    '$labelは一覧がundefinedの間に既存IDを解除しない',
    ( { modulePath, blockName, attributes } ) => {
      const setAttributes = jest.fn();

      editModules[ modulePath ]( {
        name: blockName,
        attributes,
        className: '',
        setAttributes,
      } );

      expect( hasIdResetCall( setAttributes ) ).toBe( false );
    }
  );

  test.each( ID_GUARD_BLOCKS )(
    '$labelは一覧がnullまたは非配列の間に既存IDを解除しない',
    ( { modulePath, blockName, globalName, attributes } ) => {
      const setAttributes = jest.fn();

      [ null, {}, 'not-an-array' ].forEach( ( unavailableList ) => {
        globalThis[ globalName ] = unavailableList;
        editModules[ modulePath ]( {
          name: blockName,
          attributes,
          className: '',
          setAttributes,
        } );
      } );

      expect( hasIdResetCall( setAttributes ) ).toBe( false );
    }
  );

  test.each( ID_GUARD_BLOCKS )(
    '$labelは取得済みの空一覧で存在しないIDを解除する',
    ( { modulePath, blockName, globalName, attributes } ) => {
      const setAttributes = jest.fn();
      globalThis[ globalName ] = [];

      editModules[ modulePath ]( {
        name: blockName,
        attributes,
        className: '',
        setAttributes,
      } );

      expect( hasIdResetCall( setAttributes ) ).toBe( true );
    }
  );

  test.each( ID_GUARD_BLOCKS )(
    '$labelは未選択IDを空一覧で重複更新しない',
    ( { modulePath, blockName, globalName, attributes } ) => {
      const setAttributes = jest.fn();
      globalThis[ globalName ] = [];

      editModules[ modulePath ]( {
        name: blockName,
        attributes: { ...attributes, id: '-1' },
        className: '',
        setAttributes,
      } );

      expect( hasIdResetCall( setAttributes ) ).toBe( false );
    }
  );

  test.each( ID_GUARD_BLOCKS )(
    '$labelは取得済み一覧から削除されたIDを解除する',
    ( { modulePath, blockName, globalName, missingItem, attributes } ) => {
      const setAttributes = jest.fn();
      globalThis[ globalName ] = [ missingItem ];

      editModules[ modulePath ]( {
        name: blockName,
        attributes,
        className: '',
        setAttributes,
      } );

      expect( hasIdResetCall( setAttributes ) ).toBe( true );
    }
  );

  test.each( ID_GUARD_BLOCKS )(
    '$labelは取得済み一覧に存在するIDを保持する',
    ( { modulePath, blockName, globalName, matchingItem, attributes } ) => {
      const setAttributes = jest.fn();
      globalThis[ globalName ] = [ matchingItem ];

      editModules[ modulePath ]( {
        name: blockName,
        attributes,
        className: '',
        setAttributes,
      } );

      expect( hasIdResetCall( setAttributes ) ).toBe( false );
    }
  );
} );
