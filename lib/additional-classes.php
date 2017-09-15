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

//エントリーカードの追加関数
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

//SNSシェアボタンの追加関数
if ( !function_exists( 'get_additional_sns_share_button_classes' ) ):
function get_additional_sns_share_button_classes(){
  $classes = null;
  //カラム数
  switch (get_sns_share_column_count()) {
    case 1:
      $classes .= ' sns-share-col-1';
      break;
    case 2:
      $classes .= ' sns-share-col-2';
      break;
    case 3:
      $classes .= ' sns-share-col-3';
      break;
    case 4:
      $classes .= ' sns-share-col-4';
      break;
    case 5:
      $classes .= ' sns-share-col-5';
      break;
    default:
      $classes .= ' sns-share-col-6';
      break;
  }
  return $classes;
}
endif;
