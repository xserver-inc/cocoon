<?php //スキンに
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

// _v('skin');
// _v(get_skin_url());
//スキン用のfunctions.phpがある場合
$php_file_path = url_to_local(get_skin_php_url());
if (file_exists($php_file_path)) {
  require_once $php_file_path;
}

global $_THEME_OPTIONS;

//スキン用のoption.csvがある場合
$csv_file_path = url_to_local(get_skin_csv_url());
if (file_exists($csv_file_path)) {
  $csv_file = new SplFileObject($csv_file_path);
  $csv_file->setFlags(SplFileObject::READ_CSV);
  foreach ($csv_file as $line) {
    //終端の空行を除く処理　空行の場合に取れる値は後述
    if(isset($line[0]) && isset($line[1])){
      $name = trim($line[0]);
      $value = trim($line[1]);
      $_THEME_OPTIONS[$name] = $value;
    }
  }
  //_v($_THEME_OPTIONS);
}

//スキン用のoption.jsonがある場合
$json_file_path = url_to_local(get_skin_json_url());
if (file_exists($json_file_path)) {
  $json = wp_filesystem_get_contents($json_file_path, true, false);
  if ($json) {
    $json_options = json_decode($json, true);
    $_THEME_OPTIONS = array_merge($_THEME_OPTIONS, $json_options);
    //_v($_THEME_OPTIONS);
  }
}

// add_action('admin_menu', function (){
//   _v('admin_menu9');
// }, 9);
// add_action( 'wp_loaded', function (){
//   _v('wp_loaded');
// } );

// add_action( 'after_setup_theme', function (){  //スキンが有効な場合
//   _v('after_setup_theme');
//   _v(get_skin_url());
//   _v($_THEME_OPTIONS);
// } );


