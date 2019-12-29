<?php //ウィジェット操作関連の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//タグクラウドのカスタマイズ
add_filter( 'widget_tag_cloud_args', 'widget_tag_cloud_args_custom' );
if ( !function_exists( 'widget_tag_cloud_args_custom' ) ):
function widget_tag_cloud_args_custom($args) {
  $defaults = array(
    'orderby' => 'count', //使用頻度順
    'order' => 'DESC', // 降順（使用頻度の高い順）
    'number' => 60, // 表示数
  );
  $args = wp_parse_args($args, $defaults);
  //var_dump($args);
  return $args;
}
endif;

//カテゴリウィジェットの投稿数のカッコを取り除く
add_filter( 'wp_list_categories', 'remove_post_count_parentheses', 10, 2 );
add_filter( 'get_archives_link',  'remove_post_count_parentheses', 10, 2 );
if ( !function_exists( 'remove_post_count_parentheses' ) ):
function remove_post_count_parentheses( $output, $var ) {
  //カテゴリの場合（wp_list_categoriesは配列を返す、get_archives_linkはURLを返す）
  if (is_array($var)) {
    $output = str_replace('<a href=','<a class="cf" href=',$output);
  }
  $output = preg_replace('/<\/a>.*\(([0-9,]+)\)/','<span class="post-count">$1</span></a>',$output);
  return $output;
}
endif;

//タグクラウドの出力変更
add_filter( 'wp_tag_cloud', 'wp_tag_cloud_custom', 10, 2);
if ( !function_exists( 'wp_tag_cloud_custom' ) ):
function wp_tag_cloud_custom( $output, $args ) {
  //style属性を取り除く
  $output = preg_replace( '/\s*?style="[^"]+?"/i', '',  $output);
  //タグテキストにspanタグの取り付け
  $output = preg_replace( '/ aria-label="([^"]+?)">/i', ' aria-label="$1"><span class="tag-caption"><span class="fa fa-tag" aria-hidden="true"></span> ',  $output);
  //数字を表示しているとき
  if (isset($args['show_count']) && $args['show_count']) {
    $output = str_replace( '<span class="tag-link-count">', '</span><span class="tag-link-count">',  $output);
    //_v($output);
    //カッコを取り除く
    $output = str_replace( '<span class="tag-link-count"> (', '<span class="tag-link-count">',  $output);
    $output = str_replace( ')</span>', '</span>',  $output);
  } else {//数字を表示しないとき
    $output = str_replace( '</a>', '</span></a>', $output);
  }

  return $output;
}
endif;

if ( !function_exists( 'is_first_char_exclamation' ) ):
function is_first_char_exclamation($title){
  if (strpos($title, '!') === 0) {
    return true;
  }
}
endif;

if ( !function_exists( 'is_widget_title_visible' ) ):
function is_widget_title_visible($title){
  if ($title && !is_first_char_exclamation($title)) {
    return true;
  }
}
endif;

//ウィジェットのタイトルを隠せるように
add_filter('widget_title', 'widget_title_hidable');
if ( !function_exists( 'widget_title_hidable' ) ):
function widget_title_hidable($title){
  //ウィジェットタイトルの最初の一文字が！のとき
  if (is_first_char_exclamation($title)) {
    return null;
  }
  return $title;
}
endif;
