<?php //スキン設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//スキンIDの取得
define('OP_SKIN_URL', 'skin_url');
if ( !function_exists( 'get_skin_url' ) ):
function get_skin_url(){
  return get_theme_option(OP_SKIN_URL, '');
}
endif;

//スキンのjavascript.jsを取得
if ( !function_exists( 'get_skin_js_url' ) ):
function get_skin_js_url(){
  return str_ireplace('style.css', 'javascript.js', get_skin_url());
}
endif;

//スキンのfunctions.phpを取得
if ( !function_exists( 'get_skin_php_url' ) ):
function get_skin_php_url(){
  return str_ireplace('style.css', 'functions.php', get_skin_url());
}
endif;

//スキンのoption.csvを取得
if ( !function_exists( 'get_skin_csv_url' ) ):
function get_skin_csv_url(){
  return str_ireplace('style.css', 'option.csv', get_skin_url());
}
endif;

//スキンのoption.jsonを取得
if ( !function_exists( 'get_skin_json_url' ) ):
function get_skin_json_url(){
  return str_ireplace('style.css', 'option.json', get_skin_url());
}
endif;

//親フォルダのスキンを含める
define('OP_INCLUDE_SKIN_TYPE', 'include_skin_type');
if ( !function_exists( 'get_include_skin_type' ) ):
function get_include_skin_type(){
  return get_theme_option(OP_INCLUDE_SKIN_TYPE, 'all');
}
endif;

//全てのスキンを有効
if ( !function_exists( 'is_all_skins_enable' ) ):
function is_all_skins_enable(){
  return get_include_skin_type() == 'all';
}
endif;

//親テーマのスキンのみ有効
if ( !function_exists( 'is_parent_skins_only_enable' ) ):
function is_parent_skins_only_enable(){
  return get_include_skin_type() == 'parent_only';
}
endif;

//子テーマのスキンのみ有効
if ( !function_exists( 'is_child_skins_only_enable' ) ):
function is_child_skins_only_enable(){
  return get_include_skin_type() == 'child_only';
}
endif;

//スキンファイルリストの並べ替え用の関数
if ( !function_exists( 'skin_files_comp' ) ):
function skin_files_comp($a, $b) {
  $f1 = (float)$a['priority'];
  $f2 = (float)$b['priority'];
  //優先度（priority）で比較する
  if ($f1 == $f2) {
      return 0;
  }
  return ($f1 < $f2) ? -1 : 1;
}
endif;


//フォルダ以下のファイルをすべて取得
if ( !function_exists( 'get_skin_dirs' ) ):
function get_skin_dirs($dir) {
  $list = array();
  $files = scandir($dir);
  foreach($files as $file){
    if($file == '.' || $file == '..'){
      continue;
    } else if (is_dir($dir . $file)){
      $list[] = $dir . $file;
    }
  }
  return $list;
}
endif;

//var_dump(get_skin_dirs($dir = get_template_directory().'/skins/'));


//スキン情報の取得
if ( !function_exists( 'get_skin_infos' ) ):
function get_skin_infos(){
  if (!defined('FS_METHOD')) {
    define( 'FS_METHOD', 'direct' );
  }

  // $parent = true;
  // // 子テーマで 親skins の取得有無の設定
  // if(function_exists('include_parent_skins')){
  //   $parent = include_parent_skins();
  // }

  $skin_dirs  = array();
  $child_dirs  = array();
  $parent_dirs  = array();

  //子skinsフォルダ内を検索
  $dir = get_stylesheet_directory().'/skins/';
  if(is_child_theme() && file_exists($dir)){
    if (!is_parent_skins_only_enable()) {
      $child_dirs = get_skin_dirs($dir);
    }

  }

  //親skinsフォルダ内を検索
  if ( !is_child_skins_only_enable() || !is_child_theme() ){
    $dir = get_template_directory().'/skins/';
    $parent_dirs = get_skin_dirs($dir);
  }

  //親テーマと子テーマのファイル配列をマージ
  $skin_dirs = array_merge( $child_dirs, $parent_dirs );

  $results = array();
  foreach($skin_dirs as $dir){
    //$dir = str_replace('\\', '/', $dir);
    $style_css_file = $dir.'/style.css';
    //var_dump($style_css_file);

    //スキンフォルダ内にstyle.cssがある場合
    if (file_exists($style_css_file)){

      //CSS内容の取得
      $css = wp_filesystem_get_contents($style_css_file);

      if ( $css ) {
        //Skin Name:の記述があるとき
        if (preg_match('/(Skin )?Name: *(.+)/i', $css, $matches)) {
          $skin_name = trim(strip_tags($matches[2]));
          //優先度（順番）が設定されている場合は順番取得
          if (preg_match('/Priority: *(.+)/i', $css, $m)) {
            $priority = floatval(trim($m[1]));
          } else {
            $priority = 9999999;
          }
          //説明文が設定されている場合
          $description = null;
          if (preg_match('/Description: *(.+)/i', $css, $m)) {
            $description = trim($m[1]);
          }
          //スキンURLが設定されている場合
          $skin_page_uri = null;
          if (preg_match('/Skin (Page )?URI: *(.+)/i', $css, $m)) {
            $skin_page_uri = $m[2];
          }
          //作者が設定されている場合
          $author = null;
          if (preg_match('/Author: *(.+)/i', $css, $m)) {
            $author = trim($m[1]);
          }
          //作者サイトが設定されている場合
          $author_uri = null;
          if (preg_match('/Author URI: *(.+)/i', $css, $m)) {
            $author_uri = trim($m[1]);
          }
          //スキンスクリーンショットが設定されている場合
          $screenshot_uri = null;
          if (preg_match('/Screenshot URI: *(.+)/i', $css, $m)) {
            $screenshot_uri = trim($m[1]);
          }
          //バージョンが設定されている場合
          $version = null;
          if (preg_match('/Version: *(.+)/i', $css, $m)) {
            $version = trim($m[1]);
          }
          //スキンの設定画面表示
          $visibility = true;
          if (preg_match('/Visibility: *(.+)/i', $css, $m)) {
            $visibility = str_to_bool(trim($m[1]));
          }
          //AMP記述がある場合
          $amp = true;
          if (preg_match('/AMP: *(.+)/i', $css, $m)) {
            $amp = str_to_bool(trim($m[1]));
          }


          $file_url = local_to_url($style_css_file);
          $dir_url = local_to_url($dir);
          if (is_child_theme() && strpos($file_url, get_stylesheet_directory_uri()) !== false) {
            $skin_name = '[Child]'.$skin_name;
          }
          //返り値の設定
          $results[] = array(
            'skin_name' => $skin_name,
            'description' => $description,
            'dir_url' => $dir_url,
            'priority' => $priority,
            'file_url' => $file_url,
            'skin_page_uri' => $skin_page_uri,
            'author' => $author,
            'author_uri' => $author_uri,
            'screenshot_uri' => $screenshot_uri,
            'version' => $version,
            'visibility' => $visibility,
          );
        }
      }
    }
  }
  uasort($results, 'skin_files_comp');//スキンを優先度順に並び替え

  return $results;
}
endif;
