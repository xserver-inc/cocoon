/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * グループブロックのリンク化機能
 */
( function () {
  'use strict';

  function initGroupLinks() {
    // リンクグループの取得
    var groups = document.querySelectorAll( '.is-cocoon-group-link' );

    groups.forEach( function ( group ) {
      var url = group.getAttribute( 'data-cocoon-group-link' );
      var newTab =
        group.getAttribute( 'data-cocoon-group-link-target' ) === '_blank';

      if ( ! url ) {
        return;
      }

      var navigate = function () {
        // 多層防御: 危険なプロトコル（javascript:, vbscript:, data:）をブロック
        var sanitized = url
          .replace( /[\u0000-\u001F\u007F-\u009F\s]+/g, '' )
          .toLowerCase();
        if (
          sanitized.indexOf( 'javascript:' ) === 0 ||
          sanitized.indexOf( 'vbscript:' ) === 0 ||
          sanitized.indexOf( 'data:' ) === 0
        ) {
          return;
        }

        if ( newTab ) {
          window.open( url, '_blank', 'noopener,noreferrer' );
        } else {
          window.location.href = url;
        }
      };

      // クリック時の遷移イベント
      group.addEventListener( 'click', function ( e ) {
        // 修飾キー（Ctrl/Cmd/Shift）押下時は新しいタブで開く
        if ( e.ctrlKey || e.metaKey || e.shiftKey ) {
          window.open( url, '_blank', 'noopener,noreferrer' );
          return;
        }

        // ユーザーがテキスト選択中（ドラッグによる選択）の場合は遷移しない
        var selection = window.getSelection();
        if ( selection && selection.toString().length > 0 ) {
          return;
        }

        // イベントの発生元が「自身より内側にある別のリンクターゲット（入れ子になったグループリンクなど）」なら除外
        var clickedGroupLink = e.target.closest( '.is-cocoon-group-link' );
        if ( clickedGroupLink && clickedGroupLink !== group ) {
          return;
        }

        // 内部に配置されたリンクやインタラクティブ要素がクリックされた場合は親の遷移を発火させない
        var interactiveSelectors =
          'a, button, input, textarea, select, details, summary, video, audio, [role="button"]';
        var clickedEl = e.target.closest( interactiveSelectors );
        if ( clickedEl && group.contains( clickedEl ) && clickedEl !== group ) {
          return;
        }

        e.preventDefault();
        navigate();
      } );

      // 【注意】キーボード操作（Enter/Space）のハンドラはこのファイルには含めない。
      // PHP側（lib/block-editor-group-link.php）でインライン onkeydown 属性として出力し、
      // this.click() を通じて上記の click リスナーに委譲する設計。詳細はPHP側のコメントを参照。
    } );
  }

  // DOMContentLoaded後に実行
  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', initGroupLinks );
  } else {
    initGroupLinks();
  }
} )();
