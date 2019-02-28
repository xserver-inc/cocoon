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

//管理者にPWAを有効にする
update_theme_option(OP_PWA_ADMIN_ENABLE);

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

//PWA関連ファイルの管理
manage_cocoon_pwa_files();
