<?php //スタイリング用の追加クラス関数

//メインカラムの追加関数
if ( !function_exists( 'get_additional_main_classes' ) ):
function get_additional_main_classes(){
  $classes = null;
  //サイドバーにウィジェットが入っていない場合
  if (!is_active_sidebar( 'sidebar' )) {
    $classes .= ' no-sidebar';
  }
  return $classes;
}
endif;

//メインカラムの追加関数
if ( !function_exists( 'get_additional_new_entriy_cards_classes' ) ):
function get_additional_new_entriy_cards_classes(){
  global $g_entry_type;
  $classes = null;
  if ($g_entry_type != ET_DEFAULT) {
    $classes .= ' not-default';
    if ($g_entry_type == ET_LARGE_THUMB) {
      $classes .= ' large-thumb';
    } else if ($g_entry_type == ET_LARGE_THUMB_ON) {
      $classes .= ' large-thumb-on';
    }
  }
  return $classes;
}
endif;