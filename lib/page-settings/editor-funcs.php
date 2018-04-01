<?php //その他設定に必要な定数や関数


//タイトル等の文字数カウンター表示
define('OP_ADMIN_EDITOR_COUNTER_VISIBLE', 'admin_editor_counter_visible');
if ( !function_exists( 'is_admin_editor_counter_visible' ) ):
function is_admin_editor_counter_visible(){
  return get_theme_option(OP_ADMIN_EDITOR_COUNTER_VISIBLE, 1);
}
endif;

//ビジュアルエディタースタイル
define('OP_VISUAL_EDITOR_STYLE_ENABLE', 'visual_editor_style_enable');
if ( !function_exists( 'is_visual_editor_style_enable' ) ):
function is_visual_editor_style_enable(){
  return get_theme_option(OP_VISUAL_EDITOR_STYLE_ENABLE, 1);
}
endif;

//ページ公開前に確認アラートを出す
define('OP_CONFIRMATION_BEFORE_PUBLISH', 'confirmation_before_publish');
if ( !function_exists( 'is_confirmation_before_publish' ) ):
function is_confirmation_before_publish(){
  return get_theme_option(OP_CONFIRMATION_BEFORE_PUBLISH, 1);
}
endif;