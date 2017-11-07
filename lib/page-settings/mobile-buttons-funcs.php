<?php //モバイルボタン設定に必要な定数や関数

//モバイルボタンレイアウト
define('OP_MOBILE_BUTTON_LAYOUT_TYPE', 'mobile_button_layout_type');
if ( !function_exists( 'get_mobile_button_layout_type' ) ):
function get_mobile_button_layout_type(){
  return get_theme_option(OP_MOBILE_BUTTON_LAYOUT_TYPE, 'slidein');
}
endif;
