<?php //モバイル用のデフォルトボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<ul class="mobile-header-menu-buttons mobile-menu-buttons">

  <?php //ナビメニュー
  get_template_part( 'tmp/mobile-navi-button' ); ?>

  <?php //ロゴメニュー
  get_template_part( 'tmp/mobile-logo-button' ); ?>

  <?php //検索メニュー
  get_template_part( 'tmp/mobile-search-button' ); ?>

</ul>
