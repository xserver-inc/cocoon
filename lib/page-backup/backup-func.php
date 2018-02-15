<?php //バックアップ関係の関数

//バックアップが保存されているオプションテーブルの項目名を取得する
if ( !function_exists( 'get_theme_mods_option_name' ) ):
function get_theme_mods_option_name(){
  $option_name = 'theme_mods_';
  if (is_child_theme()) {
    $option_name .= THEME_CHILD_DIR;
  } else {
    $option_name .= THEME_PARENT_DIR;
  }
  return $option_name;
}
endif;