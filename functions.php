<?php
require_once ABSPATH.'wp-admin/includes/file.php';//WP_Filesystemの使用
require_once 'lib/defins.php'; //定数を定義
require_once 'lib/scripts.php'; //スクリプト関係の関数
require_once 'lib/settings.php'; //Wordpressの設定
require_once 'lib/utils.php'; //Wordpressの設定
require_once 'lib/widget-areas.php'; //ウィジェットエリアの指定


//本文部分の冒頭を綺麗に抜粋する
if ( !function_exists( 'get_content_excerpt' ) ):
function get_content_excerpt($content, $length = 70){
  $content =  preg_replace('/<!--more-->.+/is', '', $content); //moreタグ以降削除
  $content =  strip_shortcodes($content);//ショートコード削除
  $content =  strip_tags($content);//タグの除去
  $content =  str_replace('&nbsp;', '', $content);//特殊文字の削除（今回はスペースのみ）
  $content =  preg_replace('/\[.+?\]/i', '', $content); //ショートコードを取り除く
  $content =  preg_replace(URL_REG, '', $content); //URLを取り除く
  // $content =  preg_replace('/\s/iu',"",$content); //余分な空白を削除
  $over    =  intval(mb_strlen($content)) > intval($length);
  $content =  mb_substr($content, 0, $length);//文字列を指定した長さで切り取る

  return $content;
}
endif;

//タグクラウドのカスタマイズ
if ( !function_exists( 'custom_wp_tag_cloud' ) ):
function custom_wp_tag_cloud($args) {
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
add_filter( 'widget_tag_cloud_args', 'custom_wp_tag_cloud' );

if ( !function_exists( 'remove_cotegory_count_parenthesis' ) ):
function remove_cotegory_count_parenthesis( $output, $args ) {
  $output = preg_replace('/<\/a>\s*\((\d+)\)/',' <span class="category-count">$1</span></a>',$output);
  return $output;
}
endif;
add_filter( 'wp_list_categories', 'remove_cotegory_count_parenthesis', 10, 2 );
