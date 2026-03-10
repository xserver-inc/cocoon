/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * ブロックエディター内のクラス付与スクリプト。
 * enqueue_block_assets 経由で読み込まれるため、以下の両環境で動作する:
 *   - WP 5.x〜6.2: 外側の document で実行（エディターが iframe 化されていない）
 *   - WP 6.3〜7.0+: iframe 内の document で実行（エディターが iframe 化されている）
 * いずれの場合も document は正しいコンテキストを指す。
 */
( function () {
  'use strict';

  // PHP から wp_add_inline_script で注入される設定オブジェクト
  // window.cocoonEditorClassConfig = { addClasses: [...], fontClasses: [...] };
  var config = window.cocoonEditorClassConfig;
  if ( ! config ) return;

  var addClasses = config.addClasses || [];
  var fontClasses = config.fontClasses || [];

  /**
   * エディタールート要素にテーマクラスを付与する。
   */
  function applyRootClasses() {
    if ( ! addClasses.length ) return;

    // WP 6.3+ (iframe): .is-root-container が存在する
    // WP 5.x〜6.2 (非 iframe): .block-editor-writing-flow が存在する
    var root =
      document.querySelector( '.is-root-container' ) ||
      document.querySelector( '.block-editor-writing-flow' );

    // 'article' クラスの存在で二重付与を防止する
    if ( root && ! root.classList.contains( 'article' ) ) {
      // 1 つずつ追加する（DOMTokenList の apply 互換性問題を回避）
      for ( var i = 0; i < addClasses.length; i++ ) {
        root.classList.add( addClasses[ i ] );
      }
    }
  }

  /**
   * body 要素にフォント関連クラスを付与する。
   * - iframe 内: iframe の body に付与することで、CSS の親子関係を同一 document 内に構築する
   * - 非 iframe: admin body に付与する（admin_body_class フィルターと重複するが classList.add は冪等）
   */
  function applyFontClasses() {
    if ( ! document.body || ! fontClasses.length ) return;

    for ( var i = 0; i < fontClasses.length; i++ ) {
      if (
        fontClasses[ i ] &&
        ! document.body.classList.contains( fontClasses[ i ] )
      ) {
        document.body.classList.add( fontClasses[ i ] );
      }
    }
  }

  /**
   * グループブロックプレビューに混入する余計な style 属性を除去する。
   */
  function cleanGroupBlockStyles() {
    var groups = document.querySelectorAll(
      '.block-editor-block-preview__content .wp-block-group'
    );
    for ( var i = 0; i < groups.length; i++ ) {
      // style 属性がある場合のみ除去する（不要な DOM 変更による MutationObserver の再発火を防ぐ）
      if ( groups[ i ].hasAttribute( 'style' ) ) {
        groups[ i ].removeAttribute( 'style' );
      }
    }
  }

  /**
   * エディター内のボタンラップリンクのデフォルト動作を無効化する。
   * イベント委譲で対応し、繰り返し登録を回避する。
   */
  var delegated = false;
  function setupLinkPrevention() {
    if ( delegated ) return;
    document.addEventListener( 'click', function ( e ) {
      // closest は IE 未対応だが、WP 5.0+ は IE11 をサポートしており
      // IE11 は closest をサポートしているため問題ない
      var link = e.target.closest( '.btn-wrap-block a' );
      if ( link ) {
        e.preventDefault();
      }
    } );
    delegated = true;
  }

  /**
   * 全処理を実行する。
   */
  function applyAll() {
    applyRootClasses();
    applyFontClasses();
    cleanGroupBlockStyles();
    setupLinkPrevention();
  }

  // --- 初回実行 ---
  // DOMContentLoaded がまだ発火していない場合はイベントを待つ
  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', applyAll );
  } else {
    applyAll();
  }

  // --- MutationObserver で DOM 変更を監視 ---
  // エディターの React レンダリングは非同期のため、
  // DOMContentLoaded 時点でルート要素が存在しない場合がある。
  // MutationObserver は IE11 以降でサポートされている。
  if ( typeof MutationObserver !== 'undefined' ) {
    var observer = new MutationObserver( applyAll );
    observer.observe( document.documentElement, {
      childList: true,
      subtree: true,
      // style 属性の変更も検出する（cleanGroupBlockStyles 用）
      // attributeFilter を使うことで class 等の頻繁な変更は無視しパフォーマンスを維持する
      attributeFilter: [ 'style' ],
    } );
  }
} )();
