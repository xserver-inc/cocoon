<?php //スタイリング用の追加クラス関数

//メインカラムの追加関数
if ( !function_exists( 'get_additional_main_classes' ) ):
function get_additional_main_classes($option = null){
  $classes = null;
  //サイドバーにウィジェットが入っていない場合
  if (!is_active_sidebar( 'sidebar' )) {
    $classes .= ' no-sidebar';
  }
  return $classes;
  $classes .= ' '.trim($option);
}
endif;

//エントリーカードの追加関数
if ( !function_exists( 'get_additional_new_entriy_cards_classes' ) ):
function get_additional_new_entriy_cards_classes($option = null){
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
  $classes .= ' '.trim($option);
  return $classes;
}
endif;

//SNSシェアボタンの追加関数
if ( !function_exists( 'get_additional_sns_share_button_classes' ) ):
function get_additional_sns_share_button_classes($option = null){
  $classes = null;
  //カラム数
  switch (get_sns_share_column_count()) {
    case 1:
      $classes .= ' ss-col-1';
      break;
    case 2:
      $classes .= ' ss-col-2';
      break;
    case 3:
      $classes .= ' ss-col-3';
      break;
    case 4:
      $classes .= ' ss-col-4';
      break;
    case 5:
      $classes .= ' ss-col-5';
      break;
    default:
      $classes .= ' ss-col-6';
      break;
  }

  //ロゴとキャプションの配置
  switch (get_sns_share_logo_caption_position()) {
    case 'high_and_low_lc':
      $classes .= ' ss-high-and-low-lc';
      break;
    case 'high_and_low_cl':
      $classes .= ' ss-high-and-low-cl';
      break;
    default:

      break;
  }

  //ボタンカラー
  switch (get_sns_share_button_color()) {
    case 'brand_color':
      $classes .= ' ss-brand-color';
      break;
    case 'brand_color_white':
      $classes .= ' ss-brand-color-white';
      break;
    default:

      break;
  }

  $classes .= ' '.trim($option);
  return $classes;
}
endif;
