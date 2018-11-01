<?php //スキンから親テーマの定義済み関数をオーバーライドして設定の書き換えが可能
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
  'related_entry_type' => 'vartical_card_3',
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
///////////////////////////////////////////
//ヘッダーロゴを「トップメニューにするコードサンプル
function get_header_layout_type(){
  return 'top_menu';
}

//メインカラム幅を680pxにするサンプル
function get_main_column_contents_width(){
  return 680;
}

//エントリーカードの枠線を表示するサンプル
function is_entry_card_border_visible(){
  return true;
}
*/
