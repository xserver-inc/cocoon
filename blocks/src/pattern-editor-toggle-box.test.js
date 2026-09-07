import {
  getBlockType,
  registerBlockType,
  unregisterBlockType as unregisterRegisteredBlockType,
} from '@wordpress/blocks';
import {
  select as selectData,
  subscribe as subscribeData,
} from '@wordpress/data';
import { observePatternEditorToggleBox } from './pattern-editor-toggle-box';

const TOGGLE_BOX_BLOCK_NAME = 'cocoon-blocks/toggle-box-1';
const REGISTRY_TEST_BLOCK_NAME = 'test/pattern-editor-without-post-store';

// 投稿ストアの初期化順と購読再入を制御する可変fixture。
const createObserverHarness = ( {
  initialStore,
  notifyDuringSubscribe = false,
  reenterDuringUnregister = false,
} = {} ) => {
  let editorStore = initialStore;
  let listener;
  let domReadyCallback;
  const events = [];

  const unsubscribe = jest.fn( () => {
    events.push( 'unsubscribe' );
  } );
  const select = jest.fn( ( storeName ) => {
    if ( storeName === 'core/editor' ) {
      return editorStore;
    }

    return undefined;
  } );
  // 購読開始中の同期通知を再現する購読fixture。
  const subscribe = jest.fn( ( nextListener ) => {
    listener = nextListener;
    if ( notifyDuringSubscribe ) {
      listener();
    }

    return unsubscribe;
  } );
  // ブロック登録解除中の同期再入を再現する解除fixture。
  const unregisterBlockType = jest.fn( () => {
    events.push( 'unregister' );
    if ( reenterDuringUnregister ) {
      listener();
    }
  } );
  const domReady = jest.fn( ( callback ) => {
    domReadyCallback = callback;
  } );

  return {
    domReady,
    events,
    select,
    subscribe,
    unregisterBlockType,
    unsubscribe,
    start: () =>
      observePatternEditorToggleBox( {
        domReady,
        select,
        subscribe,
        unregisterBlockType,
      } ),
    setEditorStore: ( nextStore ) => {
      editorStore = nextStore;
    },
    notify: () => listener(),
    notifyDomReady: () => domReadyCallback(),
  };
};

// 投稿タイプselectorを持つ最小ストアfixture。
const createEditorStore = ( postType ) => ( {
  getCurrentPostType: jest.fn( () => postType ),
} );

describe( 'パターンエディターのアコーディオン登録制御', () => {
  test.each( [
    [ 'core/editorなし', undefined ],
    [ 'nullストア', null ],
    [ '空のストア', {} ],
    [ 'メソッドなし', { anotherSelector: jest.fn() } ],
    [ 'undefinedメソッド', { getCurrentPostType: undefined } ],
    [ 'nullメソッド', { getCurrentPostType: null } ],
    [ '非関数メソッド', { getCurrentPostType: 'wp_block' } ],
  ] )( '%sでも購読開始と即時確認を安全に継続する', ( label, store ) => {
    const harness = createObserverHarness( { initialStore: store } );
    let stop;

    expect( () => {
      stop = harness.start();
    } ).not.toThrow();
    expect( stop ).toEqual( expect.any( Function ) );
    expect( harness.subscribe ).toHaveBeenCalledWith(
      expect.any( Function ),
      'core/editor'
    );
    expect( harness.domReady ).toHaveBeenCalledWith( expect.any( Function ) );
    expect( harness.unregisterBlockType ).not.toHaveBeenCalled();
    expect( harness.unsubscribe ).not.toHaveBeenCalled();
  } );

  test.each( [
    [ 'null', null ],
    [ 'undefined', undefined ],
    [ '空文字', '' ],
  ] )( '投稿タイプが%sの初期化途中では購読を維持する', ( label, postType ) => {
    const harness = createObserverHarness( {
      initialStore: createEditorStore( postType ),
    } );

    harness.start();

    expect( harness.unregisterBlockType ).not.toHaveBeenCalled();
    expect( harness.unsubscribe ).not.toHaveBeenCalled();
  } );

  test( '後から登録されたwp_blockストアを次の通知で処理する', () => {
    const harness = createObserverHarness();

    harness.start();
    harness.setEditorStore( createEditorStore( 'wp_block' ) );
    harness.notify();

    expect( harness.events ).toEqual( [ 'unsubscribe', 'unregister' ] );
    expect( harness.unregisterBlockType ).toHaveBeenCalledTimes( 1 );
    expect( harness.unregisterBlockType ).toHaveBeenCalledWith(
      TOGGLE_BOX_BLOCK_NAME
    );
  } );

  test.each( [ 'post', 'page' ] )(
    '%sではブロックを残して確定済み購読だけを解除する',
    ( postType ) => {
      const harness = createObserverHarness( {
        initialStore: createEditorStore( postType ),
      } );

      harness.start();
      harness.notify();

      expect( harness.events ).toEqual( [ 'unsubscribe' ] );
      expect( harness.unregisterBlockType ).not.toHaveBeenCalled();
      expect( harness.unsubscribe ).toHaveBeenCalledTimes( 1 );
    }
  );

  test( 'wp_blockでは購読解除後に対象ブロックを1回だけ解除する', () => {
    const harness = createObserverHarness( {
      initialStore: createEditorStore( 'wp_block' ),
    } );

    harness.start();
    harness.notify();

    expect( harness.events ).toEqual( [ 'unsubscribe', 'unregister' ] );
    expect( harness.unregisterBlockType ).toHaveBeenCalledTimes( 1 );
    expect( harness.unregisterBlockType ).toHaveBeenCalledWith(
      TOGGLE_BOX_BLOCK_NAME
    );
    expect( harness.unsubscribe ).toHaveBeenCalledTimes( 1 );
  } );

  test( '登録解除が購読を再入させても二重実行しない', () => {
    const harness = createObserverHarness( {
      initialStore: createEditorStore( 'wp_block' ),
      reenterDuringUnregister: true,
    } );

    harness.start();

    expect( harness.events ).toEqual( [ 'unsubscribe', 'unregister' ] );
    expect( harness.unregisterBlockType ).toHaveBeenCalledTimes( 1 );
    expect( harness.unsubscribe ).toHaveBeenCalledTimes( 1 );
  } );

  test( 'subscribe内の同期通知でも解除関数確定後に1回だけ処理する', () => {
    const harness = createObserverHarness( {
      initialStore: createEditorStore( 'wp_block' ),
      notifyDuringSubscribe: true,
    } );

    expect( () => harness.start() ).not.toThrow();
    expect( harness.events ).toEqual( [ 'unsubscribe', 'unregister' ] );
    expect( harness.unregisterBlockType ).toHaveBeenCalledTimes( 1 );
    expect( harness.unsubscribe ).toHaveBeenCalledTimes( 1 );
  } );

  test( 'データ通知がない後登録もdomReady再確認で処理する', () => {
    const harness = createObserverHarness();

    harness.start();
    harness.setEditorStore( createEditorStore( 'wp_block' ) );
    harness.notifyDomReady();

    expect( harness.events ).toEqual( [ 'unsubscribe', 'unregister' ] );
    expect( harness.unregisterBlockType ).toHaveBeenCalledTimes( 1 );
  } );

  test( 'domReady時にストアがなくても後続通知用の購読を維持する', () => {
    const harness = createObserverHarness();

    harness.start();
    harness.notifyDomReady();
    harness.setEditorStore( createEditorStore( 'wp_block' ) );
    harness.notify();

    expect( harness.events ).toEqual( [ 'unsubscribe', 'unregister' ] );
    expect( harness.unregisterBlockType ).toHaveBeenCalledTimes( 1 );
  } );

  test( '実registryでも投稿ストア不在時のブロック登録を継続する', () => {
    let stop;

    expect( selectData( 'core/editor' ) ).toBeUndefined();

    // 実registryの購読と試験ブロックを必ず片付ける後処理。
    try {
      stop = observePatternEditorToggleBox( {
        domReady: jest.fn(),
        select: selectData,
        subscribe: subscribeData,
        unregisterBlockType: unregisterRegisteredBlockType,
      } );

      expect( stop ).toEqual( expect.any( Function ) );
      expect( () =>
        registerBlockType( REGISTRY_TEST_BLOCK_NAME, {
          apiVersion: 3,
          title: '投稿ストア不在試験',
          category: 'text',
          edit: () => null,
          save: () => null,
        } )
      ).not.toThrow();
      expect( getBlockType( REGISTRY_TEST_BLOCK_NAME ) ).toBeDefined();
    } finally {
      if ( typeof stop === 'function' ) {
        stop();
      }
      if ( getBlockType( REGISTRY_TEST_BLOCK_NAME ) ) {
        unregisterRegisteredBlockType( REGISTRY_TEST_BLOCK_NAME );
      }
    }
  } );
} );
