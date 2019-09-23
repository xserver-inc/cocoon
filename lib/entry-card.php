<?php //エントリーカード関係関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'get_big_card_first_thumbnail_size' ) ):
function get_big_card_first_thumbnail_size($count){
  if ($count == 1 && is_front_page() && !is_paged()) {
    return 'large';
  } else {
    return THUMB320;
  }
}
endif;

if ( !function_exists( 'get_big_card_thumbnail_size' ) ):
function get_big_card_thumbnail_size(){
  return 'large';
}
endif;
//エイリアス（誤った関数だったので。いずれ消す）
if ( !function_exists( 'get_big_card__thumbnail_size' ) ):
function get_big_card__thumbnail_size(){
  return get_big_card_thumbnail_size();
}
endif;

//デフォルトのサムネイルサイズ
if ( !function_exists( 'get_entry_card_default_thumbnail_size' ) ):
function get_entry_card_default_thumbnail_size(){
  return THUMB320;
}
endif;
//エイリアス
if ( !function_exists( 'get_entry_card__thumbnail_size' ) ):
function get_entry_card__thumbnail_size(){
  return get_entry_card_default_thumbnail_size();
}
endif;

//エントリーカードのサムネイルサイズ
if ( !function_exists( 'get_entry_card_thumbnail_size' ) ):
function get_entry_card_thumbnail_size($count){
  $thumbnail_size = null;
  switch (get_entry_card_type()) {
    case 'big_card_first':
      $thumbnail_size = get_big_card_first_thumbnail_size($count);
      // if ($count == 1) {
      //   $thumbnail_size = 'large';
      // } else {
      //   $thumbnail_size = THUMB320;
      // }
      break;
    case 'big_card':
      $thumbnail_size = get_big_card_thumbnail_size();
      // $thumbnail_size = 'large';
      break;
    case 'vertical_card_2':
      $thumbnail_size = get_vertical_card_2_thumbnail_size();
      break;
    case 'vertical_card_3':
      $thumbnail_size = get_vertical_card_3_thumbnail_size();
      break;
    case 'tile_card_2':
      $thumbnail_size = get_tile_card_2_thumbnail_size();
      break;
    case 'tile_card_3':
      $thumbnail_size = get_tile_card_3_thumbnail_size();
      break;
    default://エントリーカード
      $thumbnail_size = get_entry_card_default_thumbnail_size();
      break;
  }
  return apply_filters('get_entry_card_thumbnail_size', $thumbnail_size, $count);
}
endif;

if ( !function_exists( 'get_entry_card_no_image_tag' ) ):
function get_entry_card_no_image_tag($count){
  $thumbnail_tag_320 = '<img src="'.get_no_image_320x180_url().'" alt="" class="entry-card-thumb-image no-image list-no-image" width="'.THUMB320WIDTH.'" height="'.THUMB320HEIGHT.'" />';
  $thumbnail_tag_large = '<img src="'.get_no_image_large_url().'" alt="" class="entry-card-thumb-image no-image list-no-image" />';
  $thumbnail_tag = $thumbnail_tag_large;
  switch (get_entry_card_type()) {
    case 'big_card_first':
      if ($count == 1 && is_front_page() && !is_paged()) {
        $thumbnail_tag = $thumbnail_tag_large;
      } else {
        $thumbnail_tag = $thumbnail_tag_320;
      }
      break;
    case 'big_card':
      $thumbnail_size = $thumbnail_tag_large;
      break;
    default://エントリーカード
      $thumbnail_tag = $thumbnail_tag_320;
      break;
  }
  return $thumbnail_tag;
}
endif;

//新着記事のサムネイルサイズ（エイリアス）
if ( !function_exists( 'get_new_entries_thumbnail_size' ) ):
function get_new_entries_thumbnail_size($type = ET_DEFAULT){
  return get_widget_entries_thumbnail_size($type);
}
endif;
//ウィジェットエントリーのサムネイルサイズ
if ( !function_exists( 'get_widget_entries_thumbnail_size' ) ):
function get_widget_entries_thumbnail_size($type = ET_DEFAULT){
  $thumb_size = is_widget_entry_card_large_image_use($type) ? THUMB320 : THUMB120;
  return apply_filters('get_widget_entries_thumbnail_size', $thumb_size, $type);
}
endif;

//人気記事のサムネイルサイズ
if ( !function_exists( 'get_popular_entries_thumbnail_size' ) ):
function get_popular_entries_thumbnail_size($type = ET_DEFAULT){
  $thumb_size = get_widget_entries_thumbnail_size($type);
  return apply_filters('get_popular_entries_thumbnail_size', $thumb_size, $type);
}
endif;
