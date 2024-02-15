<?php //コンテンツ設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// 本文行間
///////////////////////////////////////
//行の高さ

define('OP_ENTRY_CONTENT_LINE_HIGHT_DEFAULT', 1.8);
define('OP_ENTRY_CONTENT_LINE_HIGHT', 'entry_content_line_hight');
if ( !function_exists( 'get_entry_content_line_hight' ) ):
function get_entry_content_line_hight(){
  return get_theme_option(OP_ENTRY_CONTENT_LINE_HIGHT, OP_ENTRY_CONTENT_LINE_HIGHT_DEFAULT);
}
endif;

//行の余白
define('OP_ENTRY_CONTENT_MARGIN_HIGHT_DEFAULT', 1.8);
define('OP_ENTRY_CONTENT_MARGIN_HIGHT', 'entry_content_margin_hight');
if ( !function_exists( 'get_entry_content_margin_hight' ) ):
function get_entry_content_margin_hight(){
  return get_theme_option(OP_ENTRY_CONTENT_MARGIN_HIGHT, OP_ENTRY_CONTENT_MARGIN_HIGHT_DEFAULT);
}
endif;

///////////////////////////////////////
// 外部リンク
///////////////////////////////////////

//外部リンクの開き方
define('OP_EXTERNAL_LINK_OPEN_TYPE', 'external_link_open_type');
if ( !function_exists( 'get_external_link_open_type' ) ):
function get_external_link_open_type(){
  return get_theme_option(OP_EXTERNAL_LINK_OPEN_TYPE, 'default');
}
endif;
if ( !function_exists( 'is_external_link_open_type_default' ) ):
function is_external_link_open_type_default(){
  return get_external_link_open_type() == 'default';
}
endif;
if ( !function_exists( 'is_external_link_open_type_blank' ) ):
function is_external_link_open_type_blank(){
  return get_external_link_open_type() == 'blank';
}
endif;

//外部リンクのフォロータイプ
define('OP_EXTERNAL_LINK_FOLLOW_TYPE', 'external_link_follow_type');
if ( !function_exists( 'get_external_link_follow_type' ) ):
function get_external_link_follow_type(){
  return get_theme_option(OP_EXTERNAL_LINK_FOLLOW_TYPE, 'default');
}
endif;
if ( !function_exists( 'get_external_link_follow_type_default' ) ):
function get_external_link_follow_type_default(){
  return get_external_link_follow_type() == 'default';
}
endif;

//noopener
define('OP_EXTERNAL_LINK_NOOPENER_ENABLE', 'external_link_noopener_enable');
if ( !function_exists( 'is_external_link_noopener_enable' ) ):
function is_external_link_noopener_enable(){
  return get_theme_option(OP_EXTERNAL_LINK_NOOPENER_ENABLE);
}
endif;

//target="_blank"のnoopener
define('OP_EXTERNAL_TARGET_BLANK_LINK_NOOPENER_ENABLE', 'external_target_blank_link_noopener_enable');
if ( !function_exists( 'is_external_target_blank_link_noopener_enable' ) ):
function is_external_target_blank_link_noopener_enable(){
  return get_theme_option(OP_EXTERNAL_TARGET_BLANK_LINK_NOOPENER_ENABLE, 1);
}
endif;

//noreferrer
define('OP_EXTERNAL_LINK_NOREFERRER_ENABLE', 'external_link_noreferrer_enable');
if ( !function_exists( 'is_external_link_noreferrer_enable' ) ):
function is_external_link_noreferrer_enable(){
  return get_theme_option(OP_EXTERNAL_LINK_NOREFERRER_ENABLE);
}
endif;

//target="_blank"のnoreferrer
define('OP_EXTERNAL_TARGET_BLANK_LINK_NOREFERRER_ENABLE', 'external_target_blank_link_noreferrer_enable');
if ( !function_exists( 'is_external_target_blank_link_noreferrer_enable' ) ):
function is_external_target_blank_link_noreferrer_enable(){
  return get_theme_option(OP_EXTERNAL_TARGET_BLANK_LINK_NOREFERRER_ENABLE);
}
endif;

//external
define('OP_EXTERNAL_LINK_EXTERNAL_ENABLE', 'external_link_external_enable');
if ( !function_exists( 'is_external_link_external_enable' ) ):
function is_external_link_external_enable(){
  return get_theme_option(OP_EXTERNAL_LINK_EXTERNAL_ENABLE);
}
endif;

//外部リンクアイコン表示
define('OP_EXTERNAL_LINK_ICON_VISIBLE', 'external_link_icon_visible');
if ( !function_exists( 'is_external_link_icon_visible' ) ):
function is_external_link_icon_visible(){
  return get_theme_option(OP_EXTERNAL_LINK_ICON_VISIBLE);
}

endif;

//外部リンクアイコン
define('OP_EXTERNAL_LINK_ICON', 'external_link_icon');
if ( !function_exists( 'get_external_link_icon' ) ):
function get_external_link_icon(){
  return get_theme_option(OP_EXTERNAL_LINK_ICON, 'fa-external-link');
}
endif;

///////////////////////////////////////
// 内部リンク
///////////////////////////////////////

//内部リンクの開き方
define('OP_INTERNAL_LINK_OPEN_TYPE', 'internal_link_open_type');
if ( !function_exists( 'get_internal_link_open_type' ) ):
function get_internal_link_open_type(){
  return get_theme_option(OP_INTERNAL_LINK_OPEN_TYPE, 'default');
}
endif;
if ( !function_exists( 'is_internal_link_open_type_default' ) ):
function is_internal_link_open_type_default(){
  return get_internal_link_open_type() == 'default';
}
endif;
if ( !function_exists( 'is_internal_link_open_type_blank' ) ):
function is_internal_link_open_type_blank(){
  return get_internal_link_open_type() == 'blank';
}
endif;

//内部リンクのフォロータイプ
define('OP_INTERNAL_LINK_FOLLOW_TYPE', 'internal_link_follow_type');
if ( !function_exists( 'get_internal_link_follow_type' ) ):
function get_internal_link_follow_type(){
  return get_theme_option(OP_INTERNAL_LINK_FOLLOW_TYPE, 'default');
}
endif;
if ( !function_exists( 'get_internal_link_follow_type_default' ) ):
function get_internal_link_follow_type_default(){
  return get_internal_link_follow_type() == 'default';
}
endif;

//noopener
define('OP_INTERNAL_LINK_NOOPENER_ENABLE', 'internal_link_noopener_enable');
if ( !function_exists( 'is_internal_link_noopener_enable' ) ):
function is_internal_link_noopener_enable(){
  return get_theme_option(OP_INTERNAL_LINK_NOOPENER_ENABLE);
}
endif;

//target="_blank"のnoopener
define('OP_INTERNAL_TARGET_BLANK_LINK_NOOPENER_ENABLE', 'internal_target_blank_link_noopener_enable');
if ( !function_exists( 'is_internal_target_blank_link_noopener_enable' ) ):
function is_internal_target_blank_link_noopener_enable(){
  return get_theme_option(OP_INTERNAL_TARGET_BLANK_LINK_NOOPENER_ENABLE);
}
endif;

//noreferrer
define('OP_INTERNAL_LINK_NOREFERRER_ENABLE', 'internal_link_noreferrer_enable');
if ( !function_exists( 'is_internal_link_noreferrer_enable' ) ):
function is_internal_link_noreferrer_enable(){
  return get_theme_option(OP_INTERNAL_LINK_NOREFERRER_ENABLE);
}
endif;

//target="_blank"のnoreferrer
define('OP_INTERNAL_TARGET_BLANK_LINK_NOREFERRER_ENABLE', 'internal_target_blank_link_noreferrer_enable');
if ( !function_exists( 'is_internal_target_blank_link_noreferrer_enable' ) ):
function is_internal_target_blank_link_noreferrer_enable(){
  return get_theme_option(OP_INTERNAL_TARGET_BLANK_LINK_NOREFERRER_ENABLE);
}
endif;

//内部リンクアイコン表示
define('OP_INTERNAL_LINK_ICON_VISIBLE', 'internal_link_icon_visible');
if ( !function_exists( 'is_internal_link_icon_visible' ) ):
function is_internal_link_icon_visible(){
  return get_theme_option(OP_INTERNAL_LINK_ICON_VISIBLE);
}

endif;

//内部リンクアイコン
define('OP_INTERNAL_LINK_ICON', 'internal_link_icon');
if ( !function_exists( 'get_internal_link_icon' ) ):
function get_internal_link_icon(){
  return get_theme_option(OP_INTERNAL_LINK_ICON, 'fa-external-link');
}
endif;

//レスポンシブテーブル
define('OP_RESPONSIVE_TABLE_ENABLE', 'responsive_table_enable');
if ( !function_exists( 'is_responsive_table_enable' ) ):
function is_responsive_table_enable(){
  return get_theme_option(OP_RESPONSIVE_TABLE_ENABLE);
}
endif;

//レスポンシブテーブルの1列目の見出し固定する
define('OP_RESPONSIVE_TABLE_FIRST_COLUMN_STICKY_ENABLE', 'responsive_table_first_column_sticky_enable');
if ( !function_exists( 'is_responsive_table_first_column_sticky_enable' ) ):
function is_responsive_table_first_column_sticky_enable(){
  return get_theme_option(OP_RESPONSIVE_TABLE_FIRST_COLUMN_STICKY_ENABLE);
}
endif;

//横スクロールレスポンシブテーブル用の要素の追加
if (is_responsive_table_enable()) {
  add_filter('the_content', 'add_responsive_table_tag', 11);
  add_filter('the_category_tag_content', 'add_responsive_table_tag', 11);
}
if ( !function_exists( 'add_responsive_table_tag' ) ):
function add_responsive_table_tag($the_content) {
  $first_column_sticky = null;
  if (is_responsive_table_first_column_sticky_enable()) {
    $first_column_sticky = ' stfc-sticky';
  }
  //テーブル対応
  $the_content = preg_replace('/<table/i', '<div class="scrollable-table'.$first_column_sticky.'"><table', $the_content);
  $the_content = preg_replace('/<\/table>/i', '</table></div>', $the_content);
  // //テーブルブロック対応
  // $the_content = str_replace('<figure class="wp-block-table ', '<figure class="wp-block-table scrollable-block-table ', $the_content);
  return $the_content;
}
endif;

//投稿日を表示
define('OP_POST_DATE_VISIBLE', 'post_date_visible');
if ( !function_exists( 'is_post_date_visible' ) ):
function is_post_date_visible(){
  return get_theme_option(OP_POST_DATE_VISIBLE, 1);
}
endif;

//更新日を表示
define('OP_POST_UPDATE_VISIBLE', 'post_update_visible');
if ( !function_exists( 'is_post_update_visible' ) ):
function is_post_update_visible(){
  return get_theme_option(OP_POST_UPDATE_VISIBLE, 1);
}
endif;

//投稿者を表示
define('OP_POST_AUTHOR_VISIBLE', 'post_author_visible');
if ( !function_exists( 'is_post_author_visible' ) ):
function is_post_author_visible(){
  return get_theme_option(OP_POST_AUTHOR_VISIBLE, 1);
}
endif;

//記事を読む時間表示
define('OP_CONTENT_READ_TIME_VISIBLE', 'content_read_time_visible');
if ( !function_exists( 'is_content_read_time_visible' ) ):
function is_content_read_time_visible(){
  return get_theme_option(OP_CONTENT_READ_TIME_VISIBLE);
}
endif;
