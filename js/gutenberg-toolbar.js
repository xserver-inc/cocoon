/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * ブロックエディターのツールバーボタンにクラスを付与する。
 * enqueue_block_editor_assets 経由で読み込まれるため、常に iframe の外側で実行される。
 * WP 5.x〜7.0+ のすべてのバージョンでツールバーは iframe 外に存在する。
 */
( function () {
  'use strict';

  // ボタンテキストとクラスの対応表
  var buttonClassMap = [
    {
      text: 'HTML挿入',
      classes: [ 'html-insert-button', 'cocoon-donation-privilege' ],
    },
    {
      text: 'ページの更新日',
      classes: [ 'shortcode-updated-button', 'cocoon-donation-privilege' ],
    },
  ];

  /**
   * ツールバーボタンにクラスを付与する。
   * React の遅延レンダリングで動的に生成されるボタンに対応するため、
   * MutationObserver で監視する。
   */
  function applyToolbarClasses() {
    // WP バージョンによりツールバーのセレクタが異なるため、複数指定する
    // - .block-editor-writing-flow button: WP 5.x〜6.x
    // - .edit-post-header button: WP 5.x〜6.x（旧ヘッダー）
    // - .editor-header button: WP 6.x+（新ヘッダー）
    // - .block-editor-block-toolbar button: WP 5.x〜7.0+
    var buttons = document.querySelectorAll(
      '.block-editor-writing-flow button, ' +
        '.edit-post-header button, ' +
        '.editor-header button, ' +
        '.block-editor-block-toolbar button'
    );
    for ( var i = 0; i < buttons.length; i++ ) {
      var btn = buttons[ i ];
      for ( var j = 0; j < buttonClassMap.length; j++ ) {
        var entry = buttonClassMap[ j ];
        // textContent でボタンテキストを部分一致検索する
        // （RichTextToolbarButton はバージョンにより DOM 構造が異なるため、
        //  完全一致ではなく indexOf で旧コード jQuery :contains と同じ動作を維持する）
        if ( btn.textContent.indexOf( entry.text ) !== -1 ) {
          // 1 つずつ追加する（DOMTokenList の apply 互換性問題を回避）
          for ( var k = 0; k < entry.classes.length; k++ ) {
            btn.classList.add( entry.classes[ k ] );
          }
        }
      }
    }
  }

  // MutationObserver で監視（MutationObserver は IE11 以降対応）
  if ( typeof MutationObserver !== 'undefined' ) {
    var observer = new MutationObserver( applyToolbarClasses );
    // document.documentElement を使用する（document.body が null の場合への安全策）
    observer.observe( document.documentElement, {
      childList: true,
      subtree: true,
    } );
  }

  // 初回実行
  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', applyToolbarClasses );
  } else {
    applyToolbarClasses();
  }
} )();
