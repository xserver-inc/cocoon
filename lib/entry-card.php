<?php //エントリーカード関係関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link https://wp-cocoon.com/
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