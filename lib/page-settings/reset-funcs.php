<?php //リセット設定に必要な定数や関数
//全ての設定をリセット
define('OP_RESET_ALL_SETTINGS', 'reset_all_settings');
if ( !function_exists( 'reset_all_settings' ) ):
function reset_all_settings(){
  global $wpdb;
  //テーマフォルダ名の取得
  $dir_name = str_replace(get_theme_root_uri().'/', '', get_stylesheet_directory_uri());
  $wpdb->delete( 'wp_options', array( 'option_name' => 'theme_mods_'.$dir_name  ) );
}
endif;

//確認用
define('OP_CONFIRM_RESET_ALL_SETTINGS', 'confirm_reset_all_settings');