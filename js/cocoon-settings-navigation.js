/**
 * Cocoon設定画面のレスポンシブナビゲーション
 *
 * 既存radioを唯一の状態源として使い、保存対象の入力値には触れない。
 */

function cocoonBuildSettingsNavigation( tabs, settings, inputs, panels ) {
  // hidden属性と祖先の表示状態に基づく、実際に描画される操作対象の判定
  const isElementVisible = ( element ) => {
    if ( ! element || ! element.isConnected ) {
      return false;
    }

    let currentElement = element;

    while ( currentElement && currentElement.nodeType === Node.ELEMENT_NODE ) {
      const computedStyle = window.getComputedStyle( currentElement );

      if (
        currentElement.hidden ||
        computedStyle.display === 'none' ||
        computedStyle.visibility === 'hidden' ||
        computedStyle.visibility === 'collapse'
      ) {
        return false;
      }

      // 矩形が残る閉じたdetailsの内容を除外し、先頭summaryとその子要素だけを許可
      if (
        currentElement.tagName === 'DETAILS' &&
        ! currentElement.open &&
        currentElement !== element
      ) {
        const summary = currentElement.querySelector( ':scope > summary' );

        if ( ! summary || ! summary.contains( element ) ) {
          return false;
        }
      }

      currentElement = currentElement.parentElement;
    }

    return element.getClientRects().length > 0;
  };

  const labelElementsByInputId = new Map(
    Array.from( tabs.querySelectorAll( ':scope > .tab-label' ) ).map(
      ( label ) => [ label.htmlFor, label ]
    )
  );
  const labelByInputId = new Map(
    Array.from( labelElementsByInputId, ( [ inputId, label ] ) => [
      inputId,
      label.textContent.trim(),
    ] )
  );

  // スキンが元から隠したタブは拡張UIへ復活させず、従来のchecked状態だけを保持する。
  const navigationInputs = inputs.filter( ( input ) =>
    isElementVisible( labelElementsByInputId.get( input.id ) )
  );
  const navigationInputIds = new Set(
    navigationInputs.map( ( input ) => input.id )
  );

  const inputById = new Map(
    navigationInputs.map( ( input ) => [ input.id, input ] )
  );
  const panelByInputId = new Map(
    panels.map( ( panel ) => [
      panel.id.replace( /-content$/, '-input' ),
      panel,
    ] )
  );
  const configuredInputIds = new Set();

  // PHP側の分類から、現在の画面に実在するAMP・PWAを含む項目だけを採用する。
  const groups = settings.groups
    .map( ( group ) => {
      // フィルター適用済みの画面上のタブ順を優先したグループ内の整列
      const items = Array.isArray( group.tabs )
        ? navigationInputs
            .map( ( input ) => input.id )
            .filter( ( inputId ) => {
              if (
                ! group.tabs.includes( inputId ) ||
                configuredInputIds.has( inputId )
              ) {
                return false;
              }

              configuredInputIds.add( inputId );
              return true;
            } )
            .map( ( inputId ) => ( {
              input: inputById.get( inputId ),
              label: labelByInputId.get( inputId ) || inputId,
            } ) )
        : [];

      return {
        label: group.label,
        items,
      };
    } )
    .filter( ( group ) => group.items.length > 0 );

  // 将来タブが追加された場合も操作不能にせず、未分類項目として末尾へ補完する。
  const ungroupedItems = navigationInputs
    .filter( ( input ) => ! configuredInputIds.has( input.id ) )
    .map( ( input ) => ( {
      input,
      label: labelByInputId.get( input.id ) || input.id,
    } ) );

  if ( ungroupedItems.length > 0 ) {
    groups.push( {
      label: settings.fallbackLabel,
      items: ungroupedItems,
    } );
  }

  if ( groups.length === 0 ) {
    return false;
  }

  // スキンが隠したradioは値を保持したまま、視覚・フォーカス・読み上げ対象から除外する。
  inputs
    .filter( ( input ) => ! navigationInputIds.has( input.id ) )
    .forEach( ( input ) => {
      input.classList.add( 'is-navigation-excluded' );
      input.tabIndex = -1;
      input.setAttribute( 'aria-hidden', 'true' );
      input.setAttribute( 'inert', '' );
    } );

  const responsiveMode = 'responsive';
  const tabsMode = 'tabs';
  const supportedModes = [ responsiveMode, tabsMode ];
  let navigationMode = supportedModes.includes( settings.initialMode )
    ? settings.initialMode
    : tabsMode;
  let isNavigationModeSaved = true;
  let isSavingNavigationMode = false;

  const viewMode = document.createElement( 'div' );
  viewMode.className = 'cocoon-settings-view-mode';

  const viewModeCopy = document.createElement( 'div' );
  viewModeCopy.className = 'cocoon-settings-view-mode-copy';

  const viewModeLabel = document.createElement( 'span' );
  viewModeLabel.id = 'cocoon-settings-view-mode-label';
  viewModeLabel.className = 'cocoon-settings-view-mode-label';
  viewModeLabel.textContent = settings.viewModeLabel;

  const viewModeDescription = document.createElement( 'span' );
  viewModeDescription.id = 'cocoon-settings-view-mode-description';
  viewModeDescription.className = 'cocoon-settings-view-mode-description';
  viewModeDescription.textContent = settings.viewModeDescription;
  viewModeCopy.append( viewModeLabel, viewModeDescription );

  const viewModeControl = document.createElement( 'div' );
  viewModeControl.className = 'cocoon-settings-view-mode-control';
  viewModeControl.setAttribute( 'role', 'radiogroup' );
  viewModeControl.setAttribute(
    'aria-labelledby',
    'cocoon-settings-view-mode-label'
  );
  viewModeControl.setAttribute(
    'aria-describedby',
    'cocoon-settings-view-mode-description'
  );

  // 送信対象に混ざらないtype=buttonを、相互排他的なradioとして読み上げられるようにする。
  const createViewModeButton = ( mode, label, description ) => {
    const button = document.createElement( 'button' );
    button.type = 'button';
    button.className = 'cocoon-settings-view-mode-button';
    button.dataset.navigationMode = mode;
    button.setAttribute( 'role', 'radio' );
    button.setAttribute( 'aria-checked', 'false' );
    button.tabIndex = -1;
    button.title = description;
    button.textContent = label;

    return button;
  };

  const responsiveModeButton = createViewModeButton(
    responsiveMode,
    settings.responsiveModeLabel,
    settings.responsiveModeDescription
  );

  const tabsModeButton = createViewModeButton(
    tabsMode,
    settings.tabsModeLabel,
    settings.tabsModeDescription
  );
  const viewModeButtons = [ responsiveModeButton, tabsModeButton ];
  viewModeControl.append( ...viewModeButtons );

  const viewModeStatus = document.createElement( 'span' );
  viewModeStatus.className = 'cocoon-settings-view-mode-status';
  viewModeStatus.setAttribute( 'role', 'status' );
  viewModeStatus.hidden = true;
  viewMode.append( viewModeCopy, viewModeControl, viewModeStatus );

  const navigation = document.createElement( 'div' );
  navigation.className = 'cocoon-settings-navigation';

  const sidebar = document.createElement( 'nav' );
  sidebar.className = 'cocoon-settings-sidebar';
  sidebar.setAttribute( 'aria-label', settings.navigationLabel );

  const sidebarHeader = document.createElement( 'div' );
  sidebarHeader.className = 'cocoon-settings-sidebar-header';

  const sidebarTitle = document.createElement( 'div' );
  sidebarTitle.className = 'cocoon-settings-sidebar-title';
  sidebarTitle.textContent = settings.menuTitle;
  sidebarHeader.append( sidebarTitle );

  const searchLabel = document.createElement( 'label' );
  searchLabel.className = 'screen-reader-text';
  searchLabel.htmlFor = 'cocoon-settings-navigation-search';
  searchLabel.textContent = settings.searchLabel;

  const search = document.createElement( 'input' );
  search.id = 'cocoon-settings-navigation-search';
  search.className = 'cocoon-settings-navigation-search';
  search.type = 'search';
  search.autocomplete = 'off';
  search.placeholder = settings.searchPlaceholder;
  sidebarHeader.append( searchLabel, search );

  const searchStatus = document.createElement( 'p' );
  searchStatus.className = 'cocoon-settings-navigation-status';
  searchStatus.setAttribute( 'aria-live', 'polite' );
  searchStatus.hidden = true;
  sidebarHeader.append( searchStatus );
  sidebar.append( sidebarHeader );

  const groupList = document.createElement( 'div' );
  groupList.className = 'cocoon-settings-navigation-groups';
  sidebar.append( groupList );

  const mobilePicker = document.createElement( 'div' );
  mobilePicker.className = 'cocoon-settings-mobile-picker';

  const mobileLabel = document.createElement( 'label' );
  mobileLabel.className = 'cocoon-settings-mobile-label';
  mobileLabel.htmlFor = 'cocoon-settings-mobile-select';
  mobileLabel.textContent = settings.mobileLabel;

  const mobileSelect = document.createElement( 'select' );
  mobileSelect.id = 'cocoon-settings-mobile-select';
  mobileSelect.className = 'cocoon-settings-mobile-select';
  mobileSelect.setAttribute( 'aria-label', settings.mobileLabel );
  mobilePicker.append( mobileLabel, mobileSelect );

  const navigationItemByInputId = new Map();
  const groupElementByInputId = new Map();
  const groupElements = [];
  const navigationItems = [];

  // タブ切替後に内容先頭が画面外の場合だけ戻し、ポインター操作のフォーカスは奪わない。
  const revealPanelAfterActivation = ( input, wasChecked ) => {
    if ( wasChecked || ! input.checked ) {
      return;
    }

    window.requestAnimationFrame( () => {
      const panel = panelByInputId.get( input.id );

      if ( ! panel ) {
        return;
      }

      const panelBounds = panel.getBoundingClientRect();
      const isPanelStartOutsideViewport =
        panelBounds.top < 0 || panelBounds.top >= window.innerHeight;

      if ( isPanelStartOutsideViewport ) {
        panel.scrollIntoView( { block: 'start' } );
      }
    } );
  };

  // 同じ分類データから、PC用ボタンとモバイル用optgroupを同時生成してずれを防ぐ。
  groups.forEach( ( group, groupIndex ) => {
    const details = document.createElement( 'details' );
    details.className = 'cocoon-settings-navigation-group';
    details.dataset.groupIndex = String( groupIndex );

    const summary = document.createElement( 'summary' );
    summary.className = 'cocoon-settings-navigation-summary';

    const summaryLabel = document.createElement( 'span' );
    summaryLabel.textContent = group.label;

    const summaryCount = document.createElement( 'span' );
    summaryCount.className = 'cocoon-settings-navigation-count';
    summaryCount.setAttribute( 'aria-hidden', 'true' );
    summaryCount.textContent = String( group.items.length );
    summary.append( summaryLabel, summaryCount );

    const itemList = document.createElement( 'div' );
    itemList.className = 'cocoon-settings-navigation-items';

    const optionGroup = document.createElement( 'optgroup' );
    optionGroup.label = group.label;

    group.items.forEach( ( item ) => {
      const navigationItem = document.createElement( 'button' );
      const contentId = item.input.id.replace( /-input$/, '-content' );

      navigationItem.type = 'button';
      navigationItem.className = 'cocoon-settings-navigation-item';
      navigationItem.dataset.tabTarget = item.input.id;
      navigationItem.setAttribute( 'aria-controls', contentId );
      navigationItem.setAttribute( 'aria-current', 'false' );
      navigationItem.tabIndex = -1;
      navigationItem.textContent = item.label;

      if ( item.input.id === 'tab-reset-input' ) {
        navigationItem.classList.add( 'is-danger' );
      }

      // 表示用ボタンは既存radioのクリックだけを代理し、独自の選択状態を持たない。
      navigationItem.addEventListener( 'click', () => {
        const wasChecked = item.input.checked;
        item.input.click();
        revealPanelAfterActivation( item.input, wasChecked );
      } );

      const option = document.createElement( 'option' );
      option.value = item.input.id;
      option.textContent = item.label;

      itemList.append( navigationItem );
      optionGroup.append( option );
      navigationItemByInputId.set( item.input.id, navigationItem );
      groupElementByInputId.set( item.input.id, details );
      navigationItems.push( navigationItem );
    } );

    details.append( summary, itemList );
    groupList.append( details );
    mobileSelect.append( optionGroup );
    groupElements.push( details );
  } );

  navigation.append( sidebar, mobilePicker );

  // 既存radioと全contentの兄弟関係を変えず、最初のcontent直前へ表示UIだけを挿入する。
  tabs.insertBefore( navigation, panels[ 0 ] );

  // 管理済みの検索・開閉状態だけによる可視項目の取得
  const getVisibleNavigationItems = () =>
    navigationItems.filter( ( item ) => {
      const group = groupElementByInputId.get( item.dataset.tabTarget );
      return ! item.hidden && ! group.hidden && group.open;
    } );

  // PCメニュー内のTab停止を1件に絞り、矢印移動後の位置もroving tabindexへ反映する。
  const setNavigationTabStop = ( preferredItem = null ) => {
    const visibleItems = getVisibleNavigationItems();
    const tabStop = visibleItems.includes( preferredItem )
      ? preferredItem
      : visibleItems[ 0 ] || null;

    navigationItems.forEach( ( item ) => {
      item.tabIndex = item === tabStop ? 0 : -1;
    } );

    return tabStop;
  };

  const synchronizeNavigationTabStop = ( activeInputId = '' ) => {
    setNavigationTabStop(
      navigationItemByInputId.get( activeInputId ) || null
    );
  };

  navigationItems.forEach( ( navigationItem ) => {
    navigationItem.addEventListener( 'keydown', ( event ) => {
      if ( ! [ 'ArrowUp', 'ArrowDown', 'Home', 'End' ].includes( event.key ) ) {
        return;
      }

      const visibleItems = getVisibleNavigationItems();
      const currentIndex = visibleItems.indexOf( navigationItem );

      if ( currentIndex === -1 || visibleItems.length === 0 ) {
        return;
      }

      event.preventDefault();

      let nextIndex = currentIndex;

      if ( event.key === 'Home' ) {
        nextIndex = 0;
      } else if ( event.key === 'End' ) {
        nextIndex = visibleItems.length - 1;
      } else if ( event.key === 'ArrowDown' ) {
        nextIndex = ( currentIndex + 1 ) % visibleItems.length;
      } else {
        nextIndex =
          ( currentIndex - 1 + visibleItems.length ) % visibleItems.length;
      }

      const nextItem = setNavigationTabStop( visibleItems[ nextIndex ] );

      if ( nextItem ) {
        nextItem.focus();
      }
    } );
  } );

  const normalizeSearchText = ( value ) =>
    value.normalize( 'NFKC' ).toLocaleLowerCase().trim();

  // 検索していない通常時は、選択中項目を含むグループだけを開く。
  const openActiveGroup = ( activeInputId ) => {
    groupElements.forEach( ( details ) => {
      details.open = details === groupElementByInputId.get( activeInputId );
    } );
  };

  const getActiveInput = () =>
    inputs.find( ( input ) => input.checked ) || null;

  // checked radioからPC・モバイルの表示状態を毎回再構築する。
  const synchronizeNavigation = () => {
    const activeInput = getActiveInput();
    const activeInputId = activeInput ? activeInput.id : '';

    navigationItemByInputId.forEach( ( navigationItem, inputId ) => {
      const isActive = inputId === activeInputId;
      navigationItem.classList.toggle( 'is-active', isActive );
      navigationItem.setAttribute( 'aria-current', String( isActive ) );
    } );

    groupElements.forEach( ( details ) => {
      details.classList.toggle(
        'is-active-group',
        details === groupElementByInputId.get( activeInputId )
      );
    } );

    if ( activeInput && inputById.has( activeInput.id ) ) {
      mobileSelect.value = activeInput.id;
    } else {
      mobileSelect.selectedIndex = -1;
    }

    if ( normalizeSearchText( search.value ) === '' ) {
      openActiveGroup( activeInputId );
    }

    synchronizeNavigationTabStop( activeInputId );
  };

  // 入力文字に一致する項目だけを残し、検索中は一致グループをすべて展開する。
  const filterNavigation = () => {
    const query = normalizeSearchText( search.value );
    let resultCount = 0;

    groupElements.forEach( ( details ) => {
      const items = Array.from(
        details.querySelectorAll( '.cocoon-settings-navigation-item' )
      );
      let groupResultCount = 0;

      items.forEach( ( item ) => {
        const isMatch =
          query === '' ||
          normalizeSearchText( item.textContent ).includes( query );
        item.hidden = ! isMatch;

        if ( isMatch ) {
          groupResultCount += 1;
          resultCount += 1;
        }
      } );

      details.hidden = groupResultCount === 0;

      if ( query !== '' && groupResultCount > 0 ) {
        details.open = true;
      }
    } );

    searchStatus.hidden = query === '';
    searchStatus.textContent =
      resultCount === 0
        ? settings.noResults
        : settings.resultsLabel.replace( '%d', String( resultCount ) );

    if ( query === '' ) {
      synchronizeNavigation();
    } else {
      const activeInput = getActiveInput();
      synchronizeNavigationTabStop( activeInput ? activeInput.id : '' );
    }
  };

  // 検索中でない場合だけ1グループ開閉にし、一覧の高さを抑える。
  groupElements.forEach( ( details ) => {
    details.addEventListener( 'toggle', () => {
      if ( ! details.open || normalizeSearchText( search.value ) !== '' ) {
        const activeInput = getActiveInput();
        synchronizeNavigationTabStop( activeInput ? activeInput.id : '' );
        return;
      }

      groupElements.forEach( ( otherDetails ) => {
        if ( otherDetails !== details ) {
          otherDetails.open = false;
        }
      } );

      const activeInput = getActiveInput();
      synchronizeNavigationTabStop( activeInput ? activeInput.id : '' );
    } );
  } );

  search.addEventListener( 'input', filterNavigation );
  search.addEventListener( 'keydown', ( event ) => {
    // 日本語IMEの変換確定を検索ショートカットとして扱わない。
    if ( event.isComposing || event.keyCode === 229 ) {
      return;
    }

    // フォーム内検索でEnterを押しても、設定保存を誤送信させない。
    if ( event.key === 'Enter' ) {
      event.preventDefault();
    }

    if ( event.key === 'Escape' && search.value !== '' ) {
      event.preventDefault();
      search.value = '';
      filterNavigation();
    }
  } );

  mobileSelect.addEventListener( 'change', () => {
    const selectedInput = inputById.get( mobileSelect.value );

    if ( selectedInput ) {
      const wasChecked = selectedInput.checked;
      selectedInput.click();
      revealPanelAfterActivation( selectedInput, wasChecked );
    }
  } );

  let interactionMode = null;

  // スクロールバーによる境界付近の往復を防ぐ、開始幅と解除幅の分離
  const synchronizeNavigationWidth = () => {
    const minimumWidth = tabs.classList.contains( 'is-navigation-wide' )
      ? 1020
      : 1040;
    tabs.classList.toggle( 'is-navigation-wide', tabs.clientWidth >= minimumWidth );
  };

  const getActiveViewModeButton = () =>
    viewModeButtons.find(
      ( button ) => button.dataset.navigationMode === navigationMode
    ) || null;

  // 非表示要素へのfocusを避け、候補へ実際に移動できた時点で探索を終了する。
  const focusFirstVisibleCandidate = ( candidates ) => {
    for ( const focusTarget of candidates ) {
      if ( ! focusTarget || ! isElementVisible( focusTarget ) ) {
        continue;
      }

      focusTarget.focus();

      if ( document.activeElement === focusTarget ) {
        return true;
      }
    }

    return false;
  };

  const getFocusCandidatesForMode = ( mode, activeInput ) => {
    const activeNavigationItem = activeInput
      ? navigationItemByInputId.get( activeInput.id )
      : null;
    const firstVisibleNavigationItem = getVisibleNavigationItems()[ 0 ] || null;
    if ( mode === 'desktop' ) {
      return [
        activeNavigationItem,
        search,
        firstVisibleNavigationItem,
        getActiveViewModeButton(),
      ];
    }

    if ( mode === 'mobile' ) {
      return [ mobileSelect, getActiveViewModeButton() ];
    }

    const firstVisibleInput =
      navigationInputs.find( ( input ) => {
        const label = labelElementsByInputId.get( input.id );
        return isElementVisible( input ) && isElementVisible( label );
      } ) || null;

    const activeTabInput =
      activeInput && inputById.has( activeInput.id ) ? activeInput : null;

    return [ activeTabInput, firstVisibleInput, getActiveViewModeButton() ];
  };

  // 従来表示やCSSフォールバックで可視となるradioの読み上げとTab移動の復元
  const synchronizeRadioAccessibility = ( mode ) => {
    inputs.forEach( ( input ) => {
      const canExposeRadio =
        mode === 'tabs' && navigationInputIds.has( input.id );

      if ( ! canExposeRadio ) {
        input.tabIndex = -1;
        input.setAttribute( 'aria-hidden', 'true' );
      } else {
        input.removeAttribute( 'tabindex' );
        input.removeAttribute( 'aria-hidden' );
      }
    } );
  };

  // 表示中のUIの判定と幅切替時の非表示要素からのフォーカス移譲
  const synchronizeInteractionMode = () => {
    const nextInteractionMode =
      window.getComputedStyle( navigation ).display === 'none'
        ? 'tabs'
        : window.getComputedStyle( mobilePicker ).display !== 'none'
        ? 'mobile'
        : 'desktop';
    const activeElement = document.activeElement;
    const shouldTransferFocus =
      interactionMode !== null &&
      interactionMode !== nextInteractionMode &&
      ( ( interactionMode === 'tabs' && inputs.includes( activeElement ) ) ||
        ( interactionMode === 'desktop' &&
          sidebar.contains( activeElement ) ) ||
        ( interactionMode === 'mobile' &&
          mobilePicker.contains( activeElement ) ) );
    const activeInput = getActiveInput();

    // 従来表示へのfocusイベントに先立つradioの読み上げ状態の復元
    if ( nextInteractionMode === 'tabs' ) {
      synchronizeRadioAccessibility( nextInteractionMode );
    }

    // 現在のUIの非表示化に先立つ、次の可視UIへのフォーカス移譲
    if ( shouldTransferFocus ) {
      focusFirstVisibleCandidate(
        getFocusCandidatesForMode( nextInteractionMode, activeInput )
      );
    }

    if ( nextInteractionMode !== 'tabs' ) {
      synchronizeRadioAccessibility( nextInteractionMode );
    }

    synchronizeNavigationTabStop( activeInput ? activeInput.id : '' );
    interactionMode = nextInteractionMode;
  };

  // radio・パネル・フォーム値を保持したCSSクラスと選択ボタンの更新
  const applyNavigationMode = ( mode ) => {
    navigationMode = supportedModes.includes( mode ) ? mode : tabsMode;
    tabs.classList.toggle(
      'is-navigation-mode-responsive',
      navigationMode === responsiveMode
    );
    tabs.classList.toggle(
      'is-navigation-mode-tabs',
      navigationMode === tabsMode
    );

    viewModeButtons.forEach( ( button ) => {
      const isSelected = button.dataset.navigationMode === navigationMode;
      button.setAttribute( 'aria-checked', String( isSelected ) );
      button.tabIndex = isSelected ? 0 : -1;
    } );

    synchronizeNavigation();
    synchronizeNavigationWidth();
    synchronizeInteractionMode();
  };

  // 表示モード専用AJAXによる保存と、失敗時の現在表示の維持
  const persistNavigationMode = async ( mode ) => {
    isNavigationModeSaved = false;
    isSavingNavigationMode = true;
    viewModeControl.setAttribute( 'aria-busy', 'true' );
    viewModeButtons.forEach( ( button ) => {
      button.setAttribute( 'aria-disabled', 'true' );
    } );
    viewModeStatus.hidden = true;

    let timeoutId = null;
    let didTimeout = false;

    try {
      const abortController = new AbortController();

      // 応答停止時に操作を再開するための30秒の上限
      timeoutId = window.setTimeout( () => {
        didTimeout = true;
        abortController.abort();
      }, 30000 );

      const requestBody = new URLSearchParams( {
        action: 'cocoon_settings_save_navigation_mode',
        nonce: settings.nonce,
        mode,
      } );
      const response = await fetch( settings.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        },
        body: requestBody.toString(),
        signal: abortController.signal,
      } );
      const result = await response.json();
      const responseMode =
        result && result.data && typeof result.data.mode === 'string'
          ? result.data.mode
          : '';

      if ( ! response.ok || ! result || ! result.success || responseMode !== mode ) {
        const saveError = new Error( 'navigation_mode_save_failed' );
        saveError.saveReason = result && result.data && result.data.code;
        throw saveError;
      }
      isNavigationModeSaved = true;
    } catch ( error ) {
      // 読み上げ対象への復帰後に行うエラーメッセージの更新
      viewModeStatus.hidden = false;
      viewModeStatus.textContent = didTimeout
        ? settings.modeTimeoutError || settings.modeSaveError
        : error && error.saveReason === 'invalid_nonce'
        ? settings.modeNonceError || settings.modeSaveError
        : error && [ 'forbidden', 'invalid_user' ].includes( error.saveReason )
        ? settings.modePermissionError || settings.modeSaveError
        : settings.modeSaveError;
    } finally {
      if ( timeoutId !== null ) {
        window.clearTimeout( timeoutId );
      }

      viewModeButtons.forEach( ( button ) => {
        button.removeAttribute( 'aria-disabled' );
      } );
      viewModeControl.removeAttribute( 'aria-busy' );
      isSavingNavigationMode = false;
    }
  };

  viewModeButtons.forEach( ( button ) => {
    button.addEventListener( 'click', () => {
      const requestedMode = button.dataset.navigationMode;

      if (
        ( requestedMode === navigationMode && isNavigationModeSaved ) ||
        isSavingNavigationMode
      ) {
        return;
      }

      if ( requestedMode !== navigationMode ) {
        applyNavigationMode( requestedMode );
      }
      persistNavigationMode( requestedMode );
    } );

    // radioグループの標準操作に合わせ、矢印キーとHome・Endでも1項目だけを選択する。
    button.addEventListener( 'keydown', ( event ) => {
      const currentIndex = viewModeButtons.indexOf( button );
      let nextIndex = currentIndex;

      if ( [ 'ArrowRight', 'ArrowDown' ].includes( event.key ) ) {
        nextIndex = ( currentIndex + 1 ) % viewModeButtons.length;
      } else if ( [ 'ArrowLeft', 'ArrowUp' ].includes( event.key ) ) {
        nextIndex =
          ( currentIndex - 1 + viewModeButtons.length ) %
          viewModeButtons.length;
      } else if ( event.key === 'Home' ) {
        nextIndex = 0;
      } else if ( event.key === 'End' ) {
        nextIndex = viewModeButtons.length - 1;
      } else {
        return;
      }

      event.preventDefault();

      if ( isSavingNavigationMode ) {
        return;
      }

      const nextButton = viewModeButtons[ nextIndex ];
      nextButton.focus();

      if ( nextButton.dataset.navigationMode !== navigationMode ) {
        nextButton.click();
      }
    } );
  } );

  // 初期化完了後だけ既存タブを置き換え、途中エラー時は従来UIへフォールバックする。
  tabs.classList.add( 'is-navigation-enhanced' );
  synchronizeNavigationWidth();
  applyNavigationMode( navigationMode );
  tabs.before( viewMode );

  const synchronizeResponsiveState = () => {
    synchronizeNavigationWidth();
    synchronizeInteractionMode();
  };

  if ( 'ResizeObserver' in window ) {
    const observer = new ResizeObserver( synchronizeResponsiveState );
    observer.observe( tabs );
  } else {
    window.addEventListener( 'resize', synchronizeResponsiveState );
  }

  // 失敗時に元radioへリスナーを残さないよう、初期化成功が確定する最後に登録する。
  inputs.forEach( ( input ) => {
    input.addEventListener( 'change', synchronizeNavigation );
  } );

  return true;
}

// 初期化失敗時も元のタブとアクセシビリティ属性を正確に戻す。
function cocoonRestoreSettingsNavigation( tabs, snapshot ) {
  document
    .querySelectorAll( '.cocoon-settings-view-mode' )
    .forEach( ( element ) => {
      if ( ! snapshot.viewModeElements.has( element ) ) {
        element.remove();
      }
    } );
  tabs
    .querySelectorAll( ':scope > .cocoon-settings-navigation' )
    .forEach( ( element ) => {
      if ( ! snapshot.navigationElements.has( element ) ) {
        element.remove();
      }
    } );

  if ( snapshot.tabsClass === null ) {
    tabs.removeAttribute( 'class' );
  } else {
    tabs.setAttribute( 'class', snapshot.tabsClass );
  }

  snapshot.inputStates.forEach( ( state ) => {
    state.attributes.forEach( ( value, name ) => {
      if ( value === null ) {
        state.input.removeAttribute( name );
      } else {
        state.input.setAttribute( name, value );
      }
    } );
  } );
}

// フッターでは直ちに初期化し、DOMが未完成の場合だけDOMContentLoadedを一度待つ。
function cocoonInitializeSettingsNavigation() {
  const stateAttribute = 'data-cocoon-settings-navigation-state';
  const tabs = document.querySelector(
    '.toplevel_page_theme-settings .wrap.admin-settings #tabs'
  );

  if ( ! tabs ) {
    return false;
  }

  // 可視性判定より先にマスクを外し、同じ同期処理内で完成状態まで構築する。
  tabs.classList.remove( 'is-navigation-booting' );

  if (
    tabs.hasAttribute( stateAttribute ) ||
    tabs.classList.contains( 'is-navigation-enhanced' )
  ) {
    return true;
  }

  const settings = window.cocoonSettingsNavigationData;

  // 設定データがまだない場合は再試行できるよう、初期化状態を確定しない。
  if ( ! settings || ! Array.isArray( settings.groups ) ) {
    return false;
  }

  const inputs = Array.from(
    tabs.querySelectorAll( ':scope > .tab-input[type="radio"]' )
  );
  const panels = Array.from(
    tabs.querySelectorAll( ':scope > .metabox-holder' )
  );

  if ( inputs.length === 0 || panels.length === 0 ) {
    return false;
  }

  const snapshot = {
    inputStates: inputs.map( ( input ) => ( {
      attributes: new Map(
        [ 'class', 'tabindex', 'aria-hidden', 'inert' ].map( ( name ) => [
          name,
          input.getAttribute( name ),
        ] )
      ),
      input,
    } ) ),
    navigationElements: new Set(
      tabs.querySelectorAll( ':scope > .cocoon-settings-navigation' )
    ),
    tabsClass: tabs.getAttribute( 'class' ),
    viewModeElements: new Set(
      document.querySelectorAll( '.cocoon-settings-view-mode' )
    ),
  };

  tabs.setAttribute( stateAttribute, 'initializing' );

  try {
    if ( ! cocoonBuildSettingsNavigation( tabs, settings, inputs, panels ) ) {
      cocoonRestoreSettingsNavigation( tabs, snapshot );
      tabs.setAttribute( stateAttribute, 'fallback' );
      return true;
    }

    tabs.setAttribute( stateAttribute, 'ready' );
  } catch {
    cocoonRestoreSettingsNavigation( tabs, snapshot );
    tabs.setAttribute( stateAttribute, 'failed' );
  }

  return true;
}

if (
  ! cocoonInitializeSettingsNavigation() &&
  document.readyState === 'loading'
) {
  document.addEventListener(
    'DOMContentLoaded',
    cocoonInitializeSettingsNavigation,
    { once: true }
  );
}
