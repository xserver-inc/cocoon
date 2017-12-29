<?php //ショートコード

//プロフィールショートコード関数
add_shortcode('author_box', 'author_box_shortcode');
if ( !function_exists( 'author_box_shortcode' ) ):
function author_box_shortcode($atts) {
  extract(shortcode_atts(array(
    'label' => '',
  ), $atts));
  ob_start();
  generate_author_box_tag($label);
  $res = ob_get_clean();
  return $res;
}
endif;

//新着記事ショートコード関数
add_shortcode('new_entries', 'new_entries_shortcode');
if ( !function_exists( 'new_entries_shortcode' ) ):
function new_entries_shortcode($atts) {
  extract(shortcode_atts(array(
    'count' => 5,
    'cats' => array(),
    'type' => 'default',
  ), $atts));
  $categories = array();
  if ($cats) {
    $categories = explode(',', $cats);
  }
  ob_start();
  generate_new_entries_tag($count, $type, $categories);
  $res = ob_get_clean();
  return $res;
}
endif;
