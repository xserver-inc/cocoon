<?php //404ページ設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//スキンURLの取得
update_theme_option(OP_SKIN_URL);

//親フォルダのスキンを含める
update_theme_option(OP_INCLUDE_SKIN_TYPE);
