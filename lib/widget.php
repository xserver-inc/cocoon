<?php //ウィジェット操作関連の関数

//タグクラウドのカスタマイズ
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
add_filter( 'widget_tag_cloud_args', 'widget_tag_cloud_args_custom' );

//カテゴリウィジェットの投稿数のカッコを取り除く
if ( !function_exists( 'remove_post_count_parentheses' ) ):
function remove_post_count_parentheses( $output ) {
  $output = preg_replace('/<\/a>.*\((\d+)\)/','<span class="post-count">$1</span></a>',$output);
  return $output;
}
endif;
add_filter( 'wp_list_categories', 'remove_post_count_parentheses' );
add_filter( 'get_archives_link',  'remove_post_count_parentheses' );

