<?php //エントリーカード関係関数

//エントリーカードのサムネイルサイズ
if ( !function_exists( 'get_entry_card_thumbnail_size' ) ):
function get_entry_card_thumbnail_size(){
  $thumbnail_size = null;
  switch (get_entry_card_type()) {
    case 'vertical_card_2':
      $thumbnail_size = get_vartical_card_2_thumbnail_size();
      break;
    case 'vertical_card_3':
      $thumbnail_size = get_vartical_card_3_thumbnail_size();
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