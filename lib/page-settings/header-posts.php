<?php //ヘッダー設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//ヘッダーの種類
update_theme_option(OP_HEADER_LAYOUT_TYPE);

//ヘッダーの高さ
update_theme_option(OP_HEADER_AREA_HEIGHT);

//モバイルヘッダーの高さ
update_theme_option(OP_MOBILE_HEADER_AREA_HEIGHT);

//サイトロゴ
update_theme_option(OP_THE_SITE_LOGO_URL);

//サイトロゴ幅
update_theme_option(OP_THE_SITE_LOGO_WIDTH);

//サイトロゴ高さ
update_theme_option(OP_THE_SITE_LOGO_HEIGHT);

//キャッチフレーズ位置
update_theme_option(OP_TAGLINE_POSITION);

//ヘッダー背景イメージ
update_theme_option(OP_HEADER_BACKGROUND_IMAGE_URL);

//ヘッダー背景を固定にするか
update_theme_option(OP_HEADER_BACKGROUND_ATTACHMENT_FIXED);

//ヘッダー全体の背景色
update_theme_option(OP_HEADER_CONTAINER_BACKGROUND_COLOR);

//ヘッダー全体の文字色
update_theme_option(OP_HEADER_CONTAINER_TEXT_COLOR);

//ヘッダー背景色
update_theme_option(OP_HEADER_BACKGROUND_COLOR);

//ヘッダーテキスト色
update_theme_option(OP_HEADER_TEXT_COLOR);
