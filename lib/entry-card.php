<?php //エントリーカード関係関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//エントリーカードのサムネイルサイズ
if ( !function_exists( 'get_entry_card_thumbnail_size' ) ):
function get_entry_card_thumbnail_size($count){
  $thumbnail_size = null;
  switch (get_entry_card_type()) {
    case 'big_card_first':
      if ($count == 1) {
        $thumbnail_size = 'large';
      } else {
        $thumbnail_size = 'thumb320';
      }
      break;
    case 'big_card':
      $thumbnail_size = 'large';
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
      $thumbnail_size = 'thumb320';
      break;
  }
  return $thumbnail_size;
}
endif;

if ( !function_exists( 'get_entry_card_no_image_tag' ) ):
function get_entry_card_no_image_tag($count){
  $thumbnail_tag_320 = '<img src="'.get_no_image_320x180_url().'" alt="NO IMAGE" class="entry-card-thumb-image no-image list-no-image" width="320" height="180" />';
  $thumbnail_tag_large = '<img src="'.get_no_image_large_url().'" alt="NO IMAGE" class="entry-card-thumb-image no-image list-no-image" />';
  $thumbnail_tag = $thumbnail_tag_large;
  switch (get_entry_card_type()) {
    case 'big_card_first':
      if ($count == 1) {
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

//新着記事のサムネイルサイズ
if ( !function_exists( 'get_new_entries_thumbnail_size' ) ):
function get_new_entries_thumbnail_size($entry_type = ET_DEFAULT){
  $thumb_size = ($entry_type == ET_DEFAULT) ? 'thumb120' : 'thumb320';
  return $thumb_size;
}
endif;

//人気記事のサムネイルサイズ
if ( !function_exists( 'get_popular_entries_thumbnail_size' ) ):
function get_popular_entries_thumbnail_size($entry_type = ET_DEFAULT){
  $thumb_size = ($entry_type == ET_DEFAULT) ? 'thumb120' : 'thumb320';
  return $thumb_size;
}
endif;