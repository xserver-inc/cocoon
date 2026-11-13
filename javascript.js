/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
/////////////////////////////////
// JavaScriptコード
/////////////////////////////////

( function ( $ ) {
  /////////////////////////////////
  //TOPへ戻るボタン
  /////////////////////////////////
  let prevScrollTop = -1;
  const $window = $( window );
  $window.scroll( () => {
    //最上部から現在位置までの距離を取得して、変数[scrollTop]に格納
    const scrollTop = $window.scrollTop();
    const threashold = 600;
    const s1 = prevScrollTop > threashold;
    const s2 = scrollTop > threashold;

    // スレッショルドを跨いだ時のみ処理をする
    if ( s1 ^ s2 ) {
      if ( s2 ) {
        // トップへ戻るボタンが見えている時
        $( '.body' ).addClass( 'go-to-top-visible' );

        // //[.go-to-to]をゆっくりフェードインする
        // $('.go-to-top').fadeIn('slow');
      } else {
        //トップへ戻る間だけ追加したクラスを除去
        $( '.go-to-top-common' ).removeClass( 'go-to-top-up' );
        // トップへ戻るボタンが見えていない時
        $( '.body' ).removeClass( 'go-to-top-visible' );

        // //それ以外だったらフェードアウトする
        // $('.go-to-top').fadeOut('slow');
      }
    }

    prevScrollTop = scrollTop;
  } );

  //ボタン(.go-to-top-common)のクリックイベント
  $( '.go-to-top-common' ).click( function () {
    //トップへ戻る間だけクラスを追加
    $( this ).addClass( 'go-to-top-up' );
    //ページトップへ移動する
    $( 'body,html' ).animate(
      {
        scrollTop: 1,
      },
      800
    );
  } );
  // 目次と見出しの移動後に確保する上側の余白
  const tocScrollGap = 8;
  let tocScrollFrame = 0;
  let tocScrollTimer;
  let tocScrollTarget = null;

  function getTocScrollTarget( hash ) {
    // URL内の日本語などを含むハッシュからの要素IDの復元
    let id;
    try {
      id = decodeURIComponent( hash.slice( 1 ) );
    } catch ( error ) {
      return null;
    }
    const target = document.getElementById( id );
    if ( ! target || ! target.closest( '.entry-content' ) ) {
      return null;
    }
    if ( target.matches( '.toc' ) ) {
      return target;
    }
    // 見出し内の目次アンカーから、背景や枠も含む見出し全体への変換
    const heading = target.closest( 'h1,h2,h3,h4,h5,h6' );
    return heading && ( target === heading || /^toc\d+$/.test( id ) ) ? heading : null;
  }

  function getTocScrollOffset() {
    const adminBar = document.getElementById( 'wpadminbar' );
    const adminOffset = adminBar && getComputedStyle( adminBar ).position === 'fixed'
      ? Math.max( 0, adminBar.getBoundingClientRect().bottom ) : 0;
    let offset = adminOffset;
    const header = document.getElementById( 'header-container' );
    if ( header && header.classList.contains( 'fixed-header' ) ) {
      // 出現アニメーション中の位置によらない、固定完了後のヘッダー下端
      offset = Math.max( offset, adminOffset + header.offsetHeight );
    }
    const mobileHeader = document.querySelector( '.mobile-header-menu-buttons' );
    if ( mobileHeader && getComputedStyle( mobileHeader ).position === 'fixed' ) {
      // 上向きの移動で再表示されるモバイルメニューの高さの先取り
      offset = Math.max( offset, adminOffset + mobileHeader.offsetHeight );
    }
    return offset;
  }

  function stopTocScrollCorrection() {
    tocScrollTarget = null;
    window.clearTimeout( tocScrollTimer );
    window.cancelAnimationFrame( tocScrollFrame );
    tocScrollFrame = 0;
  }

  function queueTocScrollCorrection() {
    if ( ! tocScrollTarget || tocScrollFrame ) {
      return;
    }
    tocScrollFrame = window.requestAnimationFrame( () => {
      tocScrollFrame = 0;
      const target = tocScrollTarget;
      if ( ! target || ! target.isConnected || ! target.getClientRects().length ) {
        return;
      }
      // 固定要素の下に見出し全体を表示するために不足する距離の算出
      const targetTop = target.getBoundingClientRect().top;
      const hiddenHeight = getTocScrollOffset() + tocScrollGap - targetTop;
      // 履歴の位置復元で移動先が画面下へ外れた場合を含む、表示位置の補正
      if ( hiddenHeight > 1 || targetTop >= window.innerHeight ) {
        window.scrollTo( {
          top: Math.max( 0, window.scrollY - hiddenHeight ),
          left: window.scrollX,
          behavior: 'instant',
        } );
      }
    } );
  }

  function correctTocScroll( target ) {
    stopTocScrollCorrection();
    tocScrollTarget = target;
    // 初回の固定化と再読み込み後の位置復元を対象とする、短時間の再補正
    tocScrollTimer = window.setTimeout( stopTocScrollCorrection, 1500 );
    queueTocScrollCorrection();
  }

  $window.on( 'scroll', queueTocScrollCorrection );
  // 手動操作を妨げない、ホイール・タッチ・キー操作開始時の再補正の中止
  [ 'wheel', 'touchstart', 'pointerdown', 'keydown' ].forEach( ( type ) => {
    window.addEventListener( type, stopTocScrollCorrection, { passive: true } );
  } );

  // 通常の目次リンクと同じ余白を使う、モバイルの目次への移動
  $( '.go-to-toc-common' ).on( 'click', ( event ) => {
    const target = document.querySelector( '.entry-content .toc' );
    if ( ! target ) {
      return;
    }
    event.preventDefault();
    const top = window.scrollY + target.getBoundingClientRect().top - getTocScrollOffset() - tocScrollGap;
    const duration = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ? 0 : 800;
    $( document.scrollingElement ).stop( true ).animate(
      { scrollTop: Math.max( 0, top ) }, duration, () => correctTocScroll( target )
    );
  } );

  function correctTocHash() {
    const target = getTocScrollTarget( window.location.hash );
    if ( target ) {
      correctTocScroll( target );
    }
  }
  $window.on( 'hashchange load', correctTocHash );
  $window.on( 'pageshow', ( event ) => {
    // ページ全体の履歴復元位置を維持する、通常表示時だけのハッシュ補正
    if ( ! event.originalEvent.persisted ) {
      correctTocHash();
    }
  } );
  if ( document.readyState === 'complete' ) {
    correctTocHash();
  }

  // 別ページへの移動や修飾キー操作を妨げない、同一ページ内リンクの判定
  document.addEventListener( 'click', ( event ) => {
    const link = event.target.closest( 'a[href]' );
    if ( ! link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey ||
      link.hasAttribute( 'download' ) || ( link.target && link.target !== '_self' ) ) {
      return;
    }
    let url;
    try {
      url = new URL( link.href, window.location.href );
    } catch ( error ) {
      return;
    }
    if ( url.origin !== window.location.origin || url.pathname !== window.location.pathname ||
      url.search !== window.location.search || ! getTocScrollTarget( url.hash ) ) {
      return;
    }
    // 同じハッシュの再クリックを含む、標準のリンク移動後の位置補正
    window.setTimeout( () => {
      if ( ! event.defaultPrevented ) {
        correctTocHash();
      }
    }, 0 );
  } );

  // 検索メニューの開閉に応じた入力欄へのフォーカス移動
  $( '#search-menu-input' ).change( ( e ) => {
    const searchEdit = document.querySelector( '#search-menu-content .search-edit' );
    if ( ! searchEdit ) {
      return;
    }
    if ( e.target.checked ) {
      // 表示途中の入力欄へのフォーカスによる、親画面のスクロール防止
      searchEdit.focus( { preventScroll: true } );
    } else {
      searchEdit.blur();
    }
  } );

  // スキンの表示アニメーション完了後の、未フォーカス入力欄への再移動
  $( '#search-menu-content' ).on( 'transitionend', ( e ) => {
    if ( e.target !== e.currentTarget ||
      ! $( '#search-menu-input' ).prop( 'checked' ) ||
      document.activeElement !== document.body ) {
      return;
    }
    const searchEdit = e.currentTarget.querySelector( '.search-edit' );
    if ( searchEdit ) {
      searchEdit.focus( { preventScroll: true } );
    }
  } );

  //下にスクロールで管理パネルを隠す
  //上にスクロールで管理パネルを表示
  const adminMenu = $( '#admin-panel' );
  const adminHeight = adminMenu.outerHeight();
  let adminStartPos = 0;
  $( window ).scroll( function () {
    const adminCurrentPos = $( this ).scrollTop();
    if ( adminCurrentPos > adminStartPos ) {
      if ( adminCurrentPos >= 200 ) {
        adminMenu.css( 'bottom', '-' + adminHeight + 'px' );
      }
    } else {
      adminMenu.css( 'bottom', 0 );
    }
    adminStartPos = adminCurrentPos;
  } );

  //モバイルボタンが固定じゃない場合
  if ( cocoon_localize_script_options.is_fixed_mobile_buttons_enable != 1 ) {
    //ヘッダーモバイルメニュー
    const headerMenu = $( '.mobile-header-menu-buttons' );
    let headerStartPos = 0;
    $( window ).scroll( function () {
      const headerCurrentPos = $( this ).scrollTop();
      // メニュー名の折り返しで高さが変わるため、隠す直前に都度高さを取得する。
      // box-shadowはouterHeightに含まれないため、影の残り対策として余裕を加える。
      const headerHight = headerMenu.outerHeight() + 5;
      // 画面幅が600px以下の場合は、--wp-admin--admin-bar--heightを考慮しない（WordPressアドミンバーが固定とならないため、ヘッダーメニューの位置を0にする）
      if ( window.innerWidth <= 600 ) {
        if ( headerCurrentPos > headerStartPos ) {
          if ( headerCurrentPos >= 100 ) {
            headerMenu.css( 'top', '-' + headerHight + 'px' );
          }
        } else {
          headerMenu.css( 'top', 0 );
        }
      } else {
        // CSS変数--wp-admin--admin-bar--heightの値を取得
        const adminBarHeight =
          getComputedStyle( document.documentElement )
            .getPropertyValue( '--wp-admin--admin-bar--height' )
            .trim() || '0px';
        if ( headerCurrentPos > headerStartPos ) {
          if ( headerCurrentPos >= 100 ) {
            // 管理バーの高さを考慮してtopを設定
            headerMenu.css(
              'top',
              'calc(' + adminBarHeight + ' - ' + headerHight + 'px)'
            );
          }
        } else {
          // 管理バーの高さを考慮してtopを設定
          headerMenu.css( 'top', 'calc(' + adminBarHeight + ')' );
        }
      }
      headerStartPos = headerCurrentPos;
    } );

    //フッターモバイルメニュー
    const footerMenu = $( '.mobile-footer-menu-buttons' );
    let footerStartPos = 0;
    $( window ).scroll( function () {
      const footerCurrentPos = $( this ).scrollTop();
      // メニュー名の折り返しで高さが変わるため、隠す直前に都度高さを取得する。
      // box-shadowはouterHeightに含まれないため、影の残り対策として余裕を加える。
      const footerHeight = footerMenu.outerHeight() + 5;

      if ( footerCurrentPos > footerStartPos ) {
        if ( footerCurrentPos >= 100 ) {
          footerMenu.css(
            'bottom',
            'calc( -1 * (env(safe-area-inset-bottom) + ' +
              footerHeight +
              'px) )'
          );
        }
      } else if ( footerCurrentPos - footerStartPos < -8 ) {
        // スタイルシート側の安全領域の位置への復帰
        footerMenu.css( 'bottom', '' );
      }

      footerStartPos = footerCurrentPos;
    } );

    const headerButtons = $( '.mobile-header-menu-buttons' );
    const footerButtons = $( '.mobile-footer-menu-buttons' );
    headerButtons.click( () => {
      headerButtons.css( 'z-index', '3' );
      footerButtons.css( 'z-index', '2' );
    } );

    footerButtons.click( () => {
      headerButtons.css( 'z-index', '2' );
      footerButtons.css( 'z-index', '3' );
    } );
  }

  //コメントボタンがクリックされたとき
  const clickEventType = window.ontouchstart !== null ? 'click' : 'touchend';
  $( document ).on(
    clickEventType,
    '#comment-reply-btn, .comment-reply-link',
    () => {
      $( '#comment-reply-btn' ).slideUp();
      const respond = document.getElementById( 'respond' );
      const styles = {
        inset: 'auto',
        position: 'static',
        visibility: 'visible',
      };
      Object.entries( styles ).forEach( ( [ key, value ] ) => {
        respond.style[ key ] = value;
      } );
      $( '#respond' ).slideDown();
    }
  );

  //Google検索ボタン
  $( '.sbtn' ).click( function () {
    const w = $( this ).prev( '.sform' ).text();
    if ( w ) {
      window.open(
        'https://www.google.co.jp/search?q=' + encodeURIComponent( w ),
        '_blank'
      );
    }
  } );

  //スライドインサイドバーのアーカイブセレクトボックス選択処理
  $( '.sidebar-menu-content .widget_archive select' ).change( function () {
    document.location.href = this.options[ this.selectedIndex ].value;
  } );

  //スライドインサイドバーのカテゴリーセレクトボックス選択処理
  $( '.sidebar-menu-content .widget_categories select' ).change( function () {
    if ( this.options[ this.selectedIndex ].value > 0 ) {
      this.parentNode.submit();
    }
  } );

  function drawerCloser( selecter, checkbox ) {
    $( selecter ).click( () => {
      $( checkbox ).prop( 'checked', false );
      // const href = $(this).attr('href');
      // if (href.match(/#/)) {
      //   $(checkbox).prop('checked', false);
      // }
    } );
  }

  //モバイルメニューをクリックしたら閉じる
  drawerCloser( '.menu-drawer .menu-item a', '#navi-menu-input' );

  //モバイルサイドバーをクリックしたら閉じる
  drawerCloser( '#slide-in-sidebar a', '#sidebar-menu-input' );

  //モバイルヘッダーメニューのロゴ処理
  $( '.mobile-menu-buttons' ).each( function () {
    if ( $( this ).has( '.logo-menu-button' ).length ) {
      $( this ).addClass( 'has-logo-button' );
    }
  } );

  $( window ).on( 'load', () => {
    $( '#carousel' ).addClass( 'loaded' );
  } );

  //FAQボックスのアコーディオン化用
  $( '.is-style-accordion > .faq > .faq-answer' ).hide();
  $( '.is-style-accordion > .faq > .faq-question' ).click( function () {
    $( this ).next( '.is-style-accordion .faq-answer' ).slideToggle();
    $( this ).toggleClass( 'active' );
  } );

  //モバイルサイドバー表示時にサイドバーを移動する・モバイルサイドバーを閉じる時に元の位置に戻す
  $( document ).on( 'change', '#sidebar-menu-input', function () {
    if ( $( this ).prop( 'checked' ) ) {
      $( '#sidebar' ).appendTo( '#sidebar-menu-content' );
      $( '#sidebar' ).attr( 'id', 'slide-in-sidebar' );
      $( '#sidebar' ).addClass( 'slide-in-sidebar' );
      //モバイルサイドバーのリンクをクリックしたら閉じる
      drawerCloser( '#slide-in-sidebar a', '#sidebar-menu-input' );
    } else {
      $( '#sidebar' ).removeClass( 'slide-in-sidebar' );
      $( '#slide-in-sidebar' ).attr( 'id', 'sidebar' );
      $( '#sidebar' ).insertAfter( '#main' );
    }
  } );

  //リサイズした時はサイドバーを元に戻す
  let vw = window.innerWidth;
  $( window ).resize( () => {
    if ( vw != window.innerWidth ) {
      $( '#sidebar-menu-input' ).prop( 'checked', false ).change();
    }
    vw = window.innerWidth;
  } );
} )( jQuery );

/*
* Cocoon WordPress Theme incorporates code from "Youtube SpeedLoad" WordPress Plugin, Copyright 2017 Alexufo[http://habrahabr.ru/users/alexufo/]
"Youtube SpeedLoad" WordPress Plugin is distributed under the terms of the GNU GPL v2
*/
( function () {
  const f = document.querySelectorAll( '.video-click' );
  for ( let i = 0; i < f.length; ++i ) {
    f[ i ].onclick = function () {
      const iframe = this.getAttribute( 'data-iframe' );
      this.parentElement.innerHTML = '<div class="video">' + iframe + '</div>';
    };
  }
} )();

// ヘッダーの位置オフセットを更新
( function () {
  function updateHeaderOffset() {
    const header = document.getElementById( 'header-container' );

    let offset = 0;

    if ( header && header.classList.contains( 'fixed-header' ) ) {
      offset = header.offsetHeight;
    }

    // .mobile-header-menu-buttonsのtop・高さを取得して処理
    const mobileHeaderMenuButtons = document.querySelector(
      '.mobile-header-menu-buttons'
    );
    if ( mobileHeaderMenuButtons ) {
      const computedStyle = window.getComputedStyle( mobileHeaderMenuButtons );
      const topValue = parseFloat( computedStyle.top );
      // ボタンの実寸高さ（ラベル折り返し時も実寸）
      const height = mobileHeaderMenuButtons.offsetHeight;

      if ( topValue >= 0 ) {
        // topが0以上（表示中）の場合は、sticky用オフセットに高さを反映
        offset += height;
      }
      // 本文余白用には高さのみを常時設定する。
      // スクロールで隠れている最中（topが負）でも高さは変わらないため、
      // 位置(top)に依存せず余白が安定する。
      document.documentElement.style.setProperty(
        '--cocoon--mobile-header-menu-buttons--height',
        height + 'px'
      );
    }

    document.documentElement.style.setProperty(
      '--cocoon--header-container--position-offset',
      offset + 'px'
    );
  }

  function initHeaderOffset() {
    updateHeaderOffset();

    // モバイルヘッダーボタンのサイズ変化（遅延表示・ラベルの折り返し）を監視する。
    // スクロールに依存しないため、初期表示時に高さが0pxになる問題を解消できる。
    const target = document.querySelector( '.mobile-header-menu-buttons' );
    if ( target && 'ResizeObserver' in window ) {
      new ResizeObserver( updateHeaderOffset ).observe( target );
    }
  }

  if ( document.readyState !== 'loading' ) {
    initHeaderOffset();
  } else {
    document.addEventListener( 'DOMContentLoaded', initHeaderOffset );
  }

  // ブラウザサイズ変更時に更新（リサイズ処理が完了してから実行するため、少し遅延させる）
  let resizeTimer;
  window.addEventListener( 'resize', () => {
    clearTimeout( resizeTimer );
    resizeTimer = setTimeout( () => {
      updateHeaderOffset();
    }, 100 );
  } );

  // スクロール時にも更新（固定ヘッダーの表示切り替えに追従するため）
  window.addEventListener( 'scroll', updateHeaderOffset );
} )();

// スキンごとの検索ボタンの実寸に合わせた、標準消去ボタンの余白調整
( function cocoonSearchFormSpacing() {
  function initializeSearchFormSpacing() {
    // 入力欄と検索ボタンがそろったフォームの抽出
    const forms = Array.from( document.querySelectorAll( '.search-box' ) )
      .map( ( form ) => ( {
        input: form.querySelector( '.search-edit[type="search"]' ),
        button: form.querySelector( '.search-submit' ),
      } ) )
      .filter( ( form ) => form.input && form.button );
    if ( ! forms.length ) {
      return;
    }

    let scheduled = false;
    function updateSpacing() {
      scheduled = false;
      // 読み取りと書き込みの分離による、連続したレイアウト再計算の抑制
      const measurements = forms.map( ( { input, button } ) => {
        const inputRect = input.getBoundingClientRect();
        const buttonRect = button.getBoundingClientRect();
        if ( ! inputRect.width || ! buttonRect.width ) {
          return null;
        }
        const border = parseFloat( getComputedStyle( input ).borderRightWidth ) || 0;
        // ボタンの左端までの距離と、消去領域の右側の4pxの余白
        const padding = Math.max( 0, inputRect.right - buttonRect.left - border + 4 );
        return { input, value: padding.toFixed( 2 ) + 'px' };
      } );
      measurements.forEach( ( measurement ) => {
        if ( measurement && measurement.input.style.getPropertyValue( '--cocoon-search-input-padding' ) !== measurement.value ) {
          measurement.input.style.setProperty( '--cocoon-search-input-padding', measurement.value );
        }
      } );
    }

    // 同じフレーム内に届く表示変更・サイズ変更の計測の集約
    function scheduleSpacing() {
      if ( ! scheduled ) {
        scheduled = true;
        window.requestAnimationFrame( updateSpacing );
      }
    }

    if ( 'ResizeObserver' in window ) {
      const observer = new ResizeObserver( scheduleSpacing );
      forms.forEach( ( { input, button } ) => {
        observer.observe( input );
        observer.observe( button );
      } );
    }
    window.addEventListener( 'resize', scheduleSpacing );
    window.addEventListener( 'load', scheduleSpacing, { once: true } );
    document.addEventListener( 'change', scheduleSpacing );
    scheduleSpacing();
  }

  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', initializeSearchFormSpacing );
  } else {
    initializeSearchFormSpacing();
  }
} )();

// スキップリンクの移動先へのフォーカス付与
( function () {
  /**
   * スキップリンクの移動先要素にフォーカスを移す。
   *
   * <main>などの本来フォーカスできない要素にtabindex="-1"を常時付けておくと、
   * Blink・WebKit系ではその内部をクリック・範囲選択しただけで祖先要素へ
   * フォーカスが移り、Tabキーの移動起点がコンテナ先頭に戻ってしまう。
   * そのため、スキップリンクが押されたときだけ一時的に付与して、
   * フォーカスが外れた時点で取り除く。
   *
   * @param {HTMLElement} target 移動先の要素
   */
  function focusSkipLinkTarget( target ) {
    //既存のtabindexがある場合は元の指定を尊重
    const isTemporary = ! target.hasAttribute( 'tabindex' );

    if ( isTemporary ) {
      target.setAttribute( 'tabindex', '-1' );
    }

    target.focus();

    if ( ! isTemporary ) {
      return;
    }

    //非表示要素などでフォーカスできなかった場合は、blurが発生せず属性が残り続けるため即時除去
    if ( document.activeElement !== target ) {
      target.removeAttribute( 'tabindex' );
      return;
    }

    target.addEventListener( 'blur', function removeTemporaryTabindex() {
      target.removeAttribute( 'tabindex' );
      target.removeEventListener( 'blur', removeTemporaryTabindex );
    } );
  }

  /**
   * ハッシュから移動先要素のIDを取り出す。
   *
   * 日本語などのIDはパーセントエンコードされるためデコードするが、
   * 不正なエンコードでもクリック処理全体が例外で止まらないようにする。
   *
   * @param  {string} hash 先頭に#を含むハッシュ
   * @return {string} 移動先要素のID
   */
  function getSkipLinkTargetId( hash ) {
    const id = hash.slice( 1 );

    try {
      return decodeURIComponent( id );
    } catch ( e ) {
      return id;
    }
  }

  //スキップリンクは描画直後に押される可能性があるため、documentへの委譲で受け取る
  document.addEventListener( 'click', ( event ) => {
    //別タブ・別ウィンドウで開く操作のときは、現在のページのフォーカスと表示位置を動かさない
    if (
      event.button !== 0 ||
      event.metaKey ||
      event.ctrlKey ||
      event.shiftKey ||
      event.altKey
    ) {
      return;
    }

    const element = event.target;
    if ( ! element || typeof element.closest !== 'function' ) {
      return;
    }

    const link = element.closest( '.skip-link' );
    if ( ! link || ! link.hash ) {
      return;
    }

    const target = document.getElementById( getSkipLinkTargetId( link.hash ) );
    if ( target ) {
      focusSkipLinkTarget( target );
    }
  } );
} )();

