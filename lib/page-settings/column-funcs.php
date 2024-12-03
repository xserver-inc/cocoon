<?php //カラム設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// メインカラム
///////////////////////////////////////

//メインカラム幅
define('OP_MAIN_COLUMN_CONTENTS_WIDTH', 'main_column_contents_width');
if ( !function_exists( 'get_main_column_contents_width' ) ):
function get_main_column_contents_width(){
  return get_theme_option(OP_MAIN_COLUMN_CONTENTS_WIDTH, '');
}
endif;

//メインカラム外側余白
define('OP_MAIN_COLUMN_MARGIN', 'main_column_margin');
if ( !function_exists( 'get_main_column_margin' ) ):
function get_main_column_margin(){
  return get_theme_option(OP_MAIN_COLUMN_MARGIN, '');
}
endif;

//メインカラム内側余白
define('OP_MAIN_COLUMN_PADDING', 'main_column_padding');
if ( !function_exists( 'get_main_column_padding' ) ):
function get_main_column_padding(){
  return get_theme_option(OP_MAIN_COLUMN_PADDING, '');
}
endif;

//メインカラム枠線幅
define('OP_MAIN_COLUMN_BORDER_WIDTH', 'main_column_border_width');
if ( !function_exists( 'get_main_column_border_width' ) ):
function get_main_column_border_width(){
  return get_theme_option(OP_MAIN_COLUMN_BORDER_WIDTH, '');
}
endif;

//メインカラム枠線色
define('OP_MAIN_COLUMN_BORDER_COLOR', 'main_column_border_color');
if ( !function_exists( 'get_main_column_border_color' ) ):
function get_main_column_border_color(){
  return get_theme_option(OP_MAIN_COLUMN_BORDER_COLOR, '');
}
endif;

///////////////////////////////////////
// サイドバー
///////////////////////////////////////

//サイドバー幅
define('OP_SIDEBAR_CONTENTS_WIDTH', 'sidebar_contents_width');
if ( !function_exists( 'get_sidebar_contents_width' ) ):
function get_sidebar_contents_width(){
  return get_theme_option(OP_SIDEBAR_CONTENTS_WIDTH, '');
}
endif;

//サイドバー外側余白
define('OP_SIDEBAR_MARGIN', 'sidebar_margin');
if ( !function_exists( 'get_sidebar_margin' ) ):
function get_sidebar_margin(){
  return get_theme_option(OP_SIDEBAR_MARGIN, '');
}
endif;

//サイドバー内側余白
define('OP_SIDEBAR_PADDING', 'sidebar_padding');
if ( !function_exists( 'get_sidebar_padding' ) ):
function get_sidebar_padding(){
  return get_theme_option(OP_SIDEBAR_PADDING, '');
}
endif;

//サイドバー枠線幅
define('OP_SIDEBAR_BORDER_WIDTH', 'sidebar_border_width');
if ( !function_exists( 'get_sidebar_border_width' ) ):
function get_sidebar_border_width(){
  return get_theme_option(OP_SIDEBAR_BORDER_WIDTH, '');
}
endif;

//サイドバー枠線色
define('OP_SIDEBAR_BORDER_COLOR', 'sidebar_border_color');
if ( !function_exists( 'get_sidebar_border_color' ) ):
function get_sidebar_border_color(){
  return get_theme_option(OP_SIDEBAR_BORDER_COLOR, '');
}
endif;

//メインカラムとサイドバーの間隔
define('OP_MAIN_SIDEBAR_MARGIN', 'main_sidebar_margin');
if ( !function_exists( 'get_main_sidebar_margin' ) ):
function get_main_sidebar_margin(){
  return get_theme_option(OP_MAIN_SIDEBAR_MARGIN, '');
}
endif;

//カラムに変更があったか
if ( !function_exists( 'is_clumns_changed' ) ):
function is_clumns_changed(){
  return get_main_column_contents_width() ||
         get_main_column_padding() ||
         get_main_column_border_width() ||
         get_sidebar_contents_width() ||
         get_sidebar_padding() ||
         get_sidebar_border_width() ||
         get_main_sidebar_margin();
}
endif;

if ( !function_exists( 'get_main_column_width' ) ):
function get_main_column_width(){
  $default_width = 800;
  $main_column_contents_width = $default_width;
  $w = get_main_column_contents_width();
  if ($w && (!is_singular_page_type_narrow() || (is_singular_page_type_narrow() && ($w <= $default_width)))) {
    $main_column_contents_width = $w;
  }
  $main_column_padding = get_main_column_padding() ? get_main_column_padding() : 29;
  $main_column_border_width = is_numeric(get_main_column_border_width()) ? get_main_column_border_width() : 1;
  return intval($main_column_contents_width) +
         (intval($main_column_padding) * 2) +
         (intval($main_column_border_width) * 2);
}
endif;

//インデックスページのメインカラムコンテンツ幅
if ( !function_exists( 'get_index_main_column_contents_width' ) ):
function get_index_main_column_contents_width(){
  //サイドバーも表示されているとき
  $mw = intval(get_main_column_contents_width());
  if (empty($mw)) {
    $mw = 800;
  }

  //サイドバーが表示されていないとき
  if (!is_index_page_sidebar_visible()) {
    $sw = get_site_wrap_width();
    $main_column_padding = get_main_column_padding() ? get_main_column_padding() : 29;
    $main_column_border_width = get_main_column_border_width() ? get_main_column_border_width() : 1;
    $mw = $sw - ($main_column_padding*2) - ($main_column_border_width*2);
    //var_dump($mw);
  }
  return $mw;
}
endif;
///////////////////////////////////////
// 縦型カード2列用の可変サムネイル用の関数
///////////////////////////////////////
if ( !function_exists( 'get_vertical_card_2_width' ) ):
function get_vertical_card_2_width(){
  $mw = get_index_main_column_contents_width();
  if (empty($mw)) {
    $mw = 800;
  }
  $padding = 10;
  $border = 0;
  if (is_entry_card_border_visible()) {
    $border = 1;
  }
  $vcw = round($mw * 0.495) - ($padding * 2) - ($border * 2);
  $vcw = get_thumbnail_width($vcw);
  return $vcw;
}
endif;
if ( !function_exists( 'get_vertical_card_2_height' ) ):
function get_vertical_card_2_height(){
  $vcw = get_vertical_card_2_width();
  //$vch = round($vcw * 9/16);
  $vch = get_thumbnail_height($vcw);
  return $vch;
}
endif;
if ( !function_exists( 'get_vertical_card_2_thumbnail_size' ) ):
function get_vertical_card_2_thumbnail_size(){
  return 'thumb'.get_vertical_card_2_width().'x'.get_vertical_card_2_height();
}
endif;

///////////////////////////////////////
// 縦型カード3列用の可変サムネイル用の関数
///////////////////////////////////////
if ( !function_exists( 'get_vertical_card_3_width' ) ):
function get_vertical_card_3_width(){
  $mw = get_index_main_column_contents_width();
  if (empty($mw)) {
    $mw = 800;
  }
  $padding = 7;
  $border = 0;
  if (is_entry_card_border_visible()) {
    $border = 1;
  }
  $vcw = round($mw * 0.33) - ($padding * 2) - ($border * 2);

  $vcw = get_thumbnail_width($vcw);
  return $vcw;
}
endif;
if ( !function_exists( 'get_vertical_card_3_height' ) ):
function get_vertical_card_3_height(){
  $vcw = get_vertical_card_3_width();
  //$vch = round($vcw * 9/16);
  $vch = get_thumbnail_height($vcw);
  return $vch;
}
endif;
if ( !function_exists( 'get_vertical_card_3_thumbnail_size' ) ):
function get_vertical_card_3_thumbnail_size(){
  return 'thumb'.get_vertical_card_3_width().'x'.get_vertical_card_3_height();
}
endif;

///////////////////////////////////////
// タイルカード2列用の可変サムネイル用の関数
///////////////////////////////////////
if ( !function_exists( 'get_tile_card_2_width' ) ):
function get_tile_card_2_width(){
  $mw = get_index_main_column_contents_width();
  if (empty($mw)) {
    $mw = 800;
  }
  $padding = 10;
  $border = 0;
  if (is_entry_card_border_visible()) {
    $border = 1;
  }
  $vcw = round($mw * 0.495) - ($padding * 2) - ($border * 2);
  $vcw = get_thumbnail_width($vcw);
  return $vcw;
}
endif;
if ( !function_exists( 'get_tile_card_2_height' ) ):
function get_tile_card_2_height(){
  return 0;
}
endif;
if ( !function_exists( 'get_tile_card_2_thumbnail_size' ) ):
function get_tile_card_2_thumbnail_size(){
  return 'thumb'.get_tile_card_2_width().'x'.get_tile_card_2_height();
}
endif;

///////////////////////////////////////
// タイルカード3列用の可変サムネイル用の関数
///////////////////////////////////////
if ( !function_exists( 'get_tile_card_3_width' ) ):
function get_tile_card_3_width(){
  $mw = get_index_main_column_contents_width();
  if (empty($mw)) {
    $mw = 800;
  }
  $padding = 7;
  $border = 0;
  if (is_entry_card_border_visible()) {
    $border = 1;
  }
  $vcw = round($mw * 0.33) - ($padding * 2) - ($border * 2);
  $vcw = get_thumbnail_width($vcw);
  return $vcw;
}
endif;
if ( !function_exists( 'get_tile_card_3_height' ) ):
function get_tile_card_3_height(){
  return 0;
}
endif;
if ( !function_exists( 'get_tile_card_3_thumbnail_size' ) ):
function get_tile_card_3_thumbnail_size(){
  return 'thumb'.get_tile_card_3_width().'x'.get_tile_card_3_height();
}
endif;

if ( !function_exists( 'get_sidebar_width' ) ):
function get_sidebar_width(){
  $sidebar_contents_width = get_sidebar_contents_width() ? get_sidebar_contents_width() : 336;
  $sidebar_padding = get_sidebar_padding() ? get_sidebar_padding() : 19;
  $sidebar_border_width = is_numeric(get_sidebar_border_width()) ? get_sidebar_border_width() : 1;
  return intval($sidebar_contents_width) +
         (intval($sidebar_padding) * 2) +
         (intval($sidebar_border_width) * 2);
}
endif;

if ( !function_exists( 'get_site_wrap_width' ) ):
function get_site_wrap_width(){
  $main_sidebar_margin = is_numeric(get_main_sidebar_margin()) ? get_main_sidebar_margin() : 20;
  return get_main_column_width() +
         get_sidebar_width() +
         intval($main_sidebar_margin);
}
endif;
