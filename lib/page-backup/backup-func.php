<?php //バックアップ関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//バックアップが保存されているオプションテーブルの項目名を取得する
if ( !function_exists( 'get_theme_mods_option_name' ) ):
function get_theme_mods_option_name(){
  //テーマフォルダ名の取得
  $dir_name = str_replace(get_theme_root_uri().'/', '', get_stylesheet_directory_uri());
  $option_name = 'theme_mods_'.$dir_name;
  return $option_name;
}
endif;
