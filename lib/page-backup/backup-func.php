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
  $dir_name = str_replace(get_theme_root_uri().'/', '', get_cocoon_stylesheet_directory_uri());
  $option_name = 'theme_mods_'.$dir_name;
  return $option_name;
}
endif;

if ( !function_exists( 'restore_theme_settings_from_upload' ) ):
function restore_theme_settings_from_upload($file){
  if (!is_user_administrator() || !current_user_can('manage_options')) {
    return false;
  }

  // 公開ディレクトリに移さず、PHPの一時ファイルからだけ読み取る制限
  if (!is_array($file) || !isset($file['error'], $file['size'], $file['tmp_name']) ||
      $file['error'] !== UPLOAD_ERR_OK || !is_string($file['tmp_name']) ||
      !is_numeric($file['size']) || $file['size'] < 1 || $file['size'] > 300000 ||
      !is_uploaded_file($file['tmp_name'])) {
    return false;
  }

  $text = file_get_contents($file['tmp_name'], false, null, 0, 300001);
  if ($text === false || strlen($text) !== (int)$file['size']) {
    return false;
  }

  $mods = parse_theme_settings_backup($text);
  if ($mods === false) {
    return false;
  }

  $option_name = get_theme_mods_option_name();
  // 既存値と同一で更新不要な場合も成功として扱う判定
  return update_option($option_name, $mods) || get_option($option_name) === $mods;
}
endif;

if ( !function_exists( 'parse_theme_settings_backup' ) ):
function parse_theme_settings_backup($text){
  // オブジェクトを作らずにバックアップ配列だけを復元する制限
  $mods = @unserialize($text, array('allowed_classes' => false, 'max_depth' => 64));
  return is_array($mods) && theme_settings_contains_only_values($mods) ? $mods : false;
}
endif;

if ( !function_exists( 'theme_settings_contains_only_values' ) ):
// ネストした値にオブジェクトが混入していないかの再帰確認
function theme_settings_contains_only_values($values, $depth = 0){
  if ($depth > 64) {
    return false;
  }
  foreach ($values as $value) {
    if (is_array($value)) {
      if (!theme_settings_contains_only_values($value, $depth + 1)) {
        return false;
      }
    } elseif (!is_scalar($value) && $value !== null) {
      return false;
    }
  }
  return true;
}
endif;
