<?php //グローバルナビ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */ ?>
<!-- Navigation -->
<nav id="navi" class="navi cf" itemscope itemtype="https://schema.org/SiteNavigationElement">
  <div id="navi-in" class="navi-in wrap cf">
    <?php //ヘッダーナビ
    wp_nav_menu(
      array (
        //カスタムメニュー名
        'theme_location' => NAV_MENU_HEADER,
        //ul 要素に適用するCSS クラス名
        'menu_class' => 'menu-header',
        //コンテナを表示しない
        'container' => false,
        //カスタムメニューを設定しない際に固定ページでメニューを作成しない
        'fallback_cb' => false,
        //出力されるulに対してidやclassを表示しない
        //'items_wrap' => '<ul>%3$s</ul>',
        //説明出力用
        'walker' => new menu_description_walker()
      )
    ); ?>
    <?php //モバイルヘッダーナビ
    wp_nav_menu(
      array (
        //カスタムメニュー名
        'theme_location' => NAV_MENU_HEADER_MOBILE,
        //ul 要素に適用するCSS クラス名
        'menu_class' => 'menu-mobile',
        //コンテナを表示しない
        'container' => false,
        //カスタムメニューを設定しない際に固定ページでメニューを作成しない
        'fallback_cb' => false,
        //出力されるulに対してidやclassを表示しない
        //'items_wrap' => '<ul>%3$s</ul>',
        //説明出力用
        'walker' => new menu_description_walker()
      )
    ); ?>
  </div><!-- /#navi-in -->
</nav>
<!-- /Navigation -->
