<?php //PWA設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//PWAを有効にする
update_theme_option(OP_PWA_ENABLE);

//PWAアプリ名
update_theme_option(OP_PWA_NAME);

//PWAホーム画面に表示されるアプリ名
update_theme_option(OP_PWA_SHORT_NAME);

//PWAアプリの説明
update_theme_option(OP_PWA_DESCRIPTION);

//PWAテーマカラー
update_theme_option(OP_PWA_THEME_COLOR);

//PWA背景色
update_theme_option(OP_PWA_BACKGROUND_COLOR);

//PWA表示モード
update_theme_option(OP_PWA_DISPLAY);

//PWA画面の向き
update_theme_option(OP_PWA_ORIENTATION);

//PWAが有効な時
if (is_pwa_enable()) {
  $name = addslashes(get_pwa_name());
  $short_name = addslashes(get_pwa_short_name());
  $description = addslashes(get_pwa_description());
  $start_url = home_url().'/?utm_source=homescreen&utm_medium=pwa';
  $display = get_pwa_display();
  $orientation = get_pwa_orientation();
  $theme_color = get_pwa_theme_color();
  $background_color = get_pwa_background_color();
  $icon_url_192 =  get_site_icon_url(192);
  $icon_url_512 =  get_site_icon_url(512);
  $manifest =
  "{
    'name': '{$name}',
    'short_name': '{$short_name}',
    'description': '{description}',
    'start_url': '{$start_url}',
    'display': '{$display}',
    'lang': 'ja',
    'dir': 'auto',
    'orientation': '{$orientation}',
    'theme_color': '{$theme_color}',
    'background_color': '{$background_color}',
    'icons': [
        {
            'src': '{$icon_url_192}',
            'type': 'image/png',
            'sizes': '192x192'
        },
        {
            'src': '{$icon_url_512}',
            'type': 'image/png',
            'sizes': '512x512'
        }
    ]
  }";
  // _v($manifest);
}
