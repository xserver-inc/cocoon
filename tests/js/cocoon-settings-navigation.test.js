'use strict';

const assert = require( 'assert' );
const fs = require( 'fs' );
const path = require( 'path' );
const { JSDOM, VirtualConsole } = require( 'jsdom' );

const navigationScript = fs.readFileSync(
  path.resolve( __dirname, '../../js/cocoon-settings-navigation.js' ),
  'utf8'
);
const topPageSource = fs.readFileSync(
  path.resolve( __dirname, '../../lib/page-settings/_top-page.php' ),
  'utf8'
);
const bootScriptMatch = topPageSource.match(
  /<script>\s*([\s\S]*?document\.currentScript\.parentElement[\s\S]*?)\s*<\/script>/u
);
assert.ok( bootScriptMatch, 'PHP内の初期描画ブート処理を取得できること' );
const bootScript = bootScriptMatch[ 1 ];

// 実際のCocoon設定画面と同じ直下radio・label・panelの並びを最小構成で再現する。
const createSettingsMarkup = (
  initialChecked = 'alpha',
  initialMode = 'responsive'
) => `<!doctype html>
<html lang="ja">
  <body class="toplevel_page_theme-settings">
    <div class="wrap admin-settings">
      <form id="settings-form">
        <input id="draft-title" name="draft_title" type="text" value="保存済み値">
        <input id="keep-enabled" name="keep_enabled" type="checkbox" value="1" checked>
        <div id="tabs" data-navigation-initial-mode="${ initialMode }">
          <input id="tab-alpha-input" class="tab-input" name="tab" type="radio" value="alpha"${
            initialChecked === 'alpha' ? ' checked' : ''
          }>
          <label class="tab-label" for="tab-alpha-input">Alpha</label>
          <input id="tab-beta-input" class="tab-input" name="tab" type="radio" value="beta"${
            initialChecked === 'beta' ? ' checked' : ''
          }>
          <label class="tab-label" for="tab-beta-input">Beta</label>
          <input id="tab-skin-hidden-input" class="tab-input" name="tab" type="radio" value="skin-hidden"${
            initialChecked === 'skin-hidden' ? ' checked' : ''
          }>
          <label class="tab-label" for="tab-skin-hidden-input" style="display: none">Skin hidden</label>
          <div id="tab-alpha-content" class="metabox-holder">Alpha panel</div>
          <div id="tab-beta-content" class="metabox-holder">Beta panel</div>
          <div id="tab-skin-hidden-content" class="metabox-holder">Hidden panel</div>
        </div>
      </form>
    </div>
  </body>
</html>`;

// PHPから渡される翻訳済み設定を、テストで必要な全項目を含めて再現する。
const createNavigationSettings = ( initialMode ) => ( {
  ajaxUrl: 'http://localhost/wp-admin/admin-ajax.php',
  nonce: 'test-nonce',
  initialMode,
  viewModeLabel: '設定メニューの表示',
  viewModeDescription:
    'おすすめ表示では、設定メニューを自動で見やすく切り替えます。設定内容には影響しません。',
  responsiveModeLabel: 'おすすめ表示',
  responsiveModeDescription: '設定メニューを自動で見やすく切り替えます。',
  tabsModeLabel: '従来の表示',
  tabsModeDescription: 'これまでと同じ順番でタブを表示します。',
  modeSaveError: '表示モードを保存できませんでした。',
  modeNonceError:
    '表示モードの保存期限が切れました。ページを再読み込みしてください。',
  modePermissionError: '表示モードを保存する権限がありません。',
  modeTimeoutError: '表示モードの保存がタイムアウトしました。',
  navigationLabel: 'Cocoon設定項目',
  menuTitle: '設定メニュー',
  mobileLabel: '設定項目',
  searchLabel: '設定項目を検索',
  searchPlaceholder: '設定項目を検索',
  noResults: '該当する設定項目はありません。',
  resultsLabel: '%d件の設定項目が見つかりました。',
  fallbackLabel: '追加の設定',
  groups: [
    {
      label: 'テスト分類',
      tabs: [ 'tab-alpha-input', 'tab-beta-input', 'tab-skin-hidden-input' ],
    },
  ],
} );

// Promiseだけで進む非同期保存処理を、実時間を待たず最後まで進める。
const flushMicrotasks = async ( count = 12 ) => {
  for ( let index = 0; index < count; index += 1 ) {
    await Promise.resolve();
  }
};

// 各テストを完全に独立させるため、新しいDOMとブラウザAPIの代替実装を毎回用意する。
const createHarness = async ( {
  closedDetailsHaveRects = true,
  hasResizeObserver = true,
  initialChecked = 'alpha',
  initialMode = 'responsive',
  initialTabsClass = '',
  reverseTabs = false,
  throwOnObserve = false,
  ungroupedBeta = false,
} = {} ) => {
  const browserErrors = [];
  const virtualConsole = new VirtualConsole();
  virtualConsole.on( 'jsdomError', ( error ) => browserErrors.push( error ) );

  const dom = new JSDOM( createSettingsMarkup( initialChecked, initialMode ), {
    pretendToBeVisual: true,
    runScripts: 'outside-only',
    url: 'http://localhost/wp-admin/admin.php?page=theme-settings',
    virtualConsole,
  } );
  const { window } = dom;
  const { document } = window;
  const tabs = document.getElementById( 'tabs' );
  if ( reverseTabs ) {
    const alpha = document.getElementById( 'tab-alpha-input' );
    tabs.insertBefore( document.getElementById( 'tab-beta-input' ), alpha );
    tabs.insertBefore( document.querySelector( 'label[for="tab-beta-input"]' ), alpha );
  }
  const scrollCalls = [];
  const resizeObservers = [];
  let tabsWidth = 1200;
  let layoutMode = 'desktop';

  if ( initialTabsClass ) {
    tabs.className = initialTabsClass;
  }

  // PHP内の極小ブート処理を再現し、メイン初期化が同期的に解除することを検証する。
  if ( initialMode === 'responsive' ) {
    tabs.classList.add( 'is-navigation-booting' );
  }

  Object.defineProperty( tabs, 'clientWidth', {
    configurable: true,
    get: () => tabsWidth,
  } );
  Object.defineProperty( window, 'innerHeight', {
    configurable: true,
    value: 800,
  } );

  const nativeGetComputedStyle = window.getComputedStyle.bind( window );

  // 実CSSのレスポンシブ表示を模倣し、JSのフォーカス移動をDOM上で検証可能にする。
  window.getComputedStyle = ( element ) => {
    const nativeStyle = nativeGetComputedStyle( element );
    let display = nativeStyle.display || 'block';
    const isResponsive = tabs.classList.contains(
      'is-navigation-mode-responsive'
    );
    const usesSidebar = layoutMode === 'desktop' && tabs.classList.contains( 'is-navigation-wide' );
    const isEnhanced = tabs.classList.contains( 'is-navigation-enhanced' );

    if ( element.hidden ) {
      display = 'none';
    } else if ( element.classList.contains( 'cocoon-settings-navigation' ) ) {
      display = isResponsive && layoutMode !== 'fallback' ? 'block' : 'none';
    } else if ( element.classList.contains( 'cocoon-settings-sidebar' ) ) {
      display = isResponsive && usesSidebar ? 'block' : 'none';
    } else if (
      element.classList.contains( 'cocoon-settings-mobile-picker' )
    ) {
      display = isResponsive && ! usesSidebar ? 'block' : 'none';
    } else if ( element.classList.contains( 'is-navigation-excluded' ) ) {
      display = 'none';
    } else if (
      isEnhanced &&
      ( element.classList.contains( 'tab-input' ) ||
        element.classList.contains( 'tab-label' ) )
    ) {
      display = ! isResponsive || layoutMode === 'fallback' ? 'block' : 'none';
    }

    return {
      display,
      visibility: nativeStyle.visibility || 'visible',
    };
  };

  // jsdomの矩形情報の補完と、閉じたdetails内にも矩形が残るブラウザーの再現
  Object.defineProperty( window.HTMLElement.prototype, 'getClientRects', {
    configurable: true,
    value() {
      let currentElement = this;

      while ( currentElement && currentElement.nodeType === 1 ) {
        const style = window.getComputedStyle( currentElement );

        if (
          currentElement.hidden ||
          style.display === 'none' ||
          style.visibility === 'hidden' ||
          style.visibility === 'collapse'
        ) {
          return [];
        }

        currentElement = currentElement.parentElement;
      }

      const closedDetails = this.closest( 'details:not([open])' );
      const isVisibleSummary =
        this.tagName === 'SUMMARY' && this.parentElement === closedDetails;

      if (
        ! closedDetailsHaveRects &&
        closedDetails &&
        this !== closedDetails &&
        ! isVisibleSummary
      ) {
        return [];
      }

      return [
        { bottom: 20, height: 20, left: 0, right: 100, top: 0, width: 100 },
      ];
    },
  } );

  // スクロール発生先とオプションを記録し、ページ外パネルだけが対象か確認する。
  window.HTMLElement.prototype.scrollIntoView = function scrollIntoView(
    options
  ) {
    scrollCalls.push( { element: this, options } );
  };
  window.requestAnimationFrame = ( callback ) => {
    callback();
    return 1;
  };

  // ResizeObserverを手動発火できるようにし、実際の幅変化処理を直接通す。
  class TestResizeObserver {
    constructor( callback ) {
      this.callback = callback;
      this.target = null;
      resizeObservers.push( this );
    }

    observe( target ) {
      if ( throwOnObserve ) {
        throw new Error( 'ResizeObserver observe failure' );
      }

      this.target = target;
    }

    trigger() {
      this.callback( [ { target: this.target } ], this );
    }
  }

  if ( hasResizeObserver ) {
    window.ResizeObserver = TestResizeObserver;
  } else {
    delete window.ResizeObserver;
  }
  window.cocoonSettingsNavigationData = createNavigationSettings( initialMode );
  // 拡張タブと同じく、PHPの標準分類に含まれないタブの再現
  if ( ungroupedBeta ) {
    window.cocoonSettingsNavigationData.groups[ 0 ].tabs = [
      'tab-alpha-input',
      'tab-skin-hidden-input',
    ];
  }
  window.fetch = async ( url, options ) => {
    const savedMode = new window.URLSearchParams( options.body ).get( 'mode' );

    return {
      json: async () => ( { data: { mode: savedMode }, success: true } ),
      ok: true,
      status: 200,
    };
  };

  const form = document.getElementById( 'settings-form' );
  const initialSuccessfulNames = Array.from(
    new window.FormData( form ).keys()
  ).sort();
  const initialFormEntries = Array.from(
    new window.FormData( form ).entries()
  );
  const initialInputAttributeStates = Array.from(
    tabs.querySelectorAll( ':scope > .tab-input[type="radio"]' ),
    ( input ) => ( {
      ariaHidden: input.getAttribute( 'aria-hidden' ),
      className: input.getAttribute( 'class' ),
      inert: input.getAttribute( 'inert' ),
      tabindex: input.getAttribute( 'tabindex' ),
    } )
  );

  // DOMが存在するフッター読込では、DOMContentLoadedを待たず同じ評価中に完成させる。
  const readyStateBeforeEvaluation = document.readyState;
  window.eval( navigationScript );

  assert.strictEqual(
    tabs.classList.contains( 'is-navigation-booting' ),
    false,
    '初期描画マスクを同期的に解除すること'
  );

  if ( ! throwOnObserve ) {
    assert.strictEqual(
      document.querySelectorAll( '.cocoon-settings-view-mode' ).length,
      1,
      'ナビゲーションは一度だけ初期化されること'
    );
    assert.strictEqual(
      tabs.getAttribute( 'data-cocoon-settings-navigation-state' ),
      'ready',
      '同期初期化の完了状態を記録すること'
    );
  }
  assert.strictEqual(
    resizeObservers.length,
    hasResizeObserver ? 1 : 0,
    '利用可能な場合だけResizeObserverを1件登録すること'
  );

  return {
    browserErrors,
    document,
    dom,
    form,
    initialFormEntries,
    initialInputAttributeStates,
    initialSuccessfulNames,
    readyStateBeforeEvaluation,
    observer: resizeObservers[ 0 ] || null,
    scrollCalls,
    setLayoutMode: ( mode ) => {
      layoutMode = mode;
    },
    setTabsWidth: ( width ) => {
      tabsWidth = width;
    },
    tabs,
    triggerResponsiveState: () => {
      if ( resizeObservers[ 0 ] ) {
        resizeObservers[ 0 ].trigger();
      } else {
        window.dispatchEvent( new window.Event( 'resize' ) );
      }
    },
    getResizeObserverCount: () => resizeObservers.length,
    window,
  };
};

// キー操作を実DOMへ送り、ブラウザ標準のfocus状態まで含めて検証する。
const pressKey = ( window, element, key ) => {
  element.dispatchEvent(
    new window.KeyboardEvent( 'keydown', {
      bubbles: true,
      cancelable: true,
      key,
    } )
  );
};

// 表示モードのARIA選択とTab停止が常に1件だけで一致することを確認する。
const assertViewModeSelection = ( control, expectedMode ) => {
  const modeButtons = Array.from(
    control.querySelectorAll( '[role="radio"]' )
  );
  const selectedButtons = modeButtons.filter(
    ( button ) => button.getAttribute( 'aria-checked' ) === 'true'
  );

  assert.strictEqual(
    selectedButtons.length,
    1,
    'aria-checked=trueの表示モードを厳密に1件にすること'
  );
  assert.strictEqual(
    selectedButtons[ 0 ].dataset.navigationMode,
    expectedMode,
    '保存済みの表示モードとARIA選択を一致させること'
  );
  modeButtons.forEach( ( button ) => {
    assert.strictEqual(
      button.tabIndex,
      button === selectedButtons[ 0 ] ? 0 : -1,
      '選択中の表示モードだけをTab停止にすること'
    );
  } );
};

// 表示ナビ・フォーム境界・キーボード・スクロール・幅変更を一連の利用操作で検証する。
const testNavigationAndFormBehavior = async () => {
  const harness = await createHarness();
  const { document, form, tabs, window } = harness;
  const betaRadio = document.getElementById( 'tab-beta-input' );
  const alphaButton = document.querySelector(
    '[data-tab-target="tab-alpha-input"]'
  );
  const betaButton = document.querySelector(
    '[data-tab-target="tab-beta-input"]'
  );
  const mobileSelect = document.getElementById(
    'cocoon-settings-mobile-select'
  );

  assert.ok( alphaButton, '可視ラベルのAlphaをナビへ生成すること' );
  assert.ok( betaButton, '可視ラベルのBetaをナビへ生成すること' );
  assert.strictEqual(
    document.querySelector( '[data-tab-target="tab-skin-hidden-input"]' ),
    null,
    'スキンが隠したラベルを新ナビへ復活させないこと'
  );
  assert.strictEqual(
    mobileSelect.querySelector( 'option[value="tab-skin-hidden-input"]' ),
    null,
    'スキンが隠したラベルをモバイル選択欄へ復活させないこと'
  );

  assert.deepStrictEqual(
    Array.from( new window.FormData( form ).keys() ).sort(),
    harness.initialSuccessfulNames,
    '表示UIの生成だけではフォーム成功コントロールを増やさないこと'
  );
  assert.strictEqual(
    document.querySelectorAll(
      '.cocoon-settings-view-mode [name], .cocoon-settings-navigation [name]'
    ).length,
    0,
    '新しい表示専用コントロールにname属性を付けないこと'
  );

  assert.ok( tabs.classList.contains( 'is-navigation-wide' ) );
  harness.setTabsWidth( 900 );
  harness.triggerResponsiveState();
  assert.ok( ! tabs.classList.contains( 'is-navigation-wide' ) );
  harness.setTabsWidth( 1200 );
  harness.triggerResponsiveState();
  assert.ok( tabs.classList.contains( 'is-navigation-wide' ) );

  assert.strictEqual(
    alphaButton.tabIndex,
    0,
    '選択中項目だけをTab停止にすること'
  );
  assert.strictEqual( betaButton.tabIndex, -1 );
  pressKey( window, alphaButton, 'ArrowDown' );
  assert.strictEqual( document.activeElement, betaButton );
  assert.strictEqual( betaButton.tabIndex, 0 );
  pressKey( window, betaButton, 'ArrowUp' );
  assert.strictEqual( document.activeElement, alphaButton );
  pressKey( window, alphaButton, 'End' );
  assert.strictEqual( document.activeElement, betaButton );
  pressKey( window, betaButton, 'Home' );
  assert.strictEqual( document.activeElement, alphaButton );

  const draftTitle = document.getElementById( 'draft-title' );
  const betaPanel = document.getElementById( 'tab-beta-content' );
  let betaRadioClickCount = 0;
  draftTitle.value = 'ユーザーがまだ保存していない入力';
  betaRadio.addEventListener( 'click', () => {
    betaRadioClickCount += 1;
  } );
  betaPanel.getBoundingClientRect = () => ( {
    bottom: 1300,
    height: 200,
    left: 0,
    right: 800,
    top: 1100,
    width: 800,
  } );
  betaButton.click();

  assert.strictEqual(
    betaRadioClickCount,
    1,
    '既存radioのclickを代理すること'
  );
  assert.ok( betaRadio.checked, '既存radioを唯一の選択状態として更新すること' );
  assert.strictEqual(
    draftTitle.value,
    'ユーザーがまだ保存していない入力',
    'タブ切替で未保存の入力値を失わないこと'
  );
  assert.deepStrictEqual(
    Array.from( new window.FormData( form ).keys() ).sort(),
    harness.initialSuccessfulNames,
    'タブ切替後もフォーム成功コントロール名を増やさないこと'
  );
  assert.ok(
    harness.scrollCalls.some(
      ( call ) => call.element === betaPanel && call.options.block === 'start'
    ),
    '切替先パネルの先頭が画面外なら先頭へスクロールすること'
  );

  const search = document.getElementById( 'cocoon-settings-navigation-search' );
  const composingEnter = new window.KeyboardEvent( 'keydown', {
    bubbles: true,
    cancelable: true,
    isComposing: true,
    key: 'Enter',
  } );
  search.dispatchEvent( composingEnter );
  assert.strictEqual(
    composingEnter.defaultPrevented,
    false,
    '日本語IMEの変換確定Enterを妨げないこと'
  );

  const legacyImeEnter = new window.KeyboardEvent( 'keydown', {
    bubbles: true,
    cancelable: true,
    key: 'Enter',
    keyCode: 229,
  } );
  search.dispatchEvent( legacyImeEnter );
  assert.strictEqual(
    legacyImeEnter.defaultPrevented,
    false,
    'keyCode 229のIME変換確定Enterを妨げないこと'
  );

  const regularEnter = new window.KeyboardEvent( 'keydown', {
    bubbles: true,
    cancelable: true,
    key: 'Enter',
  } );
  search.dispatchEvent( regularEnter );
  assert.ok(
    regularEnter.defaultPrevented,
    '通常Enterでは設定フォームを送信しないこと'
  );

  betaButton.focus();
  search.value = 'Alpha';
  search.dispatchEvent( new window.Event( 'input', { bubbles: true } ) );
  assert.ok( betaButton.hidden, '検索不一致のフォーカス元が非表示になること' );
  assert.strictEqual( document.activeElement, betaButton );

  harness.setTabsWidth( 900 );
  harness.setLayoutMode( 'fallback' );
  let ariaHiddenAtFocus = 'focus-event-not-fired';
  betaRadio.addEventListener( 'focus', () => {
    ariaHiddenAtFocus = betaRadio.getAttribute( 'aria-hidden' );
  } );
  harness.triggerResponsiveState();
  assert.strictEqual(
    document.activeElement,
    betaRadio,
    '幅変更後は新しく表示された可視UIへフォーカスを移すこと'
  );
  assert.ok( ! betaRadio.hasAttribute( 'aria-hidden' ) );
  assert.strictEqual(
    ariaHiddenAtFocus,
    null,
    'focusイベントの時点でradioがアクセシビリティツリーへ戻っていること'
  );
  assert.notStrictEqual(
    document.activeElement,
    betaButton,
    '非表示になったナビ項目へフォーカスを残さないこと'
  );
  assert.deepStrictEqual( harness.browserErrors, [] );

  harness.dom.window.close();
};

// 検索件数・0件表示・Escape解除・絞り込み中のroving tabindexを実操作で検証する。
const testSearchStateAndRovingTabindex = async () => {
  const harness = await createHarness();
  const { document, window } = harness;
  const search = document.getElementById( 'cocoon-settings-navigation-search' );
  const searchStatus = document.querySelector(
    '.cocoon-settings-navigation-status'
  );
  const alphaButton = document.querySelector(
    '[data-tab-target="tab-alpha-input"]'
  );
  const betaButton = document.querySelector(
    '[data-tab-target="tab-beta-input"]'
  );
  const group = alphaButton.closest( 'details' );

  search.value = 'Alpha';
  search.dispatchEvent( new window.Event( 'input', { bubbles: true } ) );
  assert.strictEqual( alphaButton.hidden, false );
  assert.strictEqual( betaButton.hidden, true );
  assert.strictEqual( searchStatus.hidden, false );
  assert.strictEqual(
    searchStatus.textContent,
    '1件の設定項目が見つかりました。'
  );

  search.value = 'Beta';
  search.dispatchEvent( new window.Event( 'input', { bubbles: true } ) );
  assert.strictEqual( alphaButton.hidden, true );
  assert.strictEqual( betaButton.hidden, false );
  assert.strictEqual( alphaButton.tabIndex, -1 );
  assert.strictEqual(
    betaButton.tabIndex,
    0,
    '選択中項目が検索対象外なら最初の検索結果をTab停止にすること'
  );

  betaButton.focus();
  const filteredArrowDown = new window.KeyboardEvent( 'keydown', {
    bubbles: true,
    cancelable: true,
    key: 'ArrowDown',
  } );
  betaButton.dispatchEvent( filteredArrowDown );
  assert.ok( filteredArrowDown.defaultPrevented );
  assert.strictEqual(
    document.activeElement,
    betaButton,
    '検索結果が1件なら矢印操作でも可視項目内にフォーカスを保つこと'
  );

  search.value = '該当なし';
  search.dispatchEvent( new window.Event( 'input', { bubbles: true } ) );
  assert.strictEqual( group.hidden, true );
  assert.strictEqual( alphaButton.tabIndex, -1 );
  assert.strictEqual( betaButton.tabIndex, -1 );
  assert.strictEqual( searchStatus.hidden, false );
  assert.strictEqual(
    searchStatus.textContent,
    '該当する設定項目はありません。'
  );

  search.focus();
  const escape = new window.KeyboardEvent( 'keydown', {
    bubbles: true,
    cancelable: true,
    key: 'Escape',
  } );
  search.dispatchEvent( escape );
  assert.ok( escape.defaultPrevented );
  assert.strictEqual( search.value, '' );
  assert.strictEqual( searchStatus.hidden, true );
  assert.strictEqual( group.hidden, false );
  assert.strictEqual( group.open, true );
  assert.strictEqual( alphaButton.hidden, false );
  assert.strictEqual( betaButton.hidden, false );
  assert.strictEqual( alphaButton.tabIndex, 0 );
  assert.strictEqual( betaButton.tabIndex, -1 );
  assert.deepStrictEqual( harness.browserErrors, [] );

  harness.dom.window.close();
};

// 切替先の先頭が画面内なら、利用者の現在位置を不要に動かさないことを検証する。
const testInViewportPanelDoesNotScroll = async () => {
  const harness = await createHarness();
  const { document } = harness;
  const betaPanel = document.getElementById( 'tab-beta-content' );
  betaPanel.getBoundingClientRect = () => ( {
    bottom: 500,
    height: 300,
    left: 0,
    right: 800,
    top: 200,
    width: 800,
  } );

  document.querySelector( '[data-tab-target="tab-beta-input"]' ).click();
  assert.deepStrictEqual(
    harness.scrollCalls,
    [],
    '切替先パネルの先頭が画面内ならスクロールしないこと'
  );

  harness.dom.window.close();
};

// モバイル選択欄のchangeが既存radio・ナビ状態・切替先パネルへ同期することを検証する。
const testMobileSelectSynchronization = async () => {
  const harness = await createHarness();
  const { document, form, window } = harness;
  const alphaRadio = document.getElementById( 'tab-alpha-input' );
  const betaRadio = document.getElementById( 'tab-beta-input' );
  const betaPanel = document.getElementById( 'tab-beta-content' );
  const mobileSelect = document.getElementById(
    'cocoon-settings-mobile-select'
  );
  const alphaButton = document.querySelector(
    '[data-tab-target="tab-alpha-input"]'
  );
  const betaButton = document.querySelector(
    '[data-tab-target="tab-beta-input"]'
  );
  let betaRadioClickCount = 0;

  harness.setTabsWidth( 600 );
  harness.setLayoutMode( 'mobile' );
  harness.triggerResponsiveState();
  betaRadio.addEventListener( 'click', () => {
    betaRadioClickCount += 1;
  } );
  betaPanel.getBoundingClientRect = () => ( {
    bottom: 1300,
    height: 200,
    left: 0,
    right: 320,
    top: 1100,
    width: 320,
  } );

  mobileSelect.value = 'tab-beta-input';
  mobileSelect.dispatchEvent( new window.Event( 'change', { bubbles: true } ) );

  assert.strictEqual( betaRadioClickCount, 1 );
  assert.strictEqual( alphaRadio.checked, false );
  assert.strictEqual( betaRadio.checked, true );
  assert.strictEqual( mobileSelect.value, 'tab-beta-input' );
  assert.strictEqual( alphaButton.getAttribute( 'aria-current' ), 'false' );
  assert.strictEqual( betaButton.getAttribute( 'aria-current' ), 'true' );
  assert.ok(
    harness.scrollCalls.some(
      ( call ) => call.element === betaPanel && call.options.block === 'start'
    ),
    'モバイル選択時も画面外の切替先パネル先頭へ移動すること'
  );
  assert.deepStrictEqual(
    Array.from( new window.FormData( form ).keys() ).sort(),
    harness.initialSuccessfulNames,
    'モバイル選択でもフォーム成功コントロール名を増やさないこと'
  );
  assert.strictEqual(
    document.getElementById( 'draft-title' ).value,
    '保存済み値'
  );
  assert.strictEqual( document.getElementById( 'keep-enabled' ).checked, true );
  assert.deepStrictEqual( harness.browserErrors, [] );

  harness.dom.window.close();
};

// スキンが隠したradioがcheckedでも、値を変えず新UIだけ未選択にすることを検証する。
const testHiddenCheckedRadioPreservation = async () => {
  const harness = await createHarness( { initialChecked: 'skin-hidden' } );
  const { document, form, window } = harness;
  const hiddenRadio = document.getElementById( 'tab-skin-hidden-input' );
  const alphaRadio = document.getElementById( 'tab-alpha-input' );
  const mobileSelect = document.getElementById(
    'cocoon-settings-mobile-select'
  );

  assert.ok(
    hiddenRadio.checked,
    'スキンが隠した既存checked状態を初期化時に保持すること'
  );
  assert.ok( ! alphaRadio.checked, '可視radioへ暗黙に選択を移さないこと' );
  assert.strictEqual(
    mobileSelect.selectedIndex,
    -1,
    '新しい選択欄は未選択にすること'
  );
  assert.ok(
    hiddenRadio.hasAttribute( 'inert' ),
    '非表示radioを操作対象から除外すること'
  );
  assert.strictEqual( hiddenRadio.getAttribute( 'aria-hidden' ), 'true' );
  assert.strictEqual( hiddenRadio.tabIndex, -1 );
  assert.strictEqual( hiddenRadio.getClientRects().length, 0 );

  harness.setTabsWidth( 900 );
  harness.setLayoutMode( 'fallback' );
  harness.triggerResponsiveState();
  harness.setTabsWidth( 600 );
  harness.setLayoutMode( 'mobile' );
  harness.triggerResponsiveState();
  harness.setTabsWidth( 1200 );
  harness.setLayoutMode( 'desktop' );
  harness.triggerResponsiveState();

  assert.ok(
    hiddenRadio.checked,
    '画面幅を往復しても非表示radioのcheckedを保持すること'
  );
  assert.ok( ! alphaRadio.checked, '画面幅変更で可視radioをclickしないこと' );
  assert.strictEqual(
    hiddenRadio.getAttribute( 'aria-hidden' ),
    'true',
    'CSSフォールバックでも非表示radioを読み上げ対象へ戻さないこと'
  );
  assert.strictEqual( hiddenRadio.tabIndex, -1 );
  assert.strictEqual( hiddenRadio.getClientRects().length, 0 );
  assert.deepStrictEqual(
    Array.from( new window.FormData( form ).entries() ),
    harness.initialFormEntries,
    '画面幅変更でフォームデータを変えないこと'
  );

  harness.dom.window.close();
};

// ResizeObserver非対応環境でもwindow resizeで幅クラスを更新できることを検証する。
const testWindowResizeFallback = async () => {
  const harness = await createHarness( { hasResizeObserver: false } );
  assert.strictEqual( harness.observer, null );
  assert.ok( harness.tabs.classList.contains( 'is-navigation-wide' ) );

  harness.setTabsWidth( 900 );
  harness.triggerResponsiveState();
  assert.ok( ! harness.tabs.classList.contains( 'is-navigation-wide' ) );

  harness.dom.window.close();
};

// 表示モードが1つの2択セグメントとして認識・操作され、フォーム値へ混ざらないことを検証する。
const testViewModeSegmentedControl = async () => {
  const harness = await createHarness();
  const { document, form, window } = harness;
  const control = document.querySelector(
    '.cocoon-settings-view-mode-control'
  );
  const responsiveButton = control.querySelector(
    '[data-navigation-mode="responsive"]'
  );
  const tabsButton = control.querySelector( '[data-navigation-mode="tabs"]' );

  assert.strictEqual( control.getAttribute( 'role' ), 'radiogroup' );
  assert.strictEqual(
    control.getAttribute( 'aria-labelledby' ),
    'cocoon-settings-view-mode-label'
  );
  assert.strictEqual(
    control.getAttribute( 'aria-describedby' ),
    'cocoon-settings-view-mode-description'
  );
  assert.strictEqual( responsiveButton.textContent, 'おすすめ表示' );
  assert.strictEqual( tabsButton.textContent, '従来の表示' );
  assert.strictEqual( responsiveButton.getAttribute( 'role' ), 'radio' );
  assert.strictEqual( tabsButton.getAttribute( 'role' ), 'radio' );
  assert.strictEqual( responsiveButton.getAttribute( 'aria-checked' ), 'true' );
  assert.strictEqual( tabsButton.getAttribute( 'aria-checked' ), 'false' );
  assert.strictEqual( responsiveButton.tabIndex, 0 );
  assert.strictEqual( tabsButton.tabIndex, -1 );
  assert.strictEqual(
    control.querySelectorAll( '[aria-checked="true"]' ).length,
    1
  );
  assert.strictEqual(
    document.querySelector( '.cocoon-settings-view-mode-recommended' ),
    null,
    '別体のおすすめバッジを生成しないこと'
  );

  const beforeEntries = Array.from( new window.FormData( form ).entries() );
  const arrowRight = new window.KeyboardEvent( 'keydown', {
    bubbles: true,
    cancelable: true,
    key: 'ArrowRight',
  } );
  responsiveButton.dispatchEvent( arrowRight );

  assert.ok( arrowRight.defaultPrevented );
  assert.strictEqual( document.activeElement, tabsButton );
  assert.strictEqual(
    responsiveButton.getAttribute( 'aria-checked' ),
    'false'
  );
  assert.strictEqual( tabsButton.getAttribute( 'aria-checked' ), 'true' );
  assert.strictEqual( responsiveButton.tabIndex, -1 );
  assert.strictEqual( tabsButton.tabIndex, 0 );
  assert.strictEqual(
    control.querySelectorAll( '[aria-checked="true"]' ).length,
    1
  );
  assert.deepStrictEqual(
    Array.from( new window.FormData( form ).entries() ),
    beforeEntries,
    '表示モード切替でもフォームデータを変更しないこと'
  );

  await flushMicrotasks();
  pressKey( window, tabsButton, 'ArrowLeft' );
  await flushMicrotasks();
  assert.strictEqual( document.activeElement, responsiveButton );
  assert.strictEqual( responsiveButton.getAttribute( 'aria-checked' ), 'true' );
  assert.strictEqual( tabsButton.getAttribute( 'aria-checked' ), 'false' );
  assert.strictEqual(
    control.querySelectorAll( '[aria-checked="true"]' ).length,
    1
  );
  assert.deepStrictEqual( harness.browserErrors, [] );

  harness.dom.window.close();
};

// 応答停止時の30秒での中断と表示モード操作の再開の検証
const testTimeoutRecovery = async () => {
  const harness = await createHarness( { initialMode: 'tabs' } );
  const { document, window } = harness;
  let timeoutDelay = null;

  window.fetch = ( url, options ) =>
    new Promise( ( resolve, reject ) => {
      const rejectAsAborted = () => {
        reject( new window.DOMException( 'Aborted', 'AbortError' ) );
      };

      if ( options.signal.aborted ) {
        rejectAsAborted();
        return;
      }

      options.signal.addEventListener( 'abort', rejectAsAborted, {
        once: true,
      } );
    } );
  window.setTimeout = ( callback, delay ) => {
    timeoutDelay = delay;
    Promise.resolve().then( callback );
    return 99;
  };
  window.clearTimeout = () => {};

  const control = document.querySelector(
    '.cocoon-settings-view-mode-control'
  );
  const tabsButton = control.querySelector( '[data-navigation-mode="tabs"]' );
  const responsiveButton = control.querySelector(
    '[data-navigation-mode="responsive"]'
  );
  const status = document.querySelector( '.cocoon-settings-view-mode-status' );
  tabsButton.focus();
  pressKey( window, tabsButton, 'ArrowLeft' );
  await flushMicrotasks();

  assert.strictEqual( timeoutDelay, 30000, '保存停止を30秒で中断すること' );
  assert.strictEqual( control.hasAttribute( 'aria-busy' ), false );
  assert.strictEqual(
    control.querySelectorAll( '[aria-disabled="true"]' ).length,
    0,
    'タイムアウト後は全モードボタンを再び操作可能にすること'
  );
  assert.strictEqual( status.hidden, false );
  assert.strictEqual(
    status.textContent,
    '表示モードの保存がタイムアウトしました。'
  );
  assertViewModeSelection( control, 'responsive' );
  assert.strictEqual(
    responsiveButton.getAttribute( 'aria-checked' ),
    'true'
  );
  assert.strictEqual(
    document.activeElement,
    control.querySelector( '[data-navigation-mode="responsive"]' ),
    'タイムアウト時も現在のボタンへのフォーカスを維持すること'
  );
  assert.deepStrictEqual( harness.browserErrors, [] );

  harness.dom.window.close();
};

// nonce失効を一般通信エラーと区別し、再読込が必要な案内を表示することを検証する。
const testNonceFailureMessage = async ( code = 'invalid_nonce' ) => {
  const harness = await createHarness( { initialMode: 'tabs' } );
  const { document, window } = harness;
  window.fetch = async () => ( {
    json: async () => ( { data: { code }, success: false } ),
    ok: false,
    status: 403,
  } );

  const control = document.querySelector(
    '.cocoon-settings-view-mode-control'
  );
  const tabsButton = control.querySelector( '[data-navigation-mode="tabs"]' );
  const status = document.querySelector( '.cocoon-settings-view-mode-status' );
  tabsButton.focus();
  pressKey( window, tabsButton, 'ArrowLeft' );
  await flushMicrotasks();

  assert.strictEqual( status.hidden, false );
  assert.strictEqual(
    status.textContent,
    code === 'invalid_nonce'
      ? window.cocoonSettingsNavigationData.modeNonceError
      : code === 'forbidden' || code === 'invalid_user'
      ? window.cocoonSettingsNavigationData.modePermissionError
      : window.cocoonSettingsNavigationData.modeSaveError
  );
  assert.strictEqual( control.hasAttribute( 'aria-busy' ), false );
  assert.strictEqual(
    control.querySelectorAll( '[aria-disabled="true"]' ).length,
    0
  );
  assertViewModeSelection( control, 'responsive' );
  assert.strictEqual(
    document.activeElement,
    control.querySelector( '[data-navigation-mode="responsive"]' ),
    '認証失敗時も現在のボタンへのフォーカスを維持すること'
  );
  assert.deepStrictEqual( harness.browserErrors, [] );

  harness.dom.window.close();
};

// 一般保存失敗時の表示・入力・フォーカスの維持と同じモードへの再試行
const testGeneralSaveFailureFocusRecovery = async () => {
  const harness = await createHarness( { initialMode: 'tabs' } );
  const { document, window } = harness;
  window.fetch = async () => ( {
    json: async () => ( { success: false } ),
    ok: false,
    status: 500,
  } );

  const control = document.querySelector(
    '.cocoon-settings-view-mode-control'
  );
  const tabsButton = control.querySelector( '[data-navigation-mode="tabs"]' );
  const responsiveButton = control.querySelector(
    '[data-navigation-mode="responsive"]'
  );
  const status = document.querySelector( '.cocoon-settings-view-mode-status' );
  const draftTitle = document.getElementById( 'draft-title' );

  tabsButton.focus();
  pressKey( window, tabsButton, 'ArrowLeft' );
  await flushMicrotasks();
  assert.strictEqual( status.hidden, false );
  assert.strictEqual(
    status.textContent,
    '表示モードを保存できませんでした。'
  );
  assertViewModeSelection( control, 'responsive' );
  assert.strictEqual(
    document.activeElement,
    control.querySelector( '[data-navigation-mode="responsive"]' ),
    '一般保存失敗時も現在のボタンへのフォーカスを維持すること'
  );

  let retryCount = 0;
  const failedFetch = window.fetch;
  window.fetch = ( ...args ) => {
    retryCount += 1;
    return failedFetch( ...args );
  };
  draftTitle.value = '未保存の入力';
  draftTitle.focus();
  responsiveButton.click();
  await flushMicrotasks();
  assertViewModeSelection( control, 'responsive' );
  assert.strictEqual(
    document.activeElement,
    draftTitle,
    '失敗時にradiogroup外へ移っていたフォーカスを奪わないこと'
  );
  assert.strictEqual( retryCount, 1 );
  assert.strictEqual( draftTitle.value, '未保存の入力' );
  assert.deepStrictEqual( harness.browserErrors, [] );

  harness.dom.window.close();
};

// 中間幅での分類付き選択欄と幅境界の往復・フォーカス移譲の検証
const testIntermediateWidthAndHysteresis = async () => {
  const harness = await createHarness();
  const { document, tabs } = harness;
  const item = document.querySelector( '[data-tab-target="tab-alpha-input"]' );
  const select = document.getElementById( 'cocoon-settings-mobile-select' );
  item.focus();
  for ( const width of [ 1040, 1039, 1020 ] ) {
    harness.setTabsWidth( width );
    harness.triggerResponsiveState();
    assert.ok( tabs.classList.contains( 'is-navigation-wide' ) );
    assert.strictEqual( document.activeElement, item );
  }
  for ( const width of [ 1019, 748, 643, 1039 ] ) {
    harness.setTabsWidth( width );
    harness.triggerResponsiveState();
    assert.ok( ! tabs.classList.contains( 'is-navigation-wide' ) );
    assert.strictEqual( document.activeElement, select );
    assert.strictEqual( document.getElementById( 'tab-alpha-input' ).getAttribute( 'aria-hidden' ), 'true' );
  }
  harness.setTabsWidth( 1040 );
  harness.triggerResponsiveState();
  assert.strictEqual( document.activeElement, item );
  harness.dom.window.close();
};

// 実際のタブ順と現在項目の意味付け・ラベルの一意性の検証
const testNavigationOrderAndLabels = async () => {
  const harness = await createHarness( { reverseTabs: true } );
  const { document } = harness;
  assert.deepStrictEqual(
    Array.from( document.querySelectorAll( '.cocoon-settings-navigation-item' ), ( item ) => item.dataset.tabTarget ),
    [ 'tab-beta-input', 'tab-alpha-input' ]
  );
  assert.deepStrictEqual(
    Array.from( document.querySelectorAll( '.cocoon-settings-mobile-select option' ), ( item ) => item.value ),
    [ 'tab-beta-input', 'tab-alpha-input' ]
  );
  assert.strictEqual( document.querySelectorAll( '.cocoon-settings-navigation-item[aria-current="true"]' ).length, 1 );
  assert.strictEqual( document.querySelectorAll( '.cocoon-settings-navigation-item[aria-pressed]' ).length, 0 );
  const search = document.getElementById( 'cocoon-settings-navigation-search' );
  assert.strictEqual( search.hasAttribute( 'aria-label' ), false );
  assert.strictEqual( search.labels.length, 1 );
  assert.strictEqual( search.labels[ 0 ].textContent, '設定項目を検索' );
  harness.dom.window.close();
};

// 矢印操作時の全ナビ項目に対するレイアウト実測の防止
const testNavigationVisibilityWithoutLayoutReads = async () => {
  const harness = await createHarness();
  const { document, window } = harness;
  const item = document.querySelector( '[data-tab-target="tab-alpha-input"]' );
  const nativeStyle = window.getComputedStyle;
  let reads = 0;
  window.getComputedStyle = ( ...args ) => {
    reads += 1;
    return nativeStyle( ...args );
  };
  pressKey( window, item, 'ArrowDown' );
  assert.strictEqual( document.activeElement.dataset.tabTarget, 'tab-beta-input' );
  assert.strictEqual( reads, 0, '矢印操作で計算済みスタイルを取得しないこと' );
  harness.dom.window.close();
};

// エラー文言を設定する瞬間のライブリージョンの可視性の検証
const testSaveErrorAnnouncementOrder = async () => {
  const harness = await createHarness( { initialMode: 'tabs' } );
  const { document, window } = harness;
  const status = document.querySelector( '.cocoon-settings-view-mode-status' );
  const descriptor = Object.getOwnPropertyDescriptor( window.Node.prototype, 'textContent' );
  let hiddenWhenUpdated = null;
  Object.defineProperty( status, 'textContent', {
    get() { return descriptor.get.call( this ); },
    set( value ) {
      hiddenWhenUpdated = this.hidden;
      descriptor.set.call( this, value );
    },
  } );
  window.fetch = async () => { throw new Error( 'offline' ); };
  document.querySelector( '[data-navigation-mode="responsive"]' ).click();
  await flushMicrotasks();
  assert.strictEqual( hiddenWhenUpdated, false );
  harness.dom.window.close();
};

// DOMが既にある場合の即時初期化と、同じスクリプトの二重評価防止を検証する。
const testImmediateAndIdempotentInitialization = async () => {
  const harness = await createHarness();
  const { document, form, tabs, window } = harness;

  assert.strictEqual(
    harness.readyStateBeforeEvaluation,
    'loading',
    'DOMContentLoaded前でも対象DOMがあれば直ちに初期化すること'
  );
  window.eval( navigationScript );

  assert.strictEqual(
    document.querySelectorAll( '.cocoon-settings-view-mode' ).length,
    1,
    '二重評価でも表示モードUIを増やさないこと'
  );
  assert.strictEqual(
    tabs.querySelectorAll( ':scope > .cocoon-settings-navigation' ).length,
    1,
    '二重評価でもナビゲーションを増やさないこと'
  );
  assert.strictEqual(
    harness.getResizeObserverCount(),
    1,
    '二重評価でも監視処理を増やさないこと'
  );
  assert.strictEqual(
    tabs.getAttribute( 'data-cocoon-settings-navigation-state' ),
    'ready'
  );
  assert.strictEqual(
    document
      .getElementById( 'tab-alpha-input' )
      .classList.contains( 'is-navigation-excluded' ),
    false,
    '二重評価で可視タブをスキン非表示と誤判定しないこと'
  );
  assert.deepStrictEqual(
    Array.from( new window.FormData( form ).entries() ),
    harness.initialFormEntries,
    '二重評価でもフォームデータを変えないこと'
  );

  harness.dom.window.close();
};

// 初期化の最深部で例外が起きても、元タブとフォームを完全に復元することを検証する。
const testInitializationFailureRestoresLegacyTabs = async () => {
  const harness = await createHarness( {
    initialTabsClass: 'tabs custom-tabs-class',
    throwOnObserve: true,
  } );
  const { document, form, tabs, window } = harness;
  const currentInputAttributeStates = Array.from(
    tabs.querySelectorAll( ':scope > .tab-input[type="radio"]' ),
    ( input ) => ( {
      ariaHidden: input.getAttribute( 'aria-hidden' ),
      className: input.getAttribute( 'class' ),
      inert: input.getAttribute( 'inert' ),
      tabindex: input.getAttribute( 'tabindex' ),
    } )
  );

  assert.strictEqual(
    tabs.getAttribute( 'data-cocoon-settings-navigation-state' ),
    'failed'
  );
  assert.strictEqual(
    tabs.className,
    'tabs custom-tabs-class',
    '失敗時は独自クラスを含む初期classを復元すること'
  );
  assert.strictEqual(
    document.querySelectorAll( '.cocoon-settings-view-mode' ).length,
    0
  );
  assert.strictEqual(
    tabs.querySelectorAll( ':scope > .cocoon-settings-navigation' ).length,
    0
  );
  assert.deepStrictEqual(
    currentInputAttributeStates,
    harness.initialInputAttributeStates,
    '失敗時は全radioのclass・tabindex・aria-hidden・inertを復元すること'
  );
  assert.deepStrictEqual(
    Array.from( new window.FormData( form ).entries() ),
    harness.initialFormEntries,
    '失敗時もフォームデータを変えないこと'
  );

  window.eval( navigationScript );
  assert.strictEqual(
    harness.getResizeObserverCount(),
    1,
    '失敗後の二重評価でも自動再初期化しないこと'
  );
  assert.deepStrictEqual( harness.browserErrors, [] );

  harness.dom.window.close();
};

// ローカライズデータが欠けても初期描画マスクを残さず、従来タブを表示する。
const testMissingSettingsReleasesBootMask = () => {
  const dom = new JSDOM( createSettingsMarkup(), {
    pretendToBeVisual: true,
    runScripts: 'outside-only',
    url: 'http://localhost/wp-admin/admin.php?page=theme-settings',
  } );
  const { document } = dom.window;
  const tabs = document.getElementById( 'tabs' );
  tabs.classList.add( 'is-navigation-booting' );

  dom.window.eval( navigationScript );

  assert.strictEqual(
    tabs.classList.contains( 'is-navigation-booting' ),
    false,
    '設定データ不足時も従来タブを再表示すること'
  );
  assert.strictEqual(
    document.querySelectorAll( '.cocoon-settings-navigation' ).length,
    0
  );
  assert.strictEqual(
    tabs.hasAttribute( 'data-cocoon-settings-navigation-state' ),
    false,
    '後から設定データが利用可能になった場合の再試行を妨げないこと'
  );

  dom.window.close();
};

// PHPに埋め込んだ実ブート処理が、初期マスクと自動復帰を正しく行うことを検証する。
const testInlineBootScriptFallback = () => {
  const dom = new JSDOM( createSettingsMarkup(), {
    pretendToBeVisual: true,
    runScripts: 'dangerously',
    url: 'http://localhost/wp-admin/admin.php?page=theme-settings',
  } );
  const { document } = dom.window;
  const tabs = document.getElementById( 'tabs' );
  let fallbackDelay = null;

  dom.window.setTimeout = ( callback, delay ) => {
    fallbackDelay = delay;
    return 1;
  };

  const script = document.createElement( 'script' );
  script.textContent = bootScript;
  tabs.prepend( script );

  assert.strictEqual(
    tabs.classList.contains( 'is-navigation-booting' ),
    true,
    '最初のタブを解析する前に初期描画マスクを付けること'
  );
  assert.strictEqual(
    fallbackDelay,
    10000,
    '10秒の最終復帰経路を登録すること'
  );

  document.dispatchEvent( new dom.window.Event( 'DOMContentLoaded' ) );
  assert.strictEqual(
    tabs.classList.contains( 'is-navigation-booting' ),
    false,
    'メインJavaScriptがなくてもDOMContentLoadedで従来タブへ戻すこと'
  );

  dom.window.close();
};

// 標準分類外の拡張タブに対するナビ生成とradio同期の検証
const testUngroupedTab = async () => {
  const { document, dom, browserErrors } = await createHarness( {
    ungroupedBeta: true,
    initialChecked: 'beta',
  } );
  const betaButton = document.querySelector(
    '[data-tab-target="tab-beta-input"]'
  );
  const mobileSelect = document.getElementById(
    'cocoon-settings-mobile-select'
  );
  assert.ok( betaButton, '標準分類外のタブもナビに含まれること' );
  assert.strictEqual( betaButton.getAttribute( 'aria-current' ), 'true' );
  assert.strictEqual( mobileSelect.value, 'tab-beta-input' );
  assert.ok(
    mobileSelect.querySelector(
      'optgroup[label="追加の設定"] option[value="tab-beta-input"]'
    )
  );
  document.querySelector( '[data-tab-target="tab-alpha-input"]' ).click();
  betaButton.click();
  assert.strictEqual(
    document.getElementById( 'tab-beta-input' ).checked,
    true
  );
  assert.strictEqual( mobileSelect.value, 'tab-beta-input' );
  assert.deepStrictEqual( browserErrors, [] );
  dom.window.close();
};

// 閉じた分類の矩形有無に依存しないキーボード移動とTab停止位置の検証
const testClosedGroupKeyboardNavigation = async () => {
  for ( const closedDetailsHaveRects of [ true, false ] ) {
    const harness = await createHarness( {
      closedDetailsHaveRects,
      ungroupedBeta: true,
    } );
    const { document, window } = harness;
    const alphaButton = document.querySelector(
      '[data-tab-target="tab-alpha-input"]'
    );
    const betaButton = document.querySelector(
      '[data-tab-target="tab-beta-input"]'
    );
    const alphaGroup = alphaButton.closest( 'details' );
    const betaGroup = betaButton.closest( 'details' );

    try {
      assert.ok( alphaGroup.open );
      assert.ok( ! betaGroup.open );
      assert.strictEqual(
        betaButton.getClientRects().length > 0,
        closedDetailsHaveRects,
        '閉じた分類内の矩形をブラウザーごとの挙動で再現すること'
      );

      alphaButton.focus();
      for ( const key of [ 'End', 'ArrowDown', 'Home', 'ArrowUp' ] ) {
        pressKey( window, alphaButton, key );
        assert.strictEqual(
          document.activeElement,
          alphaButton,
          `${ key }では閉じた分類の項目へ移動しないこと`
        );
        assert.strictEqual( alphaButton.tabIndex, 0 );
        assert.strictEqual( betaButton.tabIndex, -1 );
      }

      // 選択タブを変えずに別分類を開いた場合のTab停止位置の移動
      betaGroup.open = true;
      betaGroup.dispatchEvent( new window.Event( 'toggle' ) );
      assert.ok( ! alphaGroup.open );
      assert.strictEqual( alphaButton.tabIndex, -1 );
      assert.strictEqual(
        betaButton.tabIndex,
        0,
        '閉じた選択済み項目ではなく、開いた分類の先頭をTab停止にすること'
      );
      assert.ok( document.getElementById( 'tab-alpha-input' ).checked );

      betaButton.focus();
      for ( const key of [ 'Home', 'ArrowUp', 'End', 'ArrowDown' ] ) {
        pressKey( window, betaButton, key );
        assert.strictEqual( document.activeElement, betaButton );
      }

      // 全分類を閉じた場合の不可視項目へのTab停止の解除
      betaGroup.open = false;
      betaGroup.dispatchEvent( new window.Event( 'toggle' ) );
      assert.strictEqual( alphaButton.tabIndex, -1 );
      assert.strictEqual( betaButton.tabIndex, -1 );

      // 検索による分類の再展開と通常表示への復帰
      const search = document.getElementById(
        'cocoon-settings-navigation-search'
      );
      search.value = 'a';
      search.dispatchEvent( new window.Event( 'input', { bubbles: true } ) );
      assert.ok( alphaGroup.open && betaGroup.open );
      alphaButton.focus();
      pressKey( window, alphaButton, 'End' );
      assert.strictEqual( document.activeElement, betaButton );
      search.value = '';
      search.dispatchEvent( new window.Event( 'input', { bubbles: true } ) );
      assert.ok( alphaGroup.open && ! betaGroup.open );
      assert.strictEqual( alphaButton.tabIndex, 0 );
      assert.strictEqual( betaButton.tabIndex, -1 );
      assert.deepStrictEqual( harness.browserErrors, [] );
    } finally {
      harness.dom.window.close();
    }
  }
};

// 失敗時は終了コードを1にし、npmやCIから確実に検出できる自己完結テストにする。
( async () => {
  process.stdout.write( 'Running navigation and form behavior test...\n' );
  await testNavigationAndFormBehavior();
  process.stdout.write( '標準分類外の拡張タブを検証中...\n' );
  await testUngroupedTab();
  process.stdout.write( '折りたたみ分類のキーボード操作を検証中...\n' );
  await testClosedGroupKeyboardNavigation();
  process.stdout.write( 'Running navigation search state test...\n' );
  await testSearchStateAndRovingTabindex();
  process.stdout.write( 'Running in-viewport scroll behavior test...\n' );
  await testInViewportPanelDoesNotScroll();
  process.stdout.write( 'Running mobile select synchronization test...\n' );
  await testMobileSelectSynchronization();
  process.stdout.write( 'Running hidden checked radio preservation test...\n' );
  await testHiddenCheckedRadioPreservation();
  process.stdout.write( 'Running window resize fallback test...\n' );
  await testWindowResizeFallback();
  process.stdout.write( 'Running view mode segmented control test...\n' );
  await testViewModeSegmentedControl();
  process.stdout.write( 'Running timeout recovery test...\n' );
  await testIntermediateWidthAndHysteresis();
  await testNavigationOrderAndLabels();
  await testNavigationVisibilityWithoutLayoutReads();
  await testSaveErrorAnnouncementOrder();
  await testTimeoutRecovery();
  process.stdout.write( 'Running nonce failure test...\n' );
  await testNonceFailureMessage();
  await testNonceFailureMessage( 'forbidden' );
  await testNonceFailureMessage( 'invalid_user' );
  await testNonceFailureMessage( 'unknown' );
  process.stdout.write( 'Running general save failure focus test...\n' );
  await testGeneralSaveFailureFocusRecovery();
  process.stdout.write(
    'Running immediate and idempotent initialization test...\n'
  );
  await testImmediateAndIdempotentInitialization();
  process.stdout.write(
    'Running initialization failure restoration test...\n'
  );
  await testInitializationFailureRestoresLegacyTabs();
  process.stdout.write( 'Running boot-mask fallback test...\n' );
  testMissingSettingsReleasesBootMask();
  process.stdout.write( 'Running inline boot-script fallback test...\n' );
  testInlineBootScriptFallback();
  process.stdout.write( 'Cocoon settings navigation DOM tests passed.\n' );
} )().catch( ( error ) => {
  process.stderr.write( `${ error.stack || error }\n` );
  process.exitCode = 1;
} );
