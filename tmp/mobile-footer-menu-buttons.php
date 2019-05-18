<?php //モバイル用のグローバルナビメニューボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php if (has_nav_menu( 'navi-footer-mobile' )): ?>
  <?php //ヘッダーナビ
  wp_nav_menu(
    array (
      //カスタムメニュー名
      'theme_location' => 'navi-footer-mobile',
      //ul 要素に適用するCSS クラス名
      'menu_class' => 'menu-drawer',
      //コンテナを表示しない
      'container' => false,
      //カスタムメニューを設定しない際に固定ページでメニューを作成しない
      'fallback_cb' => false,
    )
  );
    ?>
<?php endif ?>
