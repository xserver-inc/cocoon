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
      var newTab = group.getAttribute( 'data-cocoon-group-link-target' ) === '_blank';

      if ( ! url ) {
        return;
      }

      var navigate = function() {
        if ( newTab ) {
          window.open( url, '_blank', 'noopener,noreferrer' );
        } else {
          window.location.href = url;
        }
      };

      // クリック時の遷移イベント
      group.addEventListener( 'click', function ( e ) {
        // 修飾キー（Ctrl/Cmd/Shift）押下時はブラウザの標準動作を妨げない
        if ( e.ctrlKey || e.metaKey || e.shiftKey ) {
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
        var interactiveSelectors = 'a, button, input, textarea, select, details, summary, video, audio, [role="button"]';
        var clickedEl = e.target.closest( interactiveSelectors );
        if ( clickedEl && group.contains( clickedEl ) && clickedEl !== group ) {
          return;
        }

        e.preventDefault();
        navigate();
      } );
    } );
  }

  // DOMContentLoaded後に実行
  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', initGroupLinks );
  } else {
    initGroupLinks();
  }
} )();
