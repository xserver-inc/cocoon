<?php //モバイル用のデフォルトボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<ul class="mobile-footer-menu-buttons mobile-menu-buttons">

  <?php //ヘッダー・フッターモバイルボタン双方を表示している場合
  if (is_mobile_button_layout_type_header_and_footer_mobile_buttons()): ?>
    <?php //ホームメニュー
    get_template_part( 'tmp/mobile-home-button' ); ?>

    <?php //シェアメニュー
    get_template_part( 'tmp/mobile-share-button' ); ?>

    <?php //目次メニュー
    get_template_part( 'tmp/mobile-toc-button' ); ?>

    <?php //トップメニュー
    get_template_part( 'tmp/mobile-top-button' ); ?>

    <?php //サイドバーメニュー
    get_template_part( 'tmp/mobile-sidebar-button' ); ?>
  <?php else: ?>
    <?php //ナビメニュー
    get_template_part( 'tmp/mobile-navi-button' ); ?>

    <?php //ホームメニュー
    get_template_part( 'tmp/mobile-home-button' ); ?>

    <?php //検索メニュー
    get_template_part( 'tmp/mobile-search-button' ); ?>

    <?php //トップメニュー
    get_template_part( 'tmp/mobile-top-button' ); ?>

    <?php //サイドバーメニュー
    get_template_part( 'tmp/mobile-sidebar-button' ); ?>
  <?php endif; ?>

</ul>
