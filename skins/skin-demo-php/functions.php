<?php
//$_THEME_OPTIONSグローバル変数に値を追加することで設定値の制御が可能
//スキンから親テーマの定義済み関数をオーバーライドして設定の書き換えが可能
if ( !defined( 'ABSPATH' ) ) exit;

global $_THEME_OPTIONS;
$_THEME_OPTIONS =
array(
  'site_key_color' => '#839b5c',
  'site_key_text_color' => '#fff',
  'header_layout_type' => 'center_logo_slim',
  'tagline_position' => 'header_bottom',
  'site_font_size' => '18px',
  'site_background_color' => '#e0ebaf',
  'site_font_family' => 'meiryo',
  'site_font_weight' => 500,
  'sidebar_position' => 'sidebar_right',
  'site_date_format' => 'Y-m-d',
  'header_area_height' => 200,
  'other_analytics_head_tags' => '<!-- tag -->',
  'appeal_area_display_type' => 'front_page_only',
  'appeal_area_height' => '300',
  'appeal_area_image_url' => 'https://im-cocoon.net/wp-content/uploads/pine-tree.jpeg',
  'appeal_area_background_attachment_fixed' => 1,
  'appeal_area_background_color' => '#839b5c',
  'appeal_area_title' => 'スキンから入力したタイトル',
  'appeal_area_message' => 'スキンから入力したアピールエリアメッセージです。',
  'appeal_area_button_message' => 'スキンボタンキャプション',
  'appeal_area_button_url' => 'https => //wp-cocoon.com/',
  'appeal_area_button_background_color' => '#839b5c',
  'entry_card_type' => 'vertical_card_2',
  'entry_card_border_visible' => 1,
  'category_tag_display_type' => 'one_row',
  'related_entry_type' => 'vertical_card_3',
  'related_entry_count' => 9,
  'post_navi_type' => 'square',
  'single_breadcrumbs_position' => 'main_before',
  'page_breadcrumbs_position' => 'main_before'
);

/*
///////////////////////////////////////////
// 設定操作サンプル
// lib\page-settings\内のXXXXX-funcs.phpに書かれている
// 定義済み関数をオーバーライドして設定を上書きできます。
// 関数をオーバーライドする場合は必ず!function_existsで
// 存在を確認してください。
///////////////////////////////////////////
//ヘッダーロゴを「トップメニューにするコードサンプル
if ( !function_exists( 'get_header_layout_type' ) ):
function get_header_layout_type(){
  return 'top_menu';
}
endif;

//メインカラム幅を680pxにするサンプル
if ( !function_exists( 'get_main_column_contents_width' ) ):
function get_main_column_contents_width(){
  return 680;
}
endif;

//エントリーカードの枠線を表示するサンプル
if ( !function_exists( 'is_entry_card_border_visible' ) ):
function is_entry_card_border_visible(){
  return true;
}
endif;

*/
