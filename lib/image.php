<?php //画像用の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Lightboxのようなギャラリー系のjQueryプラグインが動作しているか
if ( !function_exists( 'is_lightbox_plugin_exist' ) ):
function is_lightbox_plugin_exist($content){
  //lity
  if ( is_lity_effect_enable() && includes_string( $content, 'data-lity="' ) )
    return true;
  //Lightbox
  if ( is_lightbox_effect_enable() && includes_string( $content, 'data-lightbox="image-set"' ) )
    return true;
  //Spotlight
  if ( is_spotlight_effect_enable() && includes_string( $content, ' class="spotlight"' ) || includes_string( $content, ' class="spotlight ' ) )
    return true;

  return false;
}
endif;


//画像リンクのAタグをLightboxに対応するように付け替え
if ( is_lightbox_effect_enable() ) {
  add_filter( 'the_content', 'add_lightbox_property', 9 );
  add_filter( 'the_category_tag_content', 'add_lightbox_property', 9 );
}
if ( !function_exists( 'add_lightbox_property' ) ):
function add_lightbox_property( $content ) {
  //プレビューやフィードで表示しない
  if( is_feed() )
    return $content;

  //既に適用させているところは処理しない
  if ( is_lightbox_plugin_exist($content) )
    return $content;
  //Aタグを正規表現で置換
  $content = preg_replace(
    '/<a([^>]+?(\.jpe?g|\.png|\.gif)[\'\"][^>]*?)>([\s\w\W\d]+?)<\/a>/i',//Aタグの正規表現
    '<a${1} data-lightbox="image-set">${3}</a>',//置換する
    $content );//投稿本文（置換する文章）

  $content = apply_filters('add_lightbox_property', $content);

  return $content;
}
endif;

//画像リンクのAタグをlityに対応するように付け替え
//http://sorgalla.com/lity/
if ( is_lity_effect_enable() ) {
  add_filter( 'the_content', 'add_lity_property', 11 );
  add_filter( 'the_category_tag_content', 'add_lity_property', 11 );
}
if ( !function_exists( 'add_lity_property' ) ):
function add_lity_property( $content ) {
  //プレビューやフィードで表示しない
  if( is_feed() )
    return $content;

  //既に適用させているところは処理しない
  if ( is_lightbox_plugin_exist($content) )
    return $content;

  //画像用の正規表現
  $img_reg = '\.jpe?g|\.png|\.gif|\.gif';
  //YouTube用の正規表現
  $youtube_reg = '\/\/www\.youtube\.com\/watch\?v=[^"]+';
  //Viemo用の正規表現
  $viemo_reg = '\/\/vimeo\.com\/[^"]+';
  //Googleマップ用の正規表現
  $google_map_reg = '\\/\/[mapsw]+\.google\.[^\/]+?\/maps\?q=[^"]+';
  //Aタグを正規表現で置換
  $content = preg_replace(
    '/<a([^>]+?('.$img_reg.'|'.$youtube_reg.'|'.$viemo_reg.'|'.$google_map_reg.')[\'\"][^>]*?)>([\s\w\W\d]+?)<\/a>/i',//Aタグの正規表現
    '<a${1} data-lity="">${3}</a>',//置換する
    $content );//投稿本文（置換する文章）
  return $content;
}
endif;


//thickboxを呼び出さない
add_action( 'wp_enqueue_scripts', 'deregister_thickbox_files' );
if ( !function_exists( 'deregister_thickbox_files' ) ):
function deregister_thickbox_files() {
  wp_dequeue_style( 'thickbox' );
  wp_dequeue_script( 'thickbox' );
}
endif;

//WordPressサービスを用いてサイトのスクリーンショットの取得
if ( !function_exists( 'get_site_screenshot_url' ) ):
function get_site_screenshot_url($url){
  $mshot = 'https://s0.wordpress.com/mshots/v1/';
  return $mshot.urlencode($url).'?w='.THUMB160WIDTH.'&h='.THUMB160HEIGHT;
}
endif;


//画像リンクのAタグをSpotlightに対応するように付け替え
//https://github.com/nextapps-de/spotlight
if ( is_spotlight_effect_enable() ) {
  add_filter( 'the_content', 'add_spotlight_property', 11 );
  add_filter( 'the_category_tag_content', 'add_spotlight_property', 11 );
}
if ( !function_exists( 'add_spotlight_property' ) ):
function add_spotlight_property( $content ) {
  //プレビューやフィードで表示しない
  if( is_feed() )
    return $content;

  //既に適用させているところは処理しない
  if ( is_lightbox_plugin_exist($content) )
    return $content;

  //画像用の正規表現
  $img_reg = '\.jpe?g|\.png|\.gif|\.gif';
  $res = preg_match_all('/(<a([^>]+?('.$img_reg.')[\'\"][^>]*?)>)([\s\w\W\d]+?)<\/a>/i', $content, $m);
  if ($res && isset($m[1])) {
    //$alls = $m[0];
    $a_starts = $m[1];
    $i = 0;
    $count = 1;
    //class="spotlight"の追加
    foreach ($a_starts as $a_start) {
      if (includes_string($a_start, ' class="')) {
        if (preg_match('/ class="(.+?)"/i', $a_start, $n)) {
        	//class内のクラス名の分解
        	$classes = explode(' ', $n[1]);
        	//spotlightが含まれているか
          if ( !in_array('spotlight', $classes) ) {
            $replaced_a = str_replace(' class="', ' class="spotlight ', $a_start, $count);
            $content = str_replace($a_start, $replaced_a, $content, $count);
          }
        }
      } else {
      	//classがない場合はclass="spotlight"を追加
        $replaced_a = str_replace('<a ', '<a class="spotlight" ', $a_start, $count);
        $content = str_replace($a_start, $replaced_a, $content, $count);
      }
      $i++;
    }
  }
  return $content;
}
endif;

//Lightboxを表示するページかどうか
if ( !function_exists( 'is_lightboxable_page' ) ):
function is_lightboxable_page(){
  return is_singular() || (is_category() && !is_paged()) || (is_tag() && !is_paged());
}
endif;
